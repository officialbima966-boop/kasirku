<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar Pelanggan | <?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="login-body">
  <div class="login-container">
    <form class="login-card" method="post" action="<?= url('daftar') ?>">
      <div class="login-logo"><i class="fa-solid fa-id-card"></i> Member <?= APP_NAME ?></div>
      <h1>Buat Akun Pelanggan</h1>
      <p>Gunakan akun ini untuk melihat produk dan membuat pesanan.</p>

      <?php if($error): ?>
        <div class="shopee-alert error" role="alert">
          <i class="fa-solid fa-circle-exclamation"></i>
          <div class="alert-text"><?= e($error) ?></div>
        </div>
      <?php endif; ?>

      <?= csrf_field() ?>
      <label>Nama lengkap
        <input name="nama_pelanggan" required maxlength="100" autocomplete="name" value="<?= e($_POST['nama_pelanggan']??'') ?>">
      </label>
      <label>Username
        <input name="username" required minlength="3" maxlength="50" pattern="[A-Za-z0-9._-]+" autocomplete="username" value="<?= e($_POST['username']??'') ?>">
      </label>
      <label>Nomor HP (opsional)
        <input name="no_hp" maxlength="25" autocomplete="tel" value="<?= e($_POST['no_hp']??'') ?>">
      </label>
      <label>Password (minimal 8 karakter)
        <input type="password" name="password" required minlength="8" autocomplete="new-password">
      </label>
      <label>Konfirmasi password
        <input type="password" name="konfirmasi_password" required minlength="8" autocomplete="new-password">
      </label>
      <button type="submit" class="btn primary full" style="margin-top: 10px; padding: 12px;">
        <i class="fa-solid fa-user-plus"></i> Daftar
      </button>
      <p style="text-align: center; margin-top: 16px;">
        Sudah punya akun? <a href="<?= url('login') ?>">Masuk</a>
      </p>
    </form>
  </div>
</body>
</html>
