USE db_toko;
-- 1 Semua pelanggan dan transaksi (LEFT JOIN)
SELECT p.nama_pelanggan,t.kode_transaksi,t.total_belanja
FROM tbl_pelanggan p LEFT JOIN tbl_transaksi t ON p.id_pelanggan=t.id_pelanggan;
-- 2 Pelanggan belum pernah transaksi
SELECT * FROM tbl_pelanggan p WHERE NOT EXISTS
(SELECT 1 FROM tbl_transaksi t WHERE t.id_pelanggan=p.id_pelanggan);
-- 3 Transaksi di atas rata-rata
SELECT * FROM tbl_transaksi WHERE total_belanja>(SELECT AVG(total_belanja) FROM tbl_transaksi);
-- 4 Pelanggan dengan transaksi terbanyak
SELECT p.nama_pelanggan,COUNT(t.id_transaksi) jumlah_transaksi
FROM tbl_pelanggan p JOIN tbl_transaksi t ON p.id_pelanggan=t.id_pelanggan
GROUP BY p.id_pelanggan ORDER BY jumlah_transaksi DESC;
-- 5 Total belanja setiap pelanggan
SELECT * FROM view_total_belanja;
-- 6 Lima transaksi terbesar
SELECT * FROM tbl_transaksi ORDER BY total_belanja DESC LIMIT 5;
-- 7 UNION kota pelanggan dan kota cabang (contoh dua SELECT)
SELECT kota FROM tbl_pelanggan WHERE kota IS NOT NULL
UNION SELECT 'Kota Cabang Contoh';
-- Multiple JOIN
SELECT p.nama_pelanggan,t.kode_transaksi,d.nama_produk,d.jumlah,d.harga,d.subtotal
FROM tbl_pelanggan p JOIN tbl_transaksi t ON p.id_pelanggan=t.id_pelanggan
JOIN tbl_detail_transaksi d ON t.id_transaksi=d.id_transaksi
JOIN tbl_produk pr ON pr.id_produk=d.id_produk
JOIN (SELECT id_produk,nama_produk FROM tbl_produk) dprod ON dprod.id_produk=pr.id_produk;
