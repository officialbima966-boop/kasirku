<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>404 - Halaman Tidak Ditemukan | <?= APP_NAME ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f5f5f5;">
  <div style="text-align: center; background: #fff; padding: 48px; border-radius: var(--radius-md); box-shadow: var(--shadow-md); max-width: 480px; width: 90%;">
    <div style="font-size: 72px; color: var(--shopee-primary); margin-bottom: 16px;">
      <i class="fa-solid fa-basket-shopping"></i>
    </div>
    <h1 style="font-size: 26px; font-weight: 800; margin: 0 0 10px;">Halaman Tidak Ditemukan</h1>
    <p style="color: var(--text-muted); margin: 0 0 24px;">Halaman yang Anda tuju mungkin telah dipindahkan atau tautan salah.</p>
    <a href="<?= url('dashboard') ?>" class="btn primary">
      <i class="fa-solid fa-house"></i> Kembali ke Dashboard
    </a>
  </div>
</body>
</html>
