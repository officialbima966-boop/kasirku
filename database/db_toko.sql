-- ============================================================================
-- DATABASE STRUKTUR & DATA MASTER: db_toko
-- Sistem Informasi Penjualan & KasirKu (Shopee Edition)
-- Termasuk Fitur: RFID Member Card & Pemesanan Pelanggan ke Kasir
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `db_toko` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_toko`;

SET FOREIGN_KEY_CHECKS = 0;
DROP VIEW IF EXISTS `view_laporan_transaksi`;
DROP VIEW IF EXISTS `view_total_belanja`;
DROP TABLE IF EXISTS `tbl_detail_pesanan`;
DROP TABLE IF EXISTS `tbl_pesanan`;
DROP TABLE IF EXISTS `tbl_detail_transaksi`;
DROP TABLE IF EXISTS `tbl_transaksi`;
DROP TABLE IF EXISTS `tbl_produk`;
DROP TABLE IF EXISTS `tbl_kategori`;
DROP TABLE IF EXISTS `tbl_pelanggan`;
DROP TABLE IF EXISTS `tbl_log`;
DROP TABLE IF EXISTS `tbl_user`;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 1. Tabel Pengguna (tbl_user): Admin & Kasir
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
-- 2. Tabel Pelanggan & Member (tbl_pelanggan)
-- ----------------------------------------------------------------------------
CREATE TABLE `tbl_pelanggan` (
  `id_pelanggan` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_pelanggan` VARCHAR(100) NOT NULL,
  `kota` VARCHAR(50) DEFAULT NULL,
  `no_hp` VARCHAR(25) DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `username` VARCHAR(50) DEFAULT NULL UNIQUE,
  `password` VARCHAR(255) DEFAULT NULL,
  `rfid_uid` VARCHAR(64) DEFAULT NULL UNIQUE,
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
  CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori`) 
    REFERENCES `tbl_kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. Tabel Pesanan Pelanggan (tbl_pesanan)
-- ----------------------------------------------------------------------------
CREATE TABLE `tbl_pesanan` (
  `id_pesanan` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_pesanan` VARCHAR(40) NOT NULL UNIQUE,
  `id_pelanggan` INT NOT NULL,
  `status` ENUM('menunggu','dibayar','dibatalkan') NOT NULL DEFAULT 'menunggu',
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `dibayar_pada` TIMESTAMP NULL DEFAULT NULL,
  KEY `idx_pesanan_status_dibuat` (`status`, `dibuat_pada`),
  CONSTRAINT `fk_pesanan_pelanggan` FOREIGN KEY (`id_pelanggan`) 
    REFERENCES `tbl_pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. Tabel Detail Pesanan Pelanggan (tbl_detail_pesanan)
-- ----------------------------------------------------------------------------
CREATE TABLE `tbl_detail_pesanan` (
  `id_detail_pesanan` INT AUTO_INCREMENT PRIMARY KEY,
  `id_pesanan` INT NOT NULL,
  `id_produk` INT NOT NULL,
  `jumlah` INT NOT NULL,
  `harga` DECIMAL(12,2) NOT NULL,
  `subtotal` DECIMAL(14,2) NOT NULL,
  UNIQUE KEY `uq_detail_pesanan_produk` (`id_pesanan`, `id_produk`),
  CONSTRAINT `fk_detail_pesanan_header` FOREIGN KEY (`id_pesanan`) 
    REFERENCES `tbl_pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_pesanan_produk` FOREIGN KEY (`id_produk`) 
    REFERENCES `tbl_produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 7. Tabel Transaksi Kasir (tbl_transaksi)
-- ----------------------------------------------------------------------------
CREATE TABLE `tbl_transaksi` (
  `id_transaksi` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_transaksi` VARCHAR(40) NOT NULL UNIQUE,
  `tanggal` DATE NOT NULL,
  `id_pelanggan` INT NOT NULL,
  `total_belanja` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `metode_pembayaran` VARCHAR(30) NOT NULL DEFAULT 'Tunai',
  `uang_dibayar` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `kembalian` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `id_pesanan` INT DEFAULT NULL,
  `diskon_member_persen` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `diskon` DECIMAL(12,2) DEFAULT 0.00,
  `pajak` DECIMAL(12,2) DEFAULT 0.00,
  `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_transaksi_pelanggan` FOREIGN KEY (`id_pelanggan`) 
    REFERENCES `tbl_pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 8. Tabel Detail Transaksi (tbl_detail_transaksi)
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
-- 9. Tabel Log Aktivitas (tbl_log)
-- ----------------------------------------------------------------------------
CREATE TABLE `tbl_log` (
  `id_log` INT AUTO_INCREMENT PRIMARY KEY,
  `aktivitas` VARCHAR(60) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `waktu` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 10. Indeks Performa
-- ----------------------------------------------------------------------------
CREATE INDEX `idx_pelanggan_nama` ON `tbl_pelanggan`(`nama_pelanggan`);
CREATE INDEX `idx_transaksi_tanggal` ON `tbl_transaksi`(`tanggal`);
CREATE INDEX `idx_transaksi_pelanggan` ON `tbl_transaksi`(`id_pelanggan`);
CREATE INDEX `idx_produk_nama` ON `tbl_produk`(`nama_produk`);
CREATE INDEX `idx_detail_transaksi` ON `tbl_detail_transaksi`(`id_transaksi`);

-- ----------------------------------------------------------------------------
-- 11. Data Awal Pengguna & Master
-- ----------------------------------------------------------------------------
-- User Admin & Kasir (password default: 'password' / 'admin' / 'kasir')
INSERT INTO `tbl_user` (`id_user`, `nama`, `username`, `password`, `role`, `status`) VALUES
(1, 'Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'aktif'),
(2, 'Kasir Toko', 'kasir', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kasir', 'aktif');

-- Kategori
INSERT INTO `tbl_kategori` (`nama_kategori`) VALUES 
('Makanan'),
('Minuman'),
('Perlengkapan'),
('Fashion'),
('Elektronik');

-- Pelanggan (Andi & Budi memiliki Akun Member & Kartu RFID aktif)
INSERT INTO `tbl_pelanggan` (`id_pelanggan`, `nama_pelanggan`, `kota`, `no_hp`, `alamat`, `username`, `password`, `rfid_uid`) VALUES
(1, 'Andi Pratama', 'Ponorogo', '081234567801', 'Jl. Mawar No. 12', 'member', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'RFID-ANDI-8821'),
(2, 'Budi Santoso', 'Madiun', '081234567802', 'Jl. Melati No. 45', 'budi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'RFID-BUDI-9942'),
(3, 'Citra Dewi', 'Surabaya', '081234567803', 'Jl. Kenanga No. 8', NULL, NULL, NULL),
(4, 'Dewi Lestari', 'Ponorogo', '081234567804', 'Jl. Anggrek No. 19', NULL, NULL, NULL),
(5, 'Eko Prasetyo', 'Malang', '081234567805', 'Jl. Dahlia No. 7', NULL, NULL, NULL);

-- Produk
INSERT INTO `tbl_produk` (`id_kategori`, `nama_produk`, `harga`, `stok`, `deskripsi`) VALUES
(1, 'Biskuit Cokelat Premium', 12000.00, 100, 'Biskuit renyah rasa cokelat istimewa'),
(1, 'Roti Tawar Gandum', 16000.00, 80, 'Roti tawar gandum kaya serat'),
(2, 'Air Mineral 600ml', 5000.00, 200, 'Air mineral pegunungan alami segar'),
(2, 'Teh Botol Melati', 7000.00, 150, 'Minuman teh wangi aroma melati'),
(3, 'Buku Tulis Bergaris 38 Lembar', 6000.00, 120, 'Buku tulis kertas tebal berkualitas'),
(3, 'Pulpen Gel Tinta Hitam', 3500.00, 250, 'Pulpen tinta gel nyaman untuk menulis');

-- Transaksi Awal
INSERT INTO `tbl_transaksi` (`kode_transaksi`, `tanggal`, `id_pelanggan`, `total_belanja`, `diskon`, `pajak`, `metode_pembayaran`, `uang_dibayar`, `kembalian`) VALUES
('TRX-DEMO-001', '2026-11-01', 1, 25000.00, 0.00, 2500.00, 'Tunai', 30000.00, 5000.00),
('TRX-DEMO-002', '2026-11-02', 2, 35000.00, 0.00, 3500.00, 'QRIS', 35000.00, 0.00);

-- Rincian Transaksi
INSERT INTO `tbl_detail_transaksi` (`id_transaksi`, `id_produk`, `jumlah`, `harga`, `subtotal`) VALUES
(1, 1, 1, 12000.00, 12000.00),
(1, 3, 2, 5000.00, 10000.00),
(2, 2, 1, 16000.00, 16000.00),
(2, 4, 2, 7000.00, 14000.00);

-- Pesanan Contoh yang sedang Menunggu Kasir
INSERT INTO `tbl_pesanan` (`id_pesanan`, `kode_pesanan`, `id_pelanggan`, `status`) VALUES
(1, 'PSN-DEMO-001', 1, 'menunggu');

INSERT INTO `tbl_detail_pesanan` (`id_pesanan`, `id_produk`, `jumlah`, `harga`, `subtotal`) VALUES
(1, 1, 2, 12000.00, 24000.00),
(1, 3, 1, 5000.00, 5000.00);

-- ----------------------------------------------------------------------------
-- 12. Views
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
  t.pajak,
  t.metode_pembayaran
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
