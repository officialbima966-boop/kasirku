-- Salin pelanggan dan ringkasan transaksi dari database toko ke db_toko.
-- Sumber tidak diubah; tabel pemetaan membuat migrasi aman dijalankan ulang.
USE db_toko;

ALTER TABLE tbl_transaksi
  ADD COLUMN IF NOT EXISTS metode_pembayaran VARCHAR(30) NOT NULL DEFAULT 'Tunai' AFTER total_belanja;
ALTER TABLE tbl_transaksi
  ADD COLUMN IF NOT EXISTS id_pesanan INT DEFAULT NULL AFTER metode_pembayaran,
  ADD COLUMN IF NOT EXISTS diskon_member_persen DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER id_pesanan;
ALTER TABLE tbl_pelanggan
  ADD COLUMN IF NOT EXISTS username VARCHAR(50) DEFAULT NULL AFTER alamat,
  ADD COLUMN IF NOT EXISTS password VARCHAR(255) DEFAULT NULL AFTER username,
  ADD COLUMN IF NOT EXISTS rfid_uid VARCHAR(64) DEFAULT NULL AFTER password;

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

CREATE TABLE IF NOT EXISTS migrasi_toko_pelanggan (
  id_pelanggan_toko INT NOT NULL PRIMARY KEY,
  id_pelanggan_db_toko INT NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS migrasi_toko_transaksi (
  id_transaksi_toko INT NOT NULL PRIMARY KEY,
  id_transaksi_db_toko INT NOT NULL UNIQUE
) ENGINE=InnoDB;

START TRANSACTION;

SET @base_pelanggan = (SELECT COALESCE(MAX(id_pelanggan), 0) FROM tbl_pelanggan);
INSERT INTO migrasi_toko_pelanggan (id_pelanggan_toko, id_pelanggan_db_toko)
SELECT p.id_pelanggan, @base_pelanggan + p.id_pelanggan
FROM toko.pelanggan p
LEFT JOIN migrasi_toko_pelanggan m ON m.id_pelanggan_toko = p.id_pelanggan
WHERE m.id_pelanggan_toko IS NULL;

INSERT INTO tbl_pelanggan (id_pelanggan, nama_pelanggan, kota, no_hp, alamat)
SELECT m.id_pelanggan_db_toko, p.nama_pelanggan, p.kota, p.no_telepon, p.alamat
FROM toko.pelanggan p
JOIN migrasi_toko_pelanggan m ON m.id_pelanggan_toko = p.id_pelanggan
LEFT JOIN tbl_pelanggan target ON target.id_pelanggan = m.id_pelanggan_db_toko
WHERE target.id_pelanggan IS NULL;

SET @base_transaksi = (SELECT COALESCE(MAX(id_transaksi), 0) FROM tbl_transaksi);
INSERT INTO migrasi_toko_transaksi (id_transaksi_toko, id_transaksi_db_toko)
SELECT t.id_transaksi, @base_transaksi + t.id_transaksi
FROM toko.transaksi t
LEFT JOIN migrasi_toko_transaksi m ON m.id_transaksi_toko = t.id_transaksi
WHERE m.id_transaksi_toko IS NULL;

INSERT INTO tbl_transaksi (
  id_transaksi, kode_transaksi, tanggal, id_pelanggan, total_belanja,
  diskon, pajak, metode_pembayaran, uang_dibayar, kembalian
)
SELECT
  mt.id_transaksi_db_toko,
  CONCAT('TOKO-LEGACY-', LPAD(t.id_transaksi, 10, '0')),
  t.tanggal_transaksi,
  mp.id_pelanggan_db_toko,
  t.total_belanja,
  0,
  0,
  t.metode_pembayaran,
  t.total_belanja,
  0
FROM toko.transaksi t
JOIN migrasi_toko_transaksi mt ON mt.id_transaksi_toko = t.id_transaksi
JOIN migrasi_toko_pelanggan mp ON mp.id_pelanggan_toko = t.id_pelanggan
LEFT JOIN tbl_transaksi target ON target.id_transaksi = mt.id_transaksi_db_toko
WHERE target.id_transaksi IS NULL;

COMMIT;

INSERT IGNORE INTO tbl_user (nama, username, password, role, status)
VALUES ('Kasir', 'kasir', CONCAT(CHAR(36), '2y', CHAR(36), '10', CHAR(36), 'i/ZmGcw46nEnY1ixqGVpcuMj4PFoBEAvy3PxEJXuPlvy5vZmHUEB2'), 'kasir', 'aktif');
