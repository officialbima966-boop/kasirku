USE db_toko;
DROP TRIGGER IF EXISTS after_insert_pelanggan;
DROP TRIGGER IF EXISTS after_update_pelanggan;
DROP TRIGGER IF EXISTS before_insert_transaksi;
DROP TRIGGER IF EXISTS after_insert_detail;
DROP TRIGGER IF EXISTS after_insert_detail_log;
DELIMITER //
CREATE TRIGGER after_insert_pelanggan AFTER INSERT ON tbl_pelanggan FOR EACH ROW
BEGIN INSERT INTO tbl_log(aktivitas,deskripsi) VALUES('INSERT PELANGGAN',CONCAT('Pelanggan baru: ',NEW.nama_pelanggan)); END//
CREATE TRIGGER after_update_pelanggan AFTER UPDATE ON tbl_pelanggan FOR EACH ROW
BEGIN INSERT INTO tbl_log(aktivitas,deskripsi) VALUES('UPDATE PELANGGAN',CONCAT('Perubahan ',OLD.nama_pelanggan,' menjadi ',NEW.nama_pelanggan)); END//
CREATE TRIGGER before_insert_transaksi BEFORE INSERT ON tbl_transaksi FOR EACH ROW
BEGIN IF NEW.total_belanja<0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Total transaksi tidak boleh negatif'; END IF; END//
CREATE TRIGGER after_insert_detail_log AFTER INSERT ON tbl_detail_transaksi FOR EACH ROW
BEGIN
 INSERT INTO tbl_log(aktivitas,deskripsi) VALUES('DETAIL TRANSAKSI',CONCAT('Produk ID ',NEW.id_produk,' terjual ',NEW.jumlah));
END//
DELIMITER ;
