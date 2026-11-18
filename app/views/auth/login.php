<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | <?= APP_NAME ?> — Shopee POS & Member</title>
  <meta name="theme-color" content="#ee4d2d">
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ee4d2d'><path d='M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3h-6c0-1.66 1.34-3 3-3zm7 17H5V8h14v12zm-7-8c-1.66 0-3-1.34-3-3H7c0 2.76 2.24 5 5 5s5-2.24 5-5h-2c0 1.66-1.34 3-3 3z'/></svg>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <style>
    .role-tabs {
      display: flex;
      background: #f1f3f6;
      border-radius: var(--radius-sm);
      padding: 4px;
      margin-bottom: 20px;
      gap: 4px;
    }
    .role-tab-btn {
      flex: 1;
      border: none;
      background: transparent;
      padding: 9px 6px;
      font-size: 12px;
      font-weight: 700;
      color: var(--text-muted);
      border-radius: var(--radius-xs);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.15s ease;
    }
    .role-tab-btn.active {
      background: #fff;
      color: var(--shopee-primary);
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .quick-account-chips {
      display: flex;
      gap: 6px;
      margin-top: 10px;
      flex-wrap: wrap;
    }
    .account-chip {
      background: #fff;
      border: 1px dashed var(--shopee-primary);
      color: var(--shopee-primary);
      font-size: 11px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: var(--radius-full);
      cursor: pointer;
      transition: all 0.15s ease;
    }
    .account-chip:hover {
      background: var(--shopee-primary);
      color: #fff;
    }
    .rfid-preview-badge {
      background: linear-gradient(135deg, #ffb800 0%, #ff8c00 100%);
      color: #fff;
      font-weight: 800;
      font-size: 11px;
      padding: 4px 12px;
      border-radius: var(--radius-full);
      display: inline-flex;
      align-items: center;
      gap: 6px;
      box-shadow: 0 2px 8px rgba(255, 140, 0, 0.3);
      margin-top: 8px;
    }
  </style>
</head>
<body class="login-body">
  <div class="login-container">
    <!-- Left Banner -->
    <div class="login-banner-side">
      <div class="login-ill-icon">
        <i class="fa-solid fa-bag-shopping"></i>
      </div>
      <h2 class="login-banner-title">KasirKu Shopee POS</h2>
      <p class="login-banner-desc">Sistem Kasir & Belanja Pelanggan dengan integrasi <strong>Kartu Member RFID Diskon Otomatis</strong>.</p>
      
      <div class="rfid-preview-badge">
        <i class="fa-solid fa-id-card"></i> RFID Member Card Ready
      </div>

      <div class="login-features-list" style="margin-top: 24px;">
        <div class="login-feature-item">
          <i class="fa-solid fa-circle-check"></i>
          <span><strong>Pelanggan:</strong> Pesan produk dari etalase & miliki Kartu RFID</span>
        </div>
        <div class="login-feature-item">
          <i class="fa-solid fa-circle-check"></i>
          <span><strong>Kasir:</strong> Scan Kartu RFID member & terima pembayaran</span>
        </div>
        <div class="login-feature-item">
          <i class="fa-solid fa-circle-check"></i>
          <span><strong>Admin:</strong> Kelola data toko, produk, kategori & laporan</span>
        </div>
      </div>
    </div>

    <!-- Right Login Card -->
    <form class="login-card" method="post" action="" id="loginForm">
      <div class="login-logo">
        <i class="fa-solid fa-bag-shopping"></i> KasirKu
      </div>
      <h1 id="loginCardTitle">Masuk ke Akun</h1>
      <p id="loginCardDesc">Pilih jenis akun atau masukkan kredensial Anda</p>

      <!-- Role Selector Tabs -->
      <div class="role-tabs">
        <button type="button" class="role-tab-btn active" id="tabAdmin" onclick="selectRole('admin')">
          <i class="fa-solid fa-user-shield"></i> Admin
        </button>
        <button type="button" class="role-tab-btn" id="tabKasir" onclick="selectRole('kasir')">
          <i class="fa-solid fa-cash-register"></i> Kasir
        </button>
        <button type="button" class="role-tab-btn" id="tabMember" onclick="selectRole('member')">
          <i class="fa-solid fa-id-card"></i> Pelanggan
        </button>
      </div>

      <?php if(isset($error) && $error): ?>
        <div class="shopee-alert error">
          <i class="fa-solid fa-circle-exclamation"></i>
          <div class="alert-text"><?= e($error) ?></div>
        </div>
      <?php endif; ?>

      <?= csrf_field() ?>

      <label>
        Username
        <input type="text" name="username" id="inputUsername" required autofocus placeholder="Masukkan username" autocomplete="username">
      </label>

      <label>
        Password
        <input type="password" name="password" id="inputPassword" required placeholder="Masukkan password" autocomplete="current-password">
      </label>

      <button type="submit" class="btn primary full" style="margin-top: 10px; padding: 12px; font-size: 14px;">
        <i class="fa-solid fa-right-to-bracket"></i> Masuk Sekarang
      </button>

      <!-- Quick test helper chips -->
      <div style="margin-top: 18px;">
        <span style="font-size: 11.5px; color: var(--text-muted); font-weight: 700;">Isi Cepat Akun Demo:</span>
        <div class="quick-account-chips">
          <button type="button" class="account-chip" onclick="fillCreds('admin', 'password', 'admin')">
            <i class="fa-solid fa-shield"></i> Admin
          </button>
          <button type="button" class="account-chip" onclick="fillCreds('kasir', 'kasir', 'kasir')">
            <i class="fa-solid fa-calculator"></i> Kasir
          </button>
          <button type="button" class="account-chip" onclick="fillCreds('member', 'password', 'member')">
            <i class="fa-solid fa-user-tag"></i> Pelanggan (Andi)
          </button>
        </div>
      </div>

      <div class="login-hint-box" id="roleHintBox" style="margin-top: 14px;">
        <i class="fa-solid fa-circle-info"></i> Login Admin: <strong>admin</strong> / <strong>password</strong>
      </div>

      <p style="text-align: center; margin-top: 16px; font-size: 13px;">
        Belum punya akun pelanggan? <a href="<?= url('daftar') ?>" style="font-weight: 700;">Daftar Member Baru</a>
      </p>
    </form>
  </div>

  <script>
    function selectRole(role) {
      document.querySelectorAll('.role-tab-btn').forEach(b => b.classList.remove('active'));
      const uInput = document.getElementById('inputUsername');
      const pInput = document.getElementById('inputPassword');
      const hint = document.getElementById('roleHintBox');
      const title = document.getElementById('loginCardTitle');
      const desc = document.getElementById('loginCardDesc');

      if (role === 'admin') {
        document.getElementById('tabAdmin').classList.add('active');
        title.textContent = 'Masuk sebagai Admin';
        desc.textContent = 'Kelola dashboard, produk, pelanggan, dan laporan toko';
        uInput.placeholder = 'admin';
        hint.innerHTML = '<i class="fa-solid fa-circle-info"></i> Akun Admin: <strong>admin</strong> / <strong>password</strong>';
      } else if (role === 'kasir') {
        document.getElementById('tabKasir').classList.add('active');
        title.textContent = 'Masuk sebagai Kasir';
        desc.textContent = 'Kelola antrean pesanan & scan kartu RFID pelanggan';
        uInput.placeholder = 'kasir';
        hint.innerHTML = '<i class="fa-solid fa-circle-info"></i> Akun Kasir: <strong>kasir</strong> / <strong>kasir</strong> (atau password)';
      } else if (role === 'member') {
        document.getElementById('tabMember').classList.add('active');
        title.textContent = 'Masuk sebagai Pelanggan';
        desc.textContent = 'Belanja produk, pesan ke kasir, & gunakan Kartu RFID';
        uInput.placeholder = 'member';
        hint.innerHTML = '<i class="fa-solid fa-circle-info"></i> Akun Pelanggan: <strong>member</strong> / <strong>password</strong>';
      }
    }

    function fillCreds(u, p, role) {
      selectRole(role);
      document.getElementById('inputUsername').value = u;
      document.getElementById('inputPassword').value = p;
    }
  </script>
</body>
</html>
