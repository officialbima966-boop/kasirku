<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | <?= APP_NAME ?> — Shopee POS</title>
  <meta name="theme-color" content="#ee4d2d">
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ee4d2d'><path d='M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3h-6c0-1.66 1.34-3 3-3zm7 17H5V8h14v12zm-7-8c-1.66 0-3-1.34-3-3H7c0 2.76 2.24 5 5 5s5-2.24 5-5h-2c0 1.66-1.34 3-3 3z'/></svg>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="login-body">
  <div class="login-container">
    <div class="login-banner-side">
      <div class="login-ill-icon">
        <i class="fa-solid fa-bag-shopping"></i>
      </div>
      <h2 class="login-banner-title">KasirKu Shopee POS</h2>
      <p class="login-banner-desc">Sistem Point of Sale & Manajemen Toko Modern dengan antarmuka Shopee yang menarik pembeli.</p>
      
      <div class="login-features-list">
        <div class="login-feature-item">
          <i class="fa-solid fa-circle-check"></i>
          <span>Katalog Produk Etalase Shopee</span>
        </div>
        <div class="login-feature-item">
          <i class="fa-solid fa-circle-check"></i>
          <span>Kasir POS Interaktif & Cepat</span>
        </div>
        <div class="login-feature-item">
          <i class="fa-solid fa-circle-check"></i>
          <span>Laporan Penjualan & Diskon Otomatis</span>
        </div>
      </div>
    </div>

    <form class="login-card" method="post" action="">
      <div class="login-logo">
        <i class="fa-solid fa-bag-shopping"></i> KasirKu
      </div>
      <h1>Masuk ke Akun</h1>
      <p>Silakan masuk untuk mengelola toko & kasir</p>

      <?php if(isset($error) && $error): ?>
        <div class="shopee-alert error">
          <i class="fa-solid fa-circle-exclamation"></i>
          <div class="alert-text"><?= e($error) ?></div>
        </div>
      <?php endif; ?>

      <?= csrf_field() ?>

      <label>
        Username
        <input type="text" name="username" required autofocus placeholder="Masukkan username (admin)" autocomplete="username">
      </label>

      <label>
        Password
        <input type="password" name="password" required placeholder="Masukkan password" autocomplete="current-password">
      </label>

      <button type="submit" class="btn primary full" style="margin-top: 10px; padding: 12px;">
        <i class="fa-solid fa-right-to-bracket"></i> Masuk Sekarang
      </button>

      <div class="login-hint-box">
        <i class="fa-solid fa-circle-info"></i> Login Default: <strong>admin</strong> / <strong>password</strong>
      </div>
    </form>
  </div>
</body>
</html>
