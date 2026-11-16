USE db_toko;
CREATE INDEX idx_pelanggan_nama ON tbl_pelanggan(nama_pelanggan);
CREATE INDEX idx_transaksi_tanggal ON tbl_transaksi(tanggal);
CREATE INDEX idx_transaksi_pelanggan ON tbl_transaksi(id_pelanggan);
CREATE INDEX idx_produk_nama ON tbl_produk(nama_produk);
CREATE INDEX idx_detail_transaksi ON tbl_detail_transaksi(id_transaksi);
-- Uji optimasi:
EXPLAIN SELECT t.kode_transaksi,p.nama_pelanggan,t.total_belanja
FROM tbl_transaksi t JOIN tbl_pelanggan p ON p.id_pelanggan=t.id_pelanggan
WHERE t.tanggal BETWEEN '2026-01-01' AND '2026-12-31';
