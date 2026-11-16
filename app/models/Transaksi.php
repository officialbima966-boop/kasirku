<?php
class Transaksi extends BaseModel {
    protected string $table='tbl_transaksi'; protected string $primary='id_transaksi';
    public function all(): array {
        return $this->db->query("SELECT t.*,p.nama_pelanggan FROM tbl_transaksi t JOIN tbl_pelanggan p ON p.id_pelanggan=t.id_pelanggan ORDER BY t.id_transaksi DESC")->fetchAll();
    }
    public function create(array $header, array $items, float $discount, int $payment): int {
        if (!$items) {
            throw new RuntimeException('Tambahkan minimal satu produk.');
        }

        $this->db->beginTransaction();
        try {
            $customer = $this->db->prepare("SELECT id_pelanggan FROM tbl_pelanggan WHERE id_pelanggan=?");
            $customer->execute([$header['id_pelanggan']]);
            if (!$customer->fetchColumn()) {
                throw new RuntimeException('Pelanggan tidak ditemukan.');
            }

            $quantities = [];
            foreach ($items as $item) {
                $productId = (int)($item['id_produk'] ?? 0);
                $quantity = (int)($item['jumlah'] ?? 0);
                if ($productId < 1 || $quantity < 1) {
                    throw new RuntimeException('Produk dan jumlah pembelian harus valid.');
                }
                if ($quantity > PHP_INT_MAX - ($quantities[$productId] ?? 0)) {
                    throw new RuntimeException('Jumlah pembelian terlalu besar.');
                }
                $quantities[$productId] = ($quantities[$productId] ?? 0) + $quantity;
            }
            ksort($quantities, SORT_NUMERIC);

            $lockProduct = $this->db->prepare("SELECT id_produk,nama_produk,harga,stok FROM tbl_produk WHERE id_produk=? FOR UPDATE");
            $products = [];
            $subtotal = 0.0;
            foreach ($quantities as $productId => $quantity) {
                $lockProduct->execute([$productId]);
                $product = $lockProduct->fetch();
                if (!$product) {
                    throw new RuntimeException('Salah satu produk tidak ditemukan.');
                }
                if ($quantity > (int)$product['stok']) {
                    throw new RuntimeException("Stok {$product['nama_produk']} tidak mencukupi. Sisa stok: {$product['stok']}.");
                }
                $lineSubtotal = round((float)$product['harga'] * $quantity, 2);
                $subtotal = round($subtotal + $lineSubtotal, 2);
                $products[] = [
                    'id_produk' => (int)$product['id_produk'],
                    'nama_produk' => $product['nama_produk'],
                    'jumlah' => $quantity,
                    'harga' => (float)$product['harga'],
                    'subtotal' => $lineSubtotal,
                ];
            }

            if (!is_finite($discount) || $discount < 0 || $discount > $subtotal) {
                throw new RuntimeException('Diskon harus bernilai nol atau lebih dan tidak boleh melebihi subtotal.');
            }

            $tax = round(($subtotal - $discount) * 0.11, 2);
            $total = round($subtotal - $discount + $tax, 2);
            if ($payment < $total) {
                throw new RuntimeException('Uang dibayar kurang dari total pembayaran.');
            }
            $change = round($payment - $total, 2);
            $insertTransaction = $this->db->prepare("INSERT INTO tbl_transaksi(kode_transaksi,tanggal,id_pelanggan,total_belanja,diskon,pajak,uang_dibayar,kembalian) VALUES(?,?,?,?,?,?,?,?)");
            $insertTransaction->execute([
                $header['kode'],
                $header['tanggal'],
                $header['id_pelanggan'],
                $total,
                $discount,
                $tax,
                $payment,
                $change,
            ]);
            $transactionId = (int)$this->db->lastInsertId();

            $decrementStock = $this->db->prepare("UPDATE tbl_produk SET stok=stok-? WHERE id_produk=? AND stok>=?");
            $insertDetail = $this->db->prepare("INSERT INTO tbl_detail_transaksi(id_transaksi,id_produk,jumlah,harga,subtotal) VALUES(?,?,?,?,?)");
            foreach ($products as $product) {
                $decrementStock->execute([$product['jumlah'], $product['id_produk'], $product['jumlah']]);
                if ($decrementStock->rowCount() !== 1) {
                    throw new RuntimeException("Stok {$product['nama_produk']} berubah. Silakan coba transaksi kembali.");
                }
                $insertDetail->execute([
                    $transactionId,
                    $product['id_produk'],
                    $product['jumlah'],
                    $product['harga'],
                    $product['subtotal'],
                ]);
            }

            $this->db->commit(); return $transactionId;
        } catch(Throwable $e){
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }
    public function detail(int $id): array {
        $h=$this->db->prepare("SELECT t.*,p.nama_pelanggan,p.kota,p.no_hp,p.alamat FROM tbl_transaksi t JOIN tbl_pelanggan p ON p.id_pelanggan=t.id_pelanggan WHERE t.id_transaksi=?");
        $h->execute([$id]); $header=$h->fetch();
        if(!$header) return [];
        $d=$this->db->prepare("SELECT d.*,p.nama_produk FROM tbl_detail_transaksi d JOIN tbl_produk p ON p.id_produk=d.id_produk WHERE d.id_transaksi=?");
        $d->execute([$id]); return ['header'=>$header,'items'=>$d->fetchAll()];
    }
}
