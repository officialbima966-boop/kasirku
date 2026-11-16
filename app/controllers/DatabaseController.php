<?php
class DatabaseController {
    public function index(): void {
        $db=Database::connect();
        $queries=[
          'View laporan transaksi'=>"SELECT * FROM view_laporan_transaksi ORDER BY tanggal DESC LIMIT 20",
          'Total belanja pelanggan'=>"SELECT * FROM view_total_belanja ORDER BY total_belanja DESC",
          'Transaksi di atas rata-rata'=>"SELECT * FROM tbl_transaksi WHERE total_belanja > (SELECT AVG(total_belanja) FROM tbl_transaksi)",
          'Lima transaksi terbesar'=>"SELECT * FROM tbl_transaksi ORDER BY total_belanja DESC LIMIT 5",
          'Pelanggan belum transaksi'=>"SELECT p.* FROM tbl_pelanggan p LEFT JOIN tbl_transaksi t ON p.id_pelanggan=t.id_pelanggan WHERE t.id_transaksi IS NULL"
        ];
        $results=[];
        foreach($queries as $label=>$sql){try{$results[$label]=$db->query($sql)->fetchAll();}catch(Throwable $e){$results[$label]=['error'=>$e->getMessage()];}}
        $title='Fitur Advanced Database';require APP_PATH.'/views/database/index.php';
    }
    public function logs(): void {
        $db=Database::connect();$logs=$db->query("SELECT * FROM tbl_log ORDER BY id_log DESC LIMIT 100")->fetchAll();
        $title='Log Aktivitas';require APP_PATH.'/views/database/logs.php';
    }
}
