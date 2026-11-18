<?php
class TransaksiController {
    public function index(): void {
        $items=(new Transaksi())->all();$title='Transaksi';require APP_PATH.'/views/transaksi/index.php';
    }
    public function create(): void {
        $db=Database::connect();$pelanggan=$db->query("SELECT * FROM tbl_pelanggan ORDER BY nama_pelanggan")->fetchAll();
        $produk=$db->query("SELECT * FROM tbl_produk WHERE stok>0 ORDER BY nama_produk")->fetchAll();
        $title='Buat Transaksi';require APP_PATH.'/views/transaksi/create.php';
    }
    public function store(): void {
        if($_SERVER['REQUEST_METHOD']!=='POST') redirect(url('transaksi'));
        $ids=$_POST['produk']??[];$qty=$_POST['jumlah']??[];
        if(!is_array($ids) || !is_array($qty)) {
            flash('error','Data produk transaksi tidak valid.');redirect(url('transaksi_tambah'));
        }
        $items=[];
        foreach($ids as $i=>$id){
            $quantity=$qty[$i]??null;
            $productId=is_scalar($id)?filter_var($id,FILTER_VALIDATE_INT):false;
            $validQuantity=is_scalar($quantity)?filter_var($quantity,FILTER_VALIDATE_INT):false;
            if($productId===false || $validQuantity===false || $productId<1 || $validQuantity<1) {
                flash('error','Produk dan jumlah pembelian harus valid.');redirect(url('transaksi_tambah'));
            }
            $items[]=['id_produk'=>$productId,'jumlah'=>$validQuantity];
        }
        $customerId=$_POST['id_pelanggan']??null;
        $discount=$_POST['diskon']??0;
        $payment=$_POST['uang_dibayar']??null;
        $paymentMethod=$_POST['metode_pembayaran']??null;
        $validCustomerId=is_scalar($customerId)?filter_var($customerId,FILTER_VALIDATE_INT):false;
        $validPayment=is_scalar($payment)?filter_var($payment,FILTER_VALIDATE_INT):false;
        if($validCustomerId===false || $validCustomerId<1 || !is_scalar($discount) || !is_numeric($discount)) {
            flash('error','Pelanggan atau diskon tidak valid.');redirect(url('transaksi_tambah'));
        }
        if(!is_string($paymentMethod) || !in_array($paymentMethod,['Tunai','Transfer','QRIS'],true)) {
            flash('error','Metode pembayaran tidak valid.');redirect(url('transaksi_tambah'));
        }
        if($validPayment===false || $validPayment<0) {
            flash('error','Uang dibayar harus berupa nominal rupiah yang valid.');redirect(url('transaksi_tambah'));
        }
        try{
            $id=(new Transaksi())->create([
                'kode'=>'TRX'.date('YmdHis').strtoupper(bin2hex(random_bytes(2))),
                'tanggal'=>date('Y-m-d'),
                'id_pelanggan'=>$validCustomerId,
                'metode_pembayaran'=>$paymentMethod,
            ],$items,(float)$discount,$validPayment);
            flash('success','Transaksi berhasil disimpan dan stok telah diperbarui.');redirect(url('transaksi_detail',['id'=>$id]));
        }catch(Throwable $e){flash('error','Transaksi gagal: '.$e->getMessage());redirect(url('transaksi_tambah'));}
    }
    public function detail(): void {
        $data=(new Transaksi())->detail((int)($_GET['id']??0));if(!$data){http_response_code(404);exit('Transaksi tidak ditemukan');}
        $title='Detail Transaksi';require APP_PATH.'/views/transaksi/detail.php';
    }
}
