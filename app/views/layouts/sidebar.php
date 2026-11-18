<?php
$currentPage = $_GET['page'] ?? 'dashboard';
$userRole = current_user()['role'] ?? 'admin';
?>
<aside class="sidebar" id="appSidebar">
  <div class="sidebar-header">
    <div class="brand-shopee">
      <div class="brand-icon">
        <i class="fa-solid fa-bag-shopping"></i>
      </div>
      <div class="brand-text">
        <span class="brand-name">KasirKu</span>
        <span class="brand-badge"><i class="fa-solid fa-circle-check"></i> Shopee Official</span>
      </div>
    </div>
    <button class="sidebar-close-btn" id="sidebarCloseBtn" type="button" aria-label="Tutup Menu">
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>

  <div class="store-profile-card">
    <div class="store-avatar">
      <i class="fa-solid fa-shop"></i>
    </div>
    <div class="store-info">
      <div class="store-title"><?= APP_NAME ?> Store</div>
      <div class="store-rating">
        <span class="rating-stars"><i class="fa-solid fa-star"></i> 4.9</span>
        <span class="rating-badge">Star+</span>
        <span class="store-online"><i class="fa-solid fa-circle"></i> Online</span>
      </div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <?php if($userRole==='pelanggan'): ?>
      <div class="nav-section-title">AKUN PELANGGAN</div>
      <a href="<?= url('member') ?>" class="nav-item <?= $currentPage==='member'?'active':'' ?>">
        <span class="nav-icon"><i class="fa-solid fa-store"></i></span>
        <span class="nav-label">Belanja Member</span>
      </a>
      <a href="<?= url('member_pesanan') ?>" class="nav-item <?= $currentPage==='member_pesanan'?'active':'' ?>">
        <span class="nav-icon"><i class="fa-solid fa-receipt"></i></span>
        <span class="nav-label">Pesanan Saya</span>
      </a>
    <?php elseif($userRole==='kasir'): ?>
      <div class="nav-section-title">MENU KASIR</div>
      <a href="<?= url('transaksi_tambah') ?>" class="nav-item nav-item-highlight <?= $currentPage === 'transaksi_tambah' ? 'active' : '' ?>">
        <span class="nav-icon"><i class="fa-solid fa-cash-register"></i></span>
        <span class="nav-label">Kasir POS (Transaksi)</span>
        <span class="nav-tag hot">HOT</span>
      </a>
      <a href="<?= url('kasir_pesanan') ?>" class="nav-item <?= in_array($currentPage,['kasir_pesanan','kasir_bayar'])?'active':'' ?>">
        <span class="nav-icon"><i class="fa-solid fa-clipboard-list"></i></span>
        <span class="nav-label">Antrean Pembayaran</span>
      </a>
      <a href="<?= url('transaksi') ?>" class="nav-item <?= in_array($currentPage,['transaksi','transaksi_detail'])?'active':'' ?>">
        <span class="nav-icon"><i class="fa-solid fa-receipt"></i></span>
        <span class="nav-label">Riwayat Transaksi</span>
      </a>
    <?php else: ?>
    <div class="nav-section-title">MENU UTAMA</div>
    
    <a href="<?= url('dashboard') ?>" class="nav-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fa-solid fa-house"></i></span>
      <span class="nav-label">Dashboard Toko</span>
    </a>

    <a href="<?= url('transaksi_tambah') ?>" class="nav-item nav-item-highlight <?= $currentPage === 'transaksi_tambah' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fa-solid fa-cash-register"></i></span>
      <span class="nav-label">Kasir POS (Checkout)</span>
      <span class="nav-tag hot">HOT</span>
    </a>

    <a href="<?= url('transaksi') ?>" class="nav-item <?= in_array($currentPage, ['transaksi', 'transaksi_detail']) ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fa-solid fa-receipt"></i></span>
      <span class="nav-label">Riwayat Transaksi</span>
    </a>

    <div class="nav-section-title">KATALOG & PRODUK</div>

    <a href="<?= url('produk') ?>" class="nav-item <?= $currentPage === 'produk' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fa-solid fa-boxes-stacked"></i></span>
      <span class="nav-label">Katalog Produk</span>
      <span class="nav-tag promo">Etalase</span>
    </a>

    <a href="<?= url('kategori') ?>" class="nav-item <?= $currentPage === 'kategori' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fa-solid fa-tags"></i></span>
      <span class="nav-label">Kategori Produk</span>
    </a>

    <a href="<?= url('pelanggan') ?>" class="nav-item <?= $currentPage === 'pelanggan' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fa-solid fa-users"></i></span>
      <span class="nav-label">Data Pelanggan</span>
    </a>

    <div class="nav-section-title">ANALITIK & SISTEM</div>

    <a href="<?= url('laporan') ?>" class="nav-item <?= $currentPage === 'laporan' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span>
      <span class="nav-label">Laporan Penjualan</span>
    </a>

    <a href="<?= url('database') ?>" class="nav-item <?= $currentPage === 'database' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fa-solid fa-database"></i></span>
      <span class="nav-label">Advanced SQL</span>
    </a>

    <a href="<?= url('log') ?>" class="nav-item <?= $currentPage === 'log' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
      <span class="nav-label">Log Aktivitas</span>
    </a>
    <?php endif; ?>
  </nav>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar-circle">
        <?= strtoupper(substr(current_user()['nama'] ?? 'A', 0, 1)) ?>
      </div>
      <div class="user-details">
        <div class="user-name"><?= e(current_user()['nama'] ?? 'Administrator') ?></div>
        <div class="user-role"><?= strtoupper(e(current_user()['role'] ?? 'ADMIN')) ?></div>
      </div>
      <a href="<?= url('logout') ?>" class="logout-btn" title="Keluar / Logout" onclick="return confirm('Keluar dari aplikasi?')">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
      </a>
    </div>
  </div>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
