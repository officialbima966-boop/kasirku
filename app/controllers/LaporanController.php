<?php
class LaporanController {
    public function index(): void {
        $db=Database::connect();
        $start=$_GET['mulai']??date('Y-m-01');$end=$_GET['sampai']??date('Y-m-d');
        $s=$db->prepare("SELECT t.*,p.nama_pelanggan FROM tbl_transaksi t JOIN tbl_pelanggan p ON p.id_pelanggan=t.id_pelanggan WHERE t.tanggal BETWEEN ? AND ? ORDER BY t.tanggal DESC");
        $s->execute([$start,$end]);$items=$s->fetchAll();
        $total=array_sum(array_column($items,'total_belanja'));$title='Laporan Penjualan';
        require APP_PATH.'/views/laporan/index.php';
    }
}
