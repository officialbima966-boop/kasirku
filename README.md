# Sistem Informasi Penjualan Toko — PHP Native

Aplikasi pembelajaran berbasis PHP Native + MySQL/MariaDB untuk studi kasus jobsheet Advanced Database Programming.

## Modul
Dashboard, pelanggan, kategori, produk, transaksi, laporan, log aktivitas, dan halaman demonstrasi Advanced SQL.

## Teknologi
PHP 8+, PDO, MySQL/MariaDB, HTML, CSS, JavaScript. Tanpa framework PHP.

## Struktur
- `app/controllers`: pengendali alur aplikasi.
- `app/models`: akses data dengan PDO.
- `app/views`: tampilan per modul.
- `database`: instalasi skema dan materi SQL lanjutan.
- `public`: entry point dan aset publik.
- `docs`: panduan dan checklist.

Lihat `docs/panduan_instalasi.md` sebelum menjalankan aplikasi.

## Catatan implementasi
Proyek ini adalah starter project edukasi. Fitur CRUD inti, login, pencatatan transaksi, laporan, dan demonstrasi query disediakan. Lakukan pengujian lokal sebelum dipakai sebagai sistem produksi. Untuk penggunaan produksi, tambahkan manajemen user, ubah password default, otorisasi per role, rate limiting, dan audit keamanan.
