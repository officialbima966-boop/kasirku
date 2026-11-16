USE db_toko;
-- Contoh data transaksi awal; jalankan setelah install.sql
INSERT INTO tbl_transaksi(kode_transaksi,tanggal,id_pelanggan,total_belanja,diskon,pajak) VALUES
('TRX-DEMO-001','2026-11-01',1,25000,0,2500),
('TRX-DEMO-002','2026-11-02',2,35000,0,3500),
('TRX-DEMO-003','2026-11-03',1,15000,0,1500),
('TRX-DEMO-004','2026-11-04',3,50000,0,5000);
INSERT INTO tbl_detail_transaksi(id_transaksi,id_produk,jumlah,harga,subtotal) VALUES
(1,1,1,12000,12000),(1,3,2,5000,10000),(2,2,1,16000,16000),
(2,4,2,7000,14000),(3,5,2,6000,12000),(4,1,2,12000,24000),(4,6,2,3500,7000);
