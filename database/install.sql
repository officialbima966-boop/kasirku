CREATE DATABASE IF NOT EXISTS db_toko CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_toko;

DROP TRIGGER IF EXISTS after_insert_detail;

CREATE TABLE IF NOT EXISTS tbl_user (
 id_user INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100) NOT NULL,
 username VARCHAR(50) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL,
 role ENUM('admin','kasir') NOT NULL DEFAULT 'admin', status ENUM('aktif','nonaktif') DEFAULT 'aktif',
 dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tbl_pelanggan (
 id_pelanggan INT AUTO_INCREMENT PRIMARY KEY, nama_pelanggan VARCHAR(100) NOT NULL,
 kota VARCHAR(50), no_hp VARCHAR(25), alamat TEXT, username VARCHAR(50) DEFAULT NULL,
 password VARCHAR(255) DEFAULT NULL, rfid_uid VARCHAR(64) DEFAULT NULL,
 dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_pelanggan_username (username), UNIQUE KEY uq_pelanggan_rfid_uid (rfid_uid)
) ENGINE=InnoDB;

ALTER TABLE tbl_pelanggan ADD COLUMN IF NOT EXISTS username VARCHAR(50) DEFAULT NULL AFTER alamat;
ALTER TABLE tbl_pelanggan ADD COLUMN IF NOT EXISTS password VARCHAR(255) DEFAULT NULL AFTER username;
ALTER TABLE tbl_pelanggan ADD COLUMN IF NOT EXISTS rfid_uid VARCHAR(64) DEFAULT NULL AFTER password;
SET @member_username_index_exists = (
 SELECT COUNT(*) FROM information_schema.statistics
 WHERE table_schema=DATABASE() AND table_name='tbl_pelanggan' AND index_name='uq_pelanggan_username'
);
SET @member_username_index_sql = IF(@member_username_index_exists=0,
 'CREATE UNIQUE INDEX uq_pelanggan_username ON tbl_pelanggan(username)',
 'SELECT 1');
PREPARE member_username_index_stmt FROM @member_username_index_sql;
EXECUTE member_username_index_stmt;
DEALLOCATE PREPARE member_username_index_stmt;
SET @member_rfid_index_exists = (
 SELECT COUNT(*) FROM information_schema.statistics
 WHERE table_schema=DATABASE() AND table_name='tbl_pelanggan' AND index_name='uq_pelanggan_rfid_uid'
);
SET @member_rfid_index_sql = IF(@member_rfid_index_exists=0,
 'CREATE UNIQUE INDEX uq_pelanggan_rfid_uid ON tbl_pelanggan(rfid_uid)',
 'SELECT 1');
PREPARE member_rfid_index_stmt FROM @member_rfid_index_sql;
EXECUTE member_rfid_index_stmt;
DEALLOCATE PREPARE member_rfid_index_stmt;

CREATE TABLE IF NOT EXISTS tbl_kategori (
 id_kategori INT AUTO_INCREMENT PRIMARY KEY, nama_kategori VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tbl_produk (
 id_produk INT AUTO_INCREMENT PRIMARY KEY, id_kategori INT NOT NULL,
 nama_produk VARCHAR(120) NOT NULL, harga DECIMAL(12,2) NOT NULL DEFAULT 0,
 stok INT NOT NULL DEFAULT 0, deskripsi TEXT, foto VARCHAR(255) DEFAULT NULL,
 barcode VARCHAR(64) DEFAULT NULL, UNIQUE KEY uq_produk_barcode (barcode),
 CONSTRAINT fk_produk_kategori FOREIGN KEY(id_kategori) REFERENCES tbl_kategori(id_kategori) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

ALTER TABLE tbl_produk ADD COLUMN IF NOT EXISTS foto VARCHAR(255) DEFAULT NULL AFTER deskripsi;
ALTER TABLE tbl_produk ADD COLUMN IF NOT EXISTS barcode VARCHAR(64) DEFAULT NULL AFTER foto;
UPDATE tbl_produk SET barcode=CONCAT('KS',LPAD(id_produk,10,'0')) WHERE barcode IS NULL OR barcode='';

SET @barcode_index_exists = (
 SELECT COUNT(*) FROM information_schema.statistics
 WHERE table_schema=DATABASE() AND table_name='tbl_produk' AND index_name='uq_produk_barcode'
);
SET @barcode_index_sql = IF(@barcode_index_exists=0,
 'CREATE UNIQUE INDEX uq_produk_barcode ON tbl_produk(barcode)',
 'SELECT 1');
PREPARE barcode_index_stmt FROM @barcode_index_sql;
EXECUTE barcode_index_stmt;
DEALLOCATE PREPARE barcode_index_stmt;

CREATE TABLE IF NOT EXISTS tbl_transaksi (
 id_transaksi INT AUTO_INCREMENT PRIMARY KEY, kode_transaksi VARCHAR(40) NOT NULL UNIQUE,
 tanggal DATE NOT NULL, id_pelanggan INT NOT NULL,
 total_belanja DECIMAL(14,2) NOT NULL DEFAULT 0, diskon DECIMAL(12,2) DEFAULT 0,
 pajak DECIMAL(12,2) DEFAULT 0, metode_pembayaran VARCHAR(30) NOT NULL DEFAULT 'Tunai',
 id_pesanan INT DEFAULT NULL, diskon_member_persen DECIMAL(5,2) NOT NULL DEFAULT 0,
 uang_dibayar DECIMAL(14,2) NOT NULL DEFAULT 0,
 kembalian DECIMAL(14,2) NOT NULL DEFAULT 0, dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_transaksi_pesanan (id_pesanan),
 CONSTRAINT fk_transaksi_pelanggan FOREIGN KEY(id_pelanggan) REFERENCES tbl_pelanggan(id_pelanggan) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

ALTER TABLE tbl_transaksi ADD COLUMN IF NOT EXISTS metode_pembayaran VARCHAR(30) NOT NULL DEFAULT 'Tunai' AFTER total_belanja;
ALTER TABLE tbl_transaksi ADD COLUMN IF NOT EXISTS id_pesanan INT DEFAULT NULL AFTER metode_pembayaran;
ALTER TABLE tbl_transaksi ADD COLUMN IF NOT EXISTS diskon_member_persen DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER id_pesanan;
ALTER TABLE tbl_transaksi ADD COLUMN IF NOT EXISTS uang_dibayar DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER pajak;
ALTER TABLE tbl_transaksi ADD COLUMN IF NOT EXISTS kembalian DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER uang_dibayar;
UPDATE tbl_transaksi SET uang_dibayar=total_belanja WHERE uang_dibayar=0 AND total_belanja>0;
SET @order_transaction_index_exists = (
 SELECT COUNT(*) FROM information_schema.statistics
 WHERE table_schema=DATABASE() AND table_name='tbl_transaksi' AND index_name='uq_transaksi_pesanan'
);
SET @order_transaction_index_sql = IF(@order_transaction_index_exists=0,
 'CREATE UNIQUE INDEX uq_transaksi_pesanan ON tbl_transaksi(id_pesanan)',
 'SELECT 1');
PREPARE order_transaction_index_stmt FROM @order_transaction_index_sql;
EXECUTE order_transaction_index_stmt;
DEALLOCATE PREPARE order_transaction_index_stmt;

CREATE TABLE IF NOT EXISTS tbl_detail_transaksi (
 id_detail INT AUTO_INCREMENT PRIMARY KEY, id_transaksi INT NOT NULL, id_produk INT NOT NULL,
 jumlah INT NOT NULL, harga DECIMAL(12,2) NOT NULL, subtotal DECIMAL(14,2) NOT NULL,
 CONSTRAINT fk_detail_transaksi FOREIGN KEY(id_transaksi) REFERENCES tbl_transaksi(id_transaksi) ON DELETE CASCADE ON UPDATE CASCADE,
 CONSTRAINT fk_detail_produk FOREIGN KEY(id_produk) REFERENCES tbl_produk(id_produk) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tbl_pesanan (
 id_pesanan INT AUTO_INCREMENT PRIMARY KEY, kode_pesanan VARCHAR(40) NOT NULL UNIQUE,
 id_pelanggan INT NOT NULL, status ENUM('menunggu','dibayar','dibatalkan') NOT NULL DEFAULT 'menunggu',
 dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP, dibayar_pada TIMESTAMP NULL DEFAULT NULL,
 KEY idx_pesanan_status_dibuat (status,dibuat_pada),
 CONSTRAINT fk_pesanan_pelanggan FOREIGN KEY(id_pelanggan) REFERENCES tbl_pelanggan(id_pelanggan) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tbl_detail_pesanan (
 id_detail_pesanan INT AUTO_INCREMENT PRIMARY KEY, id_pesanan INT NOT NULL, id_produk INT NOT NULL,
 jumlah INT NOT NULL, harga DECIMAL(12,2) NOT NULL, subtotal DECIMAL(14,2) NOT NULL,
 UNIQUE KEY uq_detail_pesanan_produk (id_pesanan,id_produk),
 CONSTRAINT fk_detail_pesanan_header FOREIGN KEY(id_pesanan) REFERENCES tbl_pesanan(id_pesanan) ON DELETE CASCADE ON UPDATE CASCADE,
 CONSTRAINT fk_detail_pesanan_produk FOREIGN KEY(id_produk) REFERENCES tbl_produk(id_produk) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tbl_log (
 id_log INT AUTO_INCREMENT PRIMARY KEY, aktivitas VARCHAR(60) NOT NULL,
 deskripsi TEXT, waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO tbl_user(nama,username,password,role) VALUES
('Administrator','admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin'),
('Kasir','kasir',CONCAT(CHAR(36),'2y',CHAR(36),'10',CHAR(36),'i/ZmGcw46nEnY1ixqGVpcuMj4PFoBEAvy3PxEJXuPlvy5vZmHUEB2'),'kasir');
INSERT IGNORE INTO tbl_kategori(nama_kategori) VALUES ('Makanan'),('Minuman'),('Perlengkapan');

INSERT INTO tbl_pelanggan(nama_pelanggan,kota,no_hp,alamat)
SELECT seed.nama_pelanggan,seed.kota,seed.no_hp,seed.alamat
FROM (
 SELECT 'Andi' AS nama_pelanggan,'Ponorogo' AS kota,'081234567801' AS no_hp,'Jl. Mawar' AS alamat
 UNION ALL SELECT 'Budi','Madiun','081234567802','Jl. Melati'
 UNION ALL SELECT 'Citra','Surabaya','081234567803','Jl. Kenanga'
 UNION ALL SELECT 'Dewi','Ponorogo','081234567804','Jl. Anggrek'
 UNION ALL SELECT 'Eko','Malang','081234567805','Jl. Dahlia'
 UNION ALL SELECT 'Fani','Madiun','081234567806','Jl. Teratai'
 UNION ALL SELECT 'Gilang','Surabaya','081234567807','Jl. Kamboja'
 UNION ALL SELECT 'Hana','Kediri','081234567808','Jl. Flamboyan'
 UNION ALL SELECT 'Indra','Blitar','081234567809','Jl. Cempaka'
 UNION ALL SELECT 'Joko','Ponorogo','081234567810','Jl. Nusa'
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM tbl_pelanggan);

INSERT INTO tbl_produk(id_kategori,nama_produk,harga,stok,deskripsi,barcode)
SELECT seed.id_kategori,seed.nama_produk,seed.harga,seed.stok,seed.deskripsi,seed.barcode
FROM (
 SELECT 1 AS id_kategori,'Biskuit Cokelat' AS nama_produk,12000 AS harga,100 AS stok,'Biskuit kemasan' AS deskripsi,'KS-SEED-001' AS barcode
 UNION ALL SELECT 1,'Roti Tawar',16000,80,'Roti tawar','KS-SEED-002'
 UNION ALL SELECT 2,'Air Mineral',5000,200,'Air mineral botol','KS-SEED-003'
 UNION ALL SELECT 2,'Teh Botol',7000,150,'Minuman teh','KS-SEED-004'
 UNION ALL SELECT 3,'Buku Tulis',6000,100,'Buku tulis 38 lembar','KS-SEED-005'
 UNION ALL SELECT 3,'Pulpen',3500,200,'Pulpen tinta biru','KS-SEED-006'
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM tbl_produk);

CREATE OR REPLACE VIEW view_laporan_transaksi AS
SELECT t.id_transaksi,t.kode_transaksi,t.tanggal,p.nama_pelanggan,p.kota,
       t.total_belanja,t.diskon,t.pajak
FROM tbl_transaksi t JOIN tbl_pelanggan p ON p.id_pelanggan=t.id_pelanggan;

CREATE OR REPLACE VIEW view_total_belanja AS
SELECT p.id_pelanggan,p.nama_pelanggan,COUNT(t.id_transaksi) AS total_transaksi,
       COALESCE(SUM(t.total_belanja),0) AS total_belanja
FROM tbl_pelanggan p LEFT JOIN tbl_transaksi t ON t.id_pelanggan=p.id_pelanggan
GROUP BY p.id_pelanggan,p.nama_pelanggan;
