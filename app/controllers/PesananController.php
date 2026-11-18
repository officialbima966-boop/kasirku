<?php
class PesananController {
    public function member(): void {
        $customerId=(int)current_user()['id'];
        $db=Database::connect();
        $customerQuery=$db->prepare("SELECT id_pelanggan,nama_pelanggan,username,rfid_uid FROM tbl_pelanggan WHERE id_pelanggan=?");
        $customerQuery->execute([$customerId]);
        $customer=$customerQuery->fetch();
        if(!$customer) { http_response_code(404); exit('Akun pelanggan tidak ditemukan.'); }
        $products=$db->query("SELECT id_produk,nama_produk,harga,stok,foto FROM tbl_produk WHERE stok>0 ORDER BY nama_produk")->fetchAll();
        $orders=(new Pesanan())->customerOrders($customerId);
        $title='Belanja Member';
        require APP_PATH.'/views/member/index.php';
    }

    public function memberOrders(): void {
        $orders=(new Pesanan())->customerOrders((int)current_user()['id']);
        $title='Pesanan Saya';
        require APP_PATH.'/views/member/orders.php';
    }

    public function createOrder(): void {
        if($_SERVER['REQUEST_METHOD']!=='POST') redirect(url('member'));
        $ids=$_POST['produk']??[];
        $quantities=$_POST['jumlah']??[];
        if(!is_array($ids) || !is_array($quantities)) {
            flash('error','Data produk pesanan tidak valid.');
            redirect(url('member'));
        }
        $items=[];
        foreach($ids as $rawId) {
            $id=is_scalar($rawId)?filter_var($rawId,FILTER_VALIDATE_INT):false;
            $rawQuantity=$id!==false?($quantities[(string)$id]??null):null;
            $quantity=is_scalar($rawQuantity)?filter_var($rawQuantity,FILTER_VALIDATE_INT):false;
            if($id===false || $id<1 || $quantity===false || $quantity<1) {
                flash('error','Pilih produk dan masukkan jumlah yang valid.');
                redirect(url('member'));
            }
            $items[]=['id_produk'=>$id,'jumlah'=>$quantity];
        }
        try {
            $orderId=(new Pesanan())->createForCustomer((int)current_user()['id'],$items);
            flash('success','Pesanan berhasil dikirim ke kasir. Stok akan diperiksa kembali saat pembayaran.');
            redirect(url('member_pesanan',['id'=>$orderId]));
        } catch(RuntimeException $e) {
            flash('error',$e->getMessage());
            redirect(url('member'));
        } catch(Throwable $e) {
            error_log('Gagal membuat pesanan pelanggan: '.$e->getMessage());
            flash('error','Pesanan gagal dibuat. Silakan coba kembali.');
            redirect(url('member'));
        }
    }

    public function linkCard(): void {
        if($_SERVER['REQUEST_METHOD']!=='POST') redirect(url('member'));
        $uid=$_POST['rfid_uid']??null;
        if(!is_string($uid)) {
            flash('error','UID kartu RFID tidak valid.');
            redirect(url('member'));
        }
        try {
            (new Pelanggan())->linkRfid((int)current_user()['id'],$uid);
            flash('success','Kartu member berhasil dihubungkan ke akun Anda.');
        } catch(RuntimeException $e) {
            flash('error',$e->getMessage());
        } catch(Throwable $e) {
            error_log('Gagal menghubungkan kartu member: '.$e->getMessage());
            flash('error','Kartu member gagal disimpan. Silakan coba kembali.');
        }
        redirect(url('member'));
    }

    public function cashierIndex(): void {
        $orders=(new Pesanan())->pendingOrders();
        $title='Pesanan Menunggu Pembayaran';
        require APP_PATH.'/views/kasir/orders.php';
    }

    public function cashierCheckout(): void {
        $orderId=filter_var($_GET['id']??null,FILTER_VALIDATE_INT);
        if($orderId===false || $orderId<1) { http_response_code(404); exit('Pesanan tidak ditemukan.'); }
        $order=(new Pesanan())->checkoutData($orderId);
        if(!$order) { http_response_code(404); exit('Pesanan tidak ditemukan atau sudah diproses.'); }
        $preview=(new Pesanan())->previewCheckout($orderId,'');
        $title='Proses Pembayaran Pesanan';
        require APP_PATH.'/views/kasir/checkout.php';
    }

    public function cashierPreview(): void {
        header('Content-Type: application/json; charset=utf-8');
        $orderId=filter_var($_GET['id']??null,FILTER_VALIDATE_INT);
        $uid=$_GET['rfid']??'';
        if($orderId===false || $orderId<1 || !is_string($uid)) {
            http_response_code(400);
            echo json_encode(['error'=>'Data pesanan atau kartu RFID tidak valid.'],JSON_UNESCAPED_UNICODE);
            return;
        }
        try {
            echo json_encode((new Pesanan())->previewCheckout($orderId,$uid),JSON_UNESCAPED_UNICODE);
        } catch(RuntimeException $e) {
            http_response_code(422);
            echo json_encode(['error'=>$e->getMessage()],JSON_UNESCAPED_UNICODE);
        } catch(Throwable $e) {
            error_log('Gagal menghitung diskon member: '.$e->getMessage());
            http_response_code(500);
            echo json_encode(['error'=>'Total pesanan gagal dihitung. Silakan muat ulang halaman.'],JSON_UNESCAPED_UNICODE);
        }
    }

    public function cashierPay(): void {
        if($_SERVER['REQUEST_METHOD']!=='POST') redirect(url('kasir_pesanan'));
        $orderId=filter_var($_POST['id_pesanan']??null,FILTER_VALIDATE_INT);
        $payment=filter_var($_POST['uang_dibayar']??null,FILTER_VALIDATE_INT);
        $method=$_POST['metode_pembayaran']??null;
        $uid=$_POST['rfid_uid']??'';
        if($orderId===false || $orderId<1 || $payment===false || $payment<0 || !is_string($method) || !is_string($uid)) {
            flash('error','Data pembayaran tidak valid.');
            redirect(url('kasir_pesanan'));
        }
        try {
            $transactionId=(new Pesanan())->pay($orderId,strtoupper(trim($uid)),$method,$payment);
            flash('success','Pembayaran berhasil dan stok produk telah diperbarui.');
            redirect(url('transaksi_detail',['id'=>$transactionId]));
        } catch(RuntimeException $e) {
            flash('error',$e->getMessage());
            redirect(url('kasir_bayar',['id'=>$orderId]));
        } catch(Throwable $e) {
            error_log('Gagal memproses pembayaran pesanan: '.$e->getMessage());
            flash('error','Pembayaran gagal. Silakan coba kembali.');
            redirect(url('kasir_bayar',['id'=>$orderId]));
        }
    }
}
