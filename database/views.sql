USE db_toko;
CREATE OR REPLACE VIEW view_laporan_transaksi AS
SELECT t.id_transaksi,t.kode_transaksi,t.tanggal,p.nama_pelanggan,p.kota,
       t.total_belanja,t.diskon,t.pajak
FROM tbl_transaksi t JOIN tbl_pelanggan p ON p.id_pelanggan=t.id_pelanggan;

CREATE OR REPLACE VIEW view_total_belanja AS
SELECT p.id_pelanggan,p.nama_pelanggan,COUNT(t.id_transaksi) AS total_transaksi,
       COALESCE(SUM(t.total_belanja),0) AS total_belanja
FROM tbl_pelanggan p LEFT JOIN tbl_transaksi t ON t.id_pelanggan=p.id_pelanggan
GROUP BY p.id_pelanggan,p.nama_pelanggan;
