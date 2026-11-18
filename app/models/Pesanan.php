<?php
class Pesanan extends BaseModel {
    protected string $table='tbl_pesanan';
    protected string $primary='id_pesanan';
    private const MEMBER_DISCOUNT_PERCENT = 5.0;
    private const TAX_PERCENT = 11.0;

    public function createForCustomer(int $customerId, array $items): int {
        if(!$items) throw new RuntimeException('Pilih minimal satu produk.');
        $quantities=[];
        foreach($items as $item) {
            $productId=(int)($item['id_produk']??0);
            $quantity=(int)($item['jumlah']??0);
            if($productId<1 || $quantity<1) throw new RuntimeException('Produk dan jumlah pesanan tidak valid.');
            if($quantity>PHP_INT_MAX-($quantities[$productId]??0)) throw new RuntimeException('Jumlah pesanan terlalu besar.');
            $quantities[$productId]=($quantities[$productId]??0)+$quantity;
        }
        ksort($quantities,SORT_NUMERIC);

        $this->db->beginTransaction();
        try {
            $customer=$this->db->prepare("SELECT id_pelanggan FROM tbl_pelanggan WHERE id_pelanggan=? AND username IS NOT NULL");
            $customer->execute([$customerId]);
            if(!$customer->fetchColumn()) throw new RuntimeException('Akun pelanggan tidak ditemukan.');

            $findProduct=$this->db->prepare("SELECT id_produk,nama_produk,harga,stok FROM tbl_produk WHERE id_produk=? FOR UPDATE");
            $products=[];
            foreach($quantities as $productId=>$quantity) {
                $findProduct->execute([$productId]);
                $product=$findProduct->fetch();
                if(!$product) throw new RuntimeException('Salah satu produk tidak ditemukan.');
                if($quantity>(int)$product['stok']) throw new RuntimeException("Stok {$product['nama_produk']} tidak mencukupi.");
                $products[]=[
                    'id_produk'=>(int)$product['id_produk'],
                    'jumlah'=>$quantity,
                    'harga'=>(float)$product['harga'],
                    'subtotal'=>round((float)$product['harga']*$quantity,2),
                ];
            }

            $code='PSN'.date('YmdHis').strtoupper(bin2hex(random_bytes(2)));
            $insertOrder=$this->db->prepare("INSERT INTO tbl_pesanan(kode_pesanan,id_pelanggan,status) VALUES(?,?,'menunggu')");
            $insertOrder->execute([$code,$customerId]);
            $orderId=(int)$this->db->lastInsertId();
            $insertItem=$this->db->prepare("INSERT INTO tbl_detail_pesanan(id_pesanan,id_produk,jumlah,harga,subtotal) VALUES(?,?,?,?,?)");
            foreach($products as $product) {
                $insertItem->execute([$orderId,$product['id_produk'],$product['jumlah'],$product['harga'],$product['subtotal']]);
            }
            $this->db->commit();
            return $orderId;
        } catch(Throwable $e) {
            if($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }

    public function customerOrders(int $customerId): array {
        $s=$this->db->prepare("SELECT o.*,(SELECT COALESCE(SUM(d.subtotal),0) FROM tbl_detail_pesanan d WHERE d.id_pesanan=o.id_pesanan) AS subtotal FROM tbl_pesanan o WHERE o.id_pelanggan=? ORDER BY o.id_pesanan DESC");
        $s->execute([$customerId]);
        return $s->fetchAll();
    }

    public function pendingOrders(): array {
        return $this->db->query("SELECT o.*,p.nama_pelanggan,(SELECT COALESCE(SUM(d.subtotal),0) FROM tbl_detail_pesanan d WHERE d.id_pesanan=o.id_pesanan) AS subtotal FROM tbl_pesanan o JOIN tbl_pelanggan p ON p.id_pelanggan=o.id_pelanggan WHERE o.status='menunggu' ORDER BY o.dibuat_pada ASC")->fetchAll();
    }

    public function customerOrder(int $orderId, int $customerId): ?array {
        $s=$this->db->prepare("SELECT o.*,(SELECT COALESCE(SUM(d.subtotal),0) FROM tbl_detail_pesanan d WHERE d.id_pesanan=o.id_pesanan) AS subtotal FROM tbl_pesanan o WHERE o.id_pesanan=? AND o.id_pelanggan=?");
        $s->execute([$orderId,$customerId]);
        $order=$s->fetch();
        if(!$order) return null;
        $order['items']=$this->orderItems($orderId);
        return $order;
    }

    public function checkoutData(int $orderId): ?array {
        $s=$this->db->prepare("SELECT o.*,p.nama_pelanggan,p.rfid_uid FROM tbl_pesanan o JOIN tbl_pelanggan p ON p.id_pelanggan=o.id_pelanggan WHERE o.id_pesanan=? AND o.status='menunggu'");
        $s->execute([$orderId]);
        $order=$s->fetch();
        if(!$order) return null;
        $order['items']=$this->orderItems($orderId);
        if(!$order['items']) throw new RuntimeException('Pesanan tidak memiliki rincian produk.');
        $order['subtotal']=array_sum(array_column($order['items'],'subtotal'));
        return $order;
    }

    public function previewCheckout(int $orderId, string $uid): array {
        $order=$this->checkoutData($orderId);
        if(!$order) throw new RuntimeException('Pesanan tidak ditemukan atau sudah diproses.');
        $member=$this->memberForOrder($order['id_pelanggan'],$uid);
        $discount=$member?round((float)$order['subtotal']*self::MEMBER_DISCOUNT_PERCENT/100,2):0.0;
        $tax=round(((float)$order['subtotal']-$discount)*self::TAX_PERCENT/100,2);
        return [
            'subtotal'=>(float)$order['subtotal'],
            'discount'=>$discount,
            'discount_percent'=>$member?self::MEMBER_DISCOUNT_PERCENT:0,
            'tax'=>$tax,
            'total'=>round((float)$order['subtotal']-$discount+$tax,2),
            'member'=>$member,
        ];
    }

    public function pay(int $orderId, string $uid, string $method, int $payment): int {
        if(!in_array($method,['Tunai','Transfer','QRIS'],true)) throw new RuntimeException('Metode pembayaran tidak valid.');
        $this->db->beginTransaction();
        try {
            $s=$this->db->prepare("SELECT o.*,p.rfid_uid FROM tbl_pesanan o JOIN tbl_pelanggan p ON p.id_pelanggan=o.id_pelanggan WHERE o.id_pesanan=? AND o.status='menunggu' FOR UPDATE");
            $s->execute([$orderId]);
            $order=$s->fetch();
            if(!$order) throw new RuntimeException('Pesanan tidak ditemukan atau sudah diproses.');

            $items=$this->orderItems($orderId);
            if(!$items) throw new RuntimeException('Pesanan tidak memiliki rincian produk.');
            $findProduct=$this->db->prepare("SELECT id_produk,nama_produk,stok FROM tbl_produk WHERE id_produk=? FOR UPDATE");
            foreach($items as $item) {
                $findProduct->execute([$item['id_produk']]);
                $product=$findProduct->fetch();
                if(!$product) throw new RuntimeException("Produk {$item['nama_produk']} tidak tersedia.");
                if((int)$item['jumlah']>(int)$product['stok']) throw new RuntimeException("Stok {$product['nama_produk']} tidak mencukupi.");
            }

            $member=$this->memberForOrder((int)$order['id_pelanggan'],$uid);
            $subtotal=array_sum(array_column($items,'subtotal'));
            $discount=$member?round($subtotal*self::MEMBER_DISCOUNT_PERCENT/100,2):0.0;
            $tax=round(($subtotal-$discount)*self::TAX_PERCENT/100,2);
            $total=round($subtotal-$discount+$tax,2);
            if($payment<$total) throw new RuntimeException('Pembayaran kurang. Total setelah diskon dan pajak: Rp '.number_format($total,0,',','.').'.');
            $change=round($payment-$total,2);
            $code='TRX'.date('YmdHis').strtoupper(bin2hex(random_bytes(2)));

            $insertTransaction=$this->db->prepare("INSERT INTO tbl_transaksi(kode_transaksi,tanggal,id_pelanggan,total_belanja,diskon,pajak,uang_dibayar,kembalian,metode_pembayaran,id_pesanan,diskon_member_persen) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
            $insertTransaction->execute([
                $code,date('Y-m-d'),$order['id_pelanggan'],$total,$discount,$tax,$payment,$change,$method,$orderId,
                $member?self::MEMBER_DISCOUNT_PERCENT:0,
            ]);
            $transactionId=(int)$this->db->lastInsertId();

            $decrement=$this->db->prepare("UPDATE tbl_produk SET stok=stok-? WHERE id_produk=? AND stok>=?");
            $insertDetail=$this->db->prepare("INSERT INTO tbl_detail_transaksi(id_transaksi,id_produk,jumlah,harga,subtotal) VALUES(?,?,?,?,?)");
            foreach($items as $item) {
                $decrement->execute([$item['jumlah'],$item['id_produk'],$item['jumlah']]);
                if($decrement->rowCount()!==1) throw new RuntimeException("Stok {$item['nama_produk']} berubah. Silakan coba kembali.");
                $insertDetail->execute([$transactionId,$item['id_produk'],$item['jumlah'],$item['harga'],$item['subtotal']]);
            }

            $updateOrder=$this->db->prepare("UPDATE tbl_pesanan SET status='dibayar',dibayar_pada=CURRENT_TIMESTAMP WHERE id_pesanan=? AND status='menunggu'");
            $updateOrder->execute([$orderId]);
            if($updateOrder->rowCount()!==1) throw new RuntimeException('Pesanan sudah diproses oleh kasir lain.');
            $this->db->commit();
            return $transactionId;
        } catch(Throwable $e) {
            if($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }

    private function orderItems(int $orderId): array {
        $s=$this->db->prepare("SELECT d.id_produk,d.jumlah,d.harga,d.subtotal,p.nama_produk FROM tbl_detail_pesanan d JOIN tbl_produk p ON p.id_produk=d.id_produk WHERE d.id_pesanan=? ORDER BY d.id_detail_pesanan");
        $s->execute([$orderId]);
        return $s->fetchAll();
    }

    private function memberForOrder(int $customerId, string $uid): bool {
        $uid=strtoupper(trim($uid));
        if($uid==='') return false;
        if(preg_match('/^[A-Z0-9:-]{4,64}$/D',$uid)!==1) throw new RuntimeException('UID kartu RFID tidak valid.');
        $s=$this->db->prepare("SELECT id_pelanggan FROM tbl_pelanggan WHERE rfid_uid=?");
        $s->execute([$uid]);
        $memberId=$s->fetchColumn();
        if(!$memberId) throw new RuntimeException('Kartu RFID belum terdaftar.');
        if((int)$memberId!==$customerId) throw new RuntimeException('Kartu member bukan milik pelanggan pada pesanan ini.');
        return true;
    }
}
