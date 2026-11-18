# Struktur Database

Database aplikasi bernama `db_toko` dan dibuat melalui `database/install.sql`.

| Tabel | Fungsi |
|---|---|
| `tbl_user` | Akun login, password hash, role, dan status pengguna. |
| `tbl_pelanggan` | Identitas dan kontak pelanggan. |
| `tbl_kategori` | Daftar kategori produk. |
| `tbl_produk` | Katalog produk, harga, stok, kategori, dan nama file foto. |
| `tbl_transaksi` | Informasi umum penjualan: kode, tanggal, pelanggan, diskon, pajak, metode pembayaran, uang dibayar, kembalian, dan total. |
| `tbl_detail_transaksi` | Produk, jumlah, harga saat transaksi, dan subtotal per transaksi. |
| `tbl_log` | Catatan aktivitas aplikasi/database. |

Relasi: `tbl_kategori` 1:N `tbl_produk`; `tbl_pelanggan` 1:N `tbl_transaksi`; `tbl_transaksi` 1:N `tbl_detail_transaksi`; `tbl_produk` 1:N `tbl_detail_transaksi`.

Produk dan pelanggan yang sudah dipakai dalam transaksi tidak dapat dihapus agar riwayat penjualan tetap utuh. Transaksi lama dari database `toko` dapat dipindahkan dengan `database/migrate_toko_to_db_toko.sql`; database sumber tidak diubah. Sumber tersebut hanya menyimpan ringkasan transaksi, bukan rincian produk, sehingga rincian struk lama tidak dapat dipulihkan.

View `view_laporan_transaksi` dan `view_total_belanja` dipakai oleh halaman fitur database. Koneksi PDO di `app/config/database.php` memakai konfigurasi XAMPP bawaan (`localhost`, `db_toko`, `root`, tanpa password); sesuaikan di sana bila konfigurasi MySQL berbeda.

Foto produk wajib diunggah saat menambahkan produk. Format yang diterima adalah JPG, PNG, atau WEBP dengan ukuran maksimal 5 MB. File disimpan di `public/uploads/produk/`, sedangkan nama file disimpan pada kolom `tbl_produk.foto`.
