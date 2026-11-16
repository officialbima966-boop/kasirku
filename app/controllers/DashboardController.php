<?php
class DashboardController {
    public function index(): void {
        $db=Database::connect();
        $stats=[
          'pelanggan'=>(int)$db->query("SELECT COUNT(*) FROM tbl_pelanggan")->fetchColumn(),
          'produk'=>(int)$db->query("SELECT COUNT(*) FROM tbl_produk")->fetchColumn(),
          'transaksi'=>(int)$db->query("SELECT COUNT(*) FROM tbl_transaksi")->fetchColumn(),
          'omzet'=>(float)$db->query("SELECT COALESCE(SUM(total_belanja),0) FROM tbl_transaksi")->fetchColumn()
        ];
        $recent=$db->query("SELECT t.*,p.nama_pelanggan,p.kota FROM tbl_transaksi t JOIN tbl_pelanggan p ON p.id_pelanggan=t.id_pelanggan ORDER BY t.id_transaksi DESC LIMIT 7")->fetchAll();
        $topProducts=$db->query("SELECT p.*, k.nama_kategori FROM tbl_produk p JOIN tbl_kategori k ON k.id_kategori=p.id_kategori ORDER BY p.id_produk ASC LIMIT 4")->fetchAll();
        $title='Dashboard Toko'; require APP_PATH.'/views/dashboard/index.php';
    }
}
