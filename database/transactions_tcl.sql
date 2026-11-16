USE db_toko;
-- Contoh COMMIT: perubahan disimpan permanen
START TRANSACTION;
UPDATE tbl_produk SET stok=stok+5 WHERE id_produk=1;
COMMIT;

-- Contoh ROLLBACK: perubahan dibatalkan
START TRANSACTION;
UPDATE tbl_produk SET stok=stok-2 WHERE id_produk=1;
ROLLBACK;
