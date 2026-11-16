<?php require __DIR__.'/header.php'; ?>
<div class="app-shell">
  <?php require __DIR__.'/sidebar.php'; ?>
  <main class="main">
    <?php require __DIR__.'/navbar.php'; ?>
    <div class="shopee-sub-header">
      <div class="sub-header-container">
        <span class="sub-promo-item"><i class="fa-solid fa-bolt"></i> <strong>Flash Sale KasirKu</strong> Diskon s/d 50%</span>
        <span class="sub-promo-item"><i class="fa-solid fa-award"></i> <strong>100% Produk Asli</strong> Jaminan Kualitas</span>
        <span class="sub-promo-item"><i class="fa-solid fa-coins"></i> <strong>Shopee Coins</strong> Dapatkan Cashback Belanja</span>
      </div>
    </div>
    <section class="content">
      <?php if($m = flash('success')): ?>
        <div class="shopee-alert success">
          <i class="fa-solid fa-circle-check"></i>
          <div class="alert-text"><?= e($m) ?></div>
          <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
      <?php endif; ?>
      <?php if($m = flash('error')): ?>
        <div class="shopee-alert error">
          <i class="fa-solid fa-circle-exclamation"></i>
          <div class="alert-text"><?= e($m) ?></div>
          <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
      <?php endif; ?>
