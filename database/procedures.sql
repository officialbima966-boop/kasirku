USE db_toko;
DROP PROCEDURE IF EXISTS TampilPelanggan;
DROP PROCEDURE IF EXISTS CariPelanggan;
DROP PROCEDURE IF EXISTS TambahPelanggan;
DROP PROCEDURE IF EXISTS HitungPelanggan;
DROP PROCEDURE IF EXISTS LaporanTransaksi;
DELIMITER //
CREATE PROCEDURE TampilPelanggan() BEGIN SELECT * FROM tbl_pelanggan ORDER BY id_pelanggan; END//
CREATE PROCEDURE CariPelanggan(IN p_id INT) BEGIN SELECT * FROM tbl_pelanggan WHERE id_pelanggan=p_id; END//
CREATE PROCEDURE TambahPelanggan(IN p_nama VARCHAR(100),IN p_kota VARCHAR(50))
BEGIN INSERT INTO tbl_pelanggan(nama_pelanggan,kota) VALUES(p_nama,p_kota); END//
CREATE PROCEDURE HitungPelanggan(OUT jumlah INT)
BEGIN SELECT COUNT(*) INTO jumlah FROM tbl_pelanggan; END//
CREATE PROCEDURE LaporanTransaksi(IN p_id_pelanggan INT)
BEGIN SELECT * FROM view_laporan_transaksi WHERE id_transaksi IN
(SELECT id_transaksi FROM tbl_transaksi WHERE id_pelanggan=p_id_pelanggan); END//
DELIMITER ;
