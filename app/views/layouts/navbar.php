<?php
$userRole=current_user()['role']??'admin';
$actionPage=$userRole==='pelanggan'?'member':'transaksi_tambah';
$actionLabel=$userRole==='pelanggan'?'Belanja':'Transaksi Baru';
?>
<header class="topbar">
  <div class="topbar-left">
    <button type="button" class="sidebar-toggle-btn" id="sidebarOpenBtn" aria-label="Buka Menu">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div class="topbar-title-wrap">
      <div class="topbar-badge">
        <i class="fa-solid fa-shield-halved"></i> Shopee Guarantee
      </div>
      <h1 class="topbar-page-title"><?= e($title ?? 'KasirKu Shopee') ?></h1>
    </div>
  </div>

  <div class="topbar-center">
    <div class="shopee-search-bar">
      <i class="fa-solid fa-magnifying-glass search-icon"></i>
      <input type="text" id="globalPageSearch" placeholder="Cari nama produk, kode transaksi, atau pelanggan..." autocomplete="off">
      <button type="button" class="search-btn"><i class="fa-solid fa-arrow-right"></i></button>
    </div>
  </div>

  <div class="topbar-right">
    <div class="topbar-promo-pill">
      <i class="fa-solid fa-truck-fast"></i>
      <span>Gratis Ongkir XTRA</span>
    </div>

    <a href="<?= url($actionPage) ?>" class="btn-shopee-header">
      <i class="fa-solid fa-cart-plus"></i>
      <span><?= e($actionLabel) ?></span>
    </a>

    <div class="topbar-user-chip">
      <div class="chip-avatar">
        <?= strtoupper(substr(current_user()['nama'] ?? 'A', 0, 1)) ?>
      </div>
      <div class="chip-text">
        <span class="chip-name"><?= e(current_user()['nama'] ?? 'Admin') ?></span>
        <span class="chip-status"><i class="fa-solid fa-circle"></i> Online</span>
      </div>
    </div>
  </div>
</header>
