-- ============================================================================
-- DATABASE STRUKTUR & DATA AWAL: db_toko
-- Sistem Informasi Penjualan & KasirKu (Shopee Edition)
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `db_toko` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_toko`;

SET FOREIGN_KEY_CHECKS = 0;
DROP VIEW IF EXISTS `view_laporan_transaksi`;
DROP VIEW IF EXISTS `view_total_belanja`;
DROP TABLE IF EXISTS `tbl_detail_transaksi`;
DROP TABLE IF EXISTS `tbl_transaksi`;
DROP TABLE IF EXISTS `tbl_produk`;
DROP TABLE IF EXISTS `tbl_kategori`;
DROP TABLE IF EXISTS `tbl_pelanggan`;
DROP TABLE IF EXISTS `tbl_log`;
DROP TABLE IF EXISTS `tbl_user`;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 1. Tabel Pengguna (tbl_user)
-- ----------------------------------------------------------------------------
CREATE TABLE `tbl_user` (
  `id_user` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','kasir') NOT NULL DEFAULT 'admin',
  `status` ENUM('aktif','nonaktif') DEFAULT 'aktif',
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. Tabel Pelanggan (tbl_pelanggan)
-- ----------------------------------------------------------------------------
CREATE TABLE `tbl_pelanggan` (
  `id_pelanggan` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_pelanggan` VARCHAR(100) NOT NULL,
  `kota` VARCHAR(50) DEFAULT NULL,
  `no_hp` VARCHAR(25) DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. Tabel Kategori Produk (tbl_kategori)
-- ----------------------------------------------------------------------------
CREATE TABLE `tbl_kategori` (
  `id_kategori` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_kategori` VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. Tabel Produk (tbl_produk)
-- ----------------------------------------------------------------------------
CREATE TABLE `tbl_produk` (
  `id_produk` INT AUTO_INCREMENT PRIMARY KEY,
  `id_kategori` INT NOT NULL,
  `nama_produk` VARCHAR(120) NOT NULL,
  `harga` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `stok` INT NOT NULL DEFAULT 0,
  `deskripsi` TEXT DEFAULT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `barcode` VARCHAR(64) DEFAULT NULL,
  UNIQUE KEY `uq_produk_barcode` (`barcode`),
  CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori`) 
    REFERENCES `tbl_kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. Tabel Header Transaksi (tbl_transaksi)
-- ----------------------------------------------------------------------------
CREATE TABLE `tbl_transaksi` (
  `id_transaksi` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_transaksi` VARCHAR(40) NOT NULL UNIQUE,
  `tanggal` DATE NOT NULL,
  `id_pelanggan` INT NOT NULL,
  `total_belanja` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `diskon` DECIMAL(12,2) DEFAULT 0.00,
  `pajak` DECIMAL(12,2) DEFAULT 0.00,
  `uang_dibayar` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `kembalian` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_transaksi_pelanggan` FOREIGN KEY (`id_pelanggan`) 
    REFERENCES `tbl_pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. Tabel Rincian Transaksi (tbl_detail_transaksi)
-- ----------------------------------------------------------------------------
CREATE TABLE `tbl_detail_transaksi` (
  `id_detail` INT AUTO_INCREMENT PRIMARY KEY,
  `id_transaksi` INT NOT NULL,
  `id_produk` INT NOT NULL,
  `jumlah` INT NOT NULL,
  `harga` DECIMAL(12,2) NOT NULL,
  `subtotal` DECIMAL(14,2) NOT NULL,
  CONSTRAINT `fk_detail_transaksi` FOREIGN KEY (`id_transaksi`) 
    REFERENCES `tbl_transaksi` (`id_transaksi`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_produk` FOREIGN KEY (`id_produk`) 
    REFERENCES `tbl_produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 7. Tabel Log Aktivitas (tbl_log)
-- ----------------------------------------------------------------------------
CREATE TABLE `tbl_log` (
  `id_log` INT AUTO_INCREMENT PRIMARY KEY,
  `aktivitas` VARCHAR(60) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `waktu` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 8. Indeks Kinerja Query
-- ----------------------------------------------------------------------------
CREATE INDEX `idx_pelanggan_nama` ON `tbl_pelanggan`(`nama_pelanggan`);
CREATE INDEX `idx_transaksi_tanggal` ON `tbl_transaksi`(`tanggal`);
CREATE INDEX `idx_transaksi_pelanggan` ON `tbl_transaksi`(`id_pelanggan`);
CREATE INDEX `idx_produk_nama` ON `tbl_produk`(`nama_produk`);
CREATE INDEX `idx_detail_transaksi` ON `tbl_detail_transaksi`(`id_transaksi`);

-- ----------------------------------------------------------------------------
-- 9. Data Awal (Seeds)
-- ----------------------------------------------------------------------------
-- Akun Admin Default: username 'admin', password 'password' (juga bisa 'admin')
INSERT INTO `tbl_user` (`nama`, `username`, `password`, `role`, `status`) VALUES
('Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'aktif');

-- Kategori
INSERT INTO `tbl_kategori` (`nama_kategori`) VALUES 
('Makanan'),
('Minuman'),
('Perlengkapan'),
('Fashion'),
('Elektronik');

-- Pelanggan
INSERT INTO `tbl_pelanggan` (`nama_pelanggan`, `kota`, `no_hp`, `alamat`) VALUES
('Andi Pratama', 'Ponorogo', '081234567801', 'Jl. Mawar No. 12'),
('Budi Santoso', 'Madiun', '081234567802', 'Jl. Melati No. 45'),
('Citra Dewi', 'Surabaya', '081234567803', 'Jl. Kenanga No. 8'),
('Dewi Lestari', 'Ponorogo', '081234567804', 'Jl. Anggrek No. 19'),
('Eko Prasetyo', 'Malang', '081234567805', 'Jl. Dahlia No. 7'),
('Fani Rahmawati', 'Madiun', '081234567806', 'Jl. Teratai No. 22'),
('Gilang Ramadhan', 'Surabaya', '081234567807', 'Jl. Kamboja No. 31'),
('Hana Permata', 'Kediri', '081234567808', 'Jl. Flamboyan No. 14'),
('Indra Kusuma', 'Blitar', '081234567809', 'Jl. Cempaka No. 5'),
('Joko Widodo', 'Ponorogo', '081234567810', 'Jl. Nusa No. 10');

-- Produk
INSERT INTO `tbl_produk` (`id_kategori`, `nama_produk`, `harga`, `stok`, `deskripsi`, `barcode`) VALUES
(1, 'Biskuit Cokelat Premium', 12000.00, 100, 'Biskuit renyah rasa cokelat istimewa', 'KS-SEED-001'),
(1, 'Roti Tawar Gandum', 16000.00, 80, 'Roti tawar gandum kaya serat', 'KS-SEED-002'),
(2, 'Air Mineral 600ml', 5000.00, 200, 'Air mineral pegunungan alami segar', 'KS-SEED-003'),
(2, 'Teh Botol Melati', 7000.00, 150, 'Minuman teh wangi aroma melati', 'KS-SEED-004'),
(3, 'Buku Tulis Bergaris 38 Lembar', 6000.00, 120, 'Buku tulis kertas tebal berkualitas', 'KS-SEED-005'),
(3, 'Pulpen Gel Tinta Hitam', 3500.00, 250, 'Pulpen tinta gel nyaman untuk menulis', 'KS-SEED-006');

-- Transaksi Demo
INSERT INTO `tbl_transaksi` (`kode_transaksi`, `tanggal`, `id_pelanggan`, `total_belanja`, `diskon`, `pajak`, `uang_dibayar`, `kembalian`) VALUES
('TRX-DEMO-001', '2026-11-01', 1, 25000.00, 0.00, 2500.00, 25000.00, 0.00),
('TRX-DEMO-002', '2026-11-02', 2, 35000.00, 0.00, 3500.00, 35000.00, 0.00),
('TRX-DEMO-003', '2026-11-03', 1, 15000.00, 0.00, 1500.00, 15000.00, 0.00),
('TRX-DEMO-004', '2026-11-04', 3, 50000.00, 0.00, 5000.00, 50000.00, 0.00);

-- Rincian Transaksi Demo
INSERT INTO `tbl_detail_transaksi` (`id_transaksi`, `id_produk`, `jumlah`, `harga`, `subtotal`) VALUES
(1, 1, 1, 12000.00, 12000.00),
(1, 3, 2, 5000.00, 10000.00),
(2, 2, 1, 16000.00, 16000.00),
(2, 4, 2, 7000.00, 14000.00),
(3, 5, 2, 6000.00, 12000.00),
(4, 1, 2, 12000.00, 24000.00),
(4, 6, 2, 3500.00, 7000.00);

-- ----------------------------------------------------------------------------
-- 10. Database Views
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW `view_laporan_transaksi` AS
SELECT 
  t.id_transaksi,
  t.kode_transaksi,
  t.tanggal,
  p.nama_pelanggan,
  p.kota,
  t.total_belanja,
  t.diskon,
  t.pajak
FROM `tbl_transaksi` t 
JOIN `tbl_pelanggan` p ON p.id_pelanggan = t.id_pelanggan;

CREATE OR REPLACE VIEW `view_total_belanja` AS
SELECT 
  p.id_pelanggan,
  p.nama_pelanggan,
  COUNT(t.id_transaksi) AS total_transaksi,
  COALESCE(SUM(t.total_belanja), 0) AS total_belanja
FROM `tbl_pelanggan` p 
LEFT JOIN `tbl_transaksi` t ON t.id_pelanggan = p.id_pelanggan
GROUP BY p.id_pelanggan, p.nama_pelanggan;

-- ----------------------------------------------------------------------------
-- 11. Stored Procedures & Functions & Triggers
-- ----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `TampilPelanggan`;
DROP PROCEDURE IF EXISTS `CariPelanggan`;
DROP PROCEDURE IF EXISTS `TambahPelanggan`;
DROP PROCEDURE IF EXISTS `HitungPelanggan`;
DROP PROCEDURE IF EXISTS `LaporanTransaksi`;

DROP FUNCTION IF EXISTS `HitungDiskon`;
DROP FUNCTION IF EXISTS `HitungTotal`;
DROP FUNCTION IF EXISTS `HitungPajak`;
DROP FUNCTION IF EXISTS `KategoriBelanja`;
DROP FUNCTION IF EXISTS `KonversiNilai`;

DROP TRIGGER IF EXISTS `after_insert_pelanggan`;
DROP TRIGGER IF EXISTS `after_update_pelanggan`;
DROP TRIGGER IF EXISTS `before_insert_transaksi`;
DROP TRIGGER IF EXISTS `after_insert_detail`;
DROP TRIGGER IF EXISTS `after_insert_detail_log`;

DELIMITER //

CREATE PROCEDURE `TampilPelanggan`()
BEGIN
  SELECT * FROM `tbl_pelanggan` ORDER BY `id_pelanggan`;
END//

CREATE PROCEDURE `CariPelanggan`(IN p_id INT)
BEGIN
  SELECT * FROM `tbl_pelanggan` WHERE `id_pelanggan` = p_id;
END//

CREATE PROCEDURE `TambahPelanggan`(IN p_nama VARCHAR(100), IN p_kota VARCHAR(50))
BEGIN
  INSERT INTO `tbl_pelanggan`(`nama_pelanggan`, `kota`) VALUES(p_nama, p_kota);
END//

CREATE PROCEDURE `HitungPelanggan`(OUT jumlah INT)
BEGIN
  SELECT COUNT(*) INTO jumlah FROM `tbl_pelanggan`;
END//

CREATE PROCEDURE `LaporanTransaksi`(IN p_id_pelanggan INT)
BEGIN
  SELECT * FROM `view_laporan_transaksi` 
  WHERE `id_transaksi` IN (SELECT `id_transaksi` FROM `tbl_transaksi` WHERE `id_pelanggan` = p_id_pelanggan);
END//

CREATE FUNCTION `HitungDiskon`(harga DECIMAL(12,2), diskon DECIMAL(5,2))
RETURNS DECIMAL(12,2) DETERMINISTIC NO SQL
BEGIN
  RETURN harga - (harga * diskon / 100);
END//

CREATE FUNCTION `HitungTotal`(harga DECIMAL(12,2), jumlah INT)
RETURNS DECIMAL(14,2) DETERMINISTIC NO SQL
BEGIN
  RETURN harga * jumlah;
END//

CREATE FUNCTION `HitungPajak`(harga DECIMAL(12,2))
RETURNS DECIMAL(12,2) DETERMINISTIC NO SQL
BEGIN
  RETURN harga * 0.11;
END//

CREATE FUNCTION `KategoriBelanja`(total DECIMAL(12,2))
RETURNS VARCHAR(30) DETERMINISTIC NO SQL
BEGIN
  RETURN CASE 
    WHEN total >= 1000000 THEN 'Pelanggan Gold'
    WHEN total >= 500000 THEN 'Pelanggan Silver'
    ELSE 'Pelanggan Reguler'
  END;
END//

CREATE FUNCTION `KonversiNilai`(nilai DECIMAL(5,2))
RETURNS VARCHAR(2) DETERMINISTIC NO SQL
BEGIN
  RETURN CASE 
    WHEN nilai >= 90 THEN 'A'
    WHEN nilai >= 80 THEN 'B'
    WHEN nilai >= 70 THEN 'C'
    WHEN nilai >= 60 THEN 'D'
    ELSE 'E'
  END;
END//

CREATE TRIGGER `after_insert_pelanggan` AFTER INSERT ON `tbl_pelanggan` FOR EACH ROW
BEGIN
  INSERT INTO `tbl_log`(`aktivitas`, `deskripsi`) 
  VALUES('INSERT PELANGGAN', CONCAT('Pelanggan baru: ', NEW.nama_pelanggan));
END//

CREATE TRIGGER `after_update_pelanggan` AFTER UPDATE ON `tbl_pelanggan` FOR EACH ROW
BEGIN
  INSERT INTO `tbl_log`(`aktivitas`, `deskripsi`) 
  VALUES('UPDATE PELANGGAN', CONCAT('Perubahan ', OLD.nama_pelanggan, ' menjadi ', NEW.nama_pelanggan));
END//

CREATE TRIGGER `before_insert_transaksi` BEFORE INSERT ON `tbl_transaksi` FOR EACH ROW
BEGIN
  IF NEW.total_belanja < 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Total transaksi tidak boleh negatif';
  END IF;
END//

CREATE TRIGGER `after_insert_detail_log` AFTER INSERT ON `tbl_detail_transaksi` FOR EACH ROW
BEGIN
  INSERT INTO `tbl_log`(`aktivitas`, `deskripsi`) 
  VALUES('DETAIL TRANSAKSI', CONCAT('Produk ID ', NEW.id_produk, ' terjual ', NEW.jumlah));
END//

DELIMITER ;
