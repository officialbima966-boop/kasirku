# Panduan Instalasi

1. Install XAMPP dan jalankan Apache serta MySQL.
2. Ekstrak folder proyek ke `C:/xampp/htdocs/`.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih tab **Import**, lalu import `database/install.sql`. File ini membuat `db_toko`, tabel, data awal, serta view laporan tanpa menghapus data yang sudah ada. Import ulang file ini untuk menambahkan kolom foto produk pada database yang sudah terpasang.
5. Untuk memindahkan data pelanggan dan ringkasan transaksi dari database lama bernama `toko`, import `database/migrate_toko_to_db_toko.sql` setelah `install.sql`. Script mempertahankan database sumber dan aman dijalankan ulang. Produk/rincian transaksi tidak dipindahkan karena tidak tersedia pada struktur sumber.
6. Opsional: import `database/procedures.sql`, `functions.sql`, `triggers.sql`, dan `indexes.sql` satu per satu untuk materi SQL lanjutan. `triggers.sql` hanya mencatat log detail transaksi; stok dikurangi oleh aplikasi dalam transaksi database. `install.sql` menghapus trigger stok lama agar tidak terjadi pengurangan ganda. `views.sql` tidak wajib karena view sudah dibuat oleh `install.sql`.
7. Opsional: import `database/seeds/demo_data.sql` untuk menambahkan transaksi contoh. Jalankan hanya pada database yang belum berisi transaksi demo.
8. Periksa `app/config/app.php`; untuk folder proyek ini, URL aplikasi adalah `http://localhost/kasirku/public/`.
9. Periksa `app/config/database.php`; nilai bawaan XAMPP adalah host `localhost`, database `db_toko`, username `root`, dan password kosong. Sesuaikan jika konfigurasi MySQL berbeda.
10. Buka `http://localhost/kasirku/public/`.

Login awal: Admin `admin` / `password`; Kasir `kasir` / `kasir123`. Import ulang `install.sql` atau jalankan kembali `migrate_toko_to_db_toko.sql` untuk menambahkan akun kasir pada database yang sudah ada. Segera ganti password default untuk penggunaan nyata.

Catatan: import file SQL melalui phpMyAdmin satu per satu karena perintah `DELIMITER` pada stored routine perlu diproses sebagai script SQL. Aplikasi tidak membuat database secara otomatis; bila database belum ada atau kredensial salah, halaman akan menampilkan petunjuk konfigurasi.
