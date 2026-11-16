# Panduan Instalasi

1. Install XAMPP dan jalankan Apache serta MySQL.
2. Ekstrak folder proyek ke `C:/xampp/htdocs/`.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih tab **Import**, lalu import `database/install.sql`. File ini membuat `db_toko`, tabel, data awal, serta view laporan tanpa menghapus data yang sudah ada. Import ulang file ini untuk menambahkan kolom foto produk pada database yang sudah terpasang.
5. Opsional: import `database/procedures.sql`, `functions.sql`, `triggers.sql`, dan `indexes.sql` satu per satu untuk materi SQL lanjutan. `triggers.sql` hanya mencatat log detail transaksi; stok dikurangi oleh aplikasi dalam transaksi database. `install.sql` menghapus trigger stok lama agar tidak terjadi pengurangan ganda. `views.sql` tidak wajib karena view sudah dibuat oleh `install.sql`.
6. Opsional: import `database/seeds/demo_data.sql` untuk menambahkan transaksi contoh. Jalankan hanya pada database yang belum berisi transaksi demo.
7. Periksa `app/config/app.php`; untuk folder proyek ini, URL aplikasi adalah `http://localhost/kasirku/public/`.
8. Periksa `app/config/database.php`; nilai bawaan XAMPP adalah host `localhost`, database `db_toko`, username `root`, dan password kosong. Sesuaikan jika konfigurasi MySQL berbeda.
9. Buka `http://localhost/kasirku/public/`.

Login awal: `admin` / `password`. Segera ganti password hash pada tabel `tbl_user` untuk penggunaan nyata.

Catatan: import file SQL melalui phpMyAdmin satu per satu karena perintah `DELIMITER` pada stored routine perlu diproses sebagai script SQL. Aplikasi tidak membuat database secara otomatis; bila database belum ada atau kredensial salah, halaman akan menampilkan petunjuk konfigurasi.
