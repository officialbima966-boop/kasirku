<?php require APP_PATH.'/views/layouts/main.php'; ?>

<!-- Shopee Flash Sale & Promo Banner -->
<div class="shopee-promo-banner">
  <div class="banner-left">
    <div class="banner-badge">
      <i class="fa-solid fa-bolt"></i> FLASH SALE KASIRKU HARI INI
    </div>
    <h2 class="banner-title">Super Promo Toko & Diskon Kilat Pelanggan!</h2>
    <p class="banner-subtitle">Tingkatkan omzet penjualan toko Anda dengan fitur Point of Sale modern dan etalase produk bergaya Shopee yang menarik pembeli.</p>
    <div class="banner-vouchers">
      <span class="voucher-pill"><i class="fa-solid fa-ticket"></i> DISKON 10%</span>
      <span class="voucher-pill"><i class="fa-solid fa-truck-fast"></i> GRATIS ONGKIR RP 0</span>
      <span class="voucher-pill"><i class="fa-solid fa-coins"></i> CASHBACK 5%</span>
    </div>
  </div>
  <a href="<?= url('transaksi_tambah') ?>" class="banner-cta">
    <i class="fa-solid fa-cash-register"></i> Buka Kasir Sekarang
  </a>
</div>

<!-- Stats / Metrics Section -->
<div class="stats">
  <div class="stat">
    <div class="stat-icon-wrap">
      <i class="fa-solid fa-money-bill-wave"></i>
    </div>
    <div class="stat-body">
      <span>Total Penjualan (Omzet)</span>
      <b style="color: var(--shopee-primary);"><?= rupiah($stats['omzet']) ?></b>
      <small><i class="fa-solid fa-circle-check" style="color: var(--shopee-green);"></i> Akumulasi transaksi toko</small>
    </div>
  </div>

  <div class="stat stat-blue">
    <div class="stat-icon-wrap">
      <i class="fa-solid fa-receipt"></i>
    </div>
    <div class="stat-body">
      <span>Total Pesanan Selesai</span>
      <b><?= number_format($stats['transaksi']) ?></b>
      <small><i class="fa-solid fa-bag-shopping"></i> Transaksi berhasil</small>
    </div>
  </div>

  <div class="stat stat-green">
    <div class="stat-icon-wrap">
      <i class="fa-solid fa-users"></i>
    </div>
    <div class="stat-body">
      <span>Pelanggan Terdaftar</span>
      <b><?= number_format($stats['pelanggan']) ?></b>
      <small><i class="fa-solid fa-user-check"></i> Member setia toko</small>
    </div>
  </div>

  <div class="stat stat-purple">
    <div class="stat-icon-wrap">
      <i class="fa-solid fa-boxes-stacked"></i>
    </div>
    <div class="stat-body">
      <span>Produk dalam Katalog</span>
      <b><?= number_format($stats['produk']) ?></b>
      <small><i class="fa-solid fa-tag"></i> Item siap jual</small>
    </div>
  </div>
</div>

<!-- Rekomendasi Produk Terlaris (Shopee Style Grid) -->
<?php if(!empty($topProducts)): ?>
<div class="panel">
  <div class="panel-head">
    <h2><i class="fa-solid fa-fire" style="color: var(--shopee-primary);"></i> Rekomendasi Produk Unggulan</h2>
    <a href="<?= url('produk') ?>" class="btn secondary sm">Lihat Semua Etalase →</a>
  </div>
  <div class="shopee-product-grid">
    <?php foreach($topProducts as $idx => $prod): 
      $discounts = [15, 20, 10, 25];
      $discPct = $discounts[$idx % 4];
      $oriPrice = $prod['harga'] / (1 - ($discPct / 100));
      $icons = ['Makanan' => 'fa-utensils', 'Minuman' => 'fa-mug-hot', 'Perlengkapan' => 'fa-box-archive'];
      $catIcon = $icons[$prod['nama_kategori']] ?? 'fa-bag-shopping';
    ?>
    <div class="shopee-product-card">
      <div class="product-thumb-wrap">
        <span class="card-badge-mall">STAR+</span>
        <span class="card-badge-discount">-<?= $discPct ?>%</span>
        <i class="fa-solid <?= $catIcon ?> product-thumb-icon"></i>
      </div>
      <div class="product-card-body">
        <span class="product-cat-pill"><?= e($prod['nama_kategori']) ?></span>
        <div class="product-card-title"><?= e($prod['nama_produk']) ?></div>
        <div class="product-card-desc"><?= e($prod['deskripsi'] ?: 'Kualitas terbaik, 100% original.') ?></div>
        <div class="product-price-row">
          <span class="original-price"><?= rupiah($oriPrice) ?></span>
          <span class="shopee-price"><?= rupiah($prod['harga']) ?></span>
        </div>
        <div class="product-meta-row">
          <span class="product-rating"><i class="fa-solid fa-star"></i> 4.9</span>
          <span class="product-sold">120+ Terjual</span>
          <span class="stock-tag <?= $prod['stok'] <= 10 ? 'low' : '' ?>">Stok: <?= $prod['stok'] ?></span>
        </div>
        <a href="<?= url('transaksi_tambah') ?>" class="btn-add-cart">
          <i class="fa-solid fa-cart-shopping"></i> Beli di Kasir
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- Recent Transactions Panel -->
<div class="panel">
  <div class="panel-head">
    <h2><i class="fa-solid fa-clock-rotate-left" style="color: var(--shopee-primary);"></i> Pesanan & Transaksi Terbaru</h2>
    <a href="<?= url('transaksi') ?>" class="btn secondary sm">Lihat Semua Pesanan →</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Kode Transaksi</th>
          <th>Tanggal</th>
          <th>Pelanggan</th>
          <th>Status</th>
          <th>Total Belanja</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($recent as $r): ?>
        <tr>
          <td>
            <strong><?= e($r['kode_transaksi']) ?></strong>
          </td>
          <td><i class="fa-regular fa-calendar" style="color: var(--text-light); margin-right: 4px;"></i> <?= e($r['tanggal']) ?></td>
          <td>
            <div style="font-weight: 700;"><?= e($r['nama_pelanggan']) ?></div>
            <?php if(!empty($r['kota'])): ?>
              <small style="color: var(--text-muted);"><i class="fa-solid fa-location-dot" style="font-size: 10px;"></i> <?= e($r['kota']) ?></small>
            <?php endif; ?>
          </td>
          <td>
            <span class="badge-status success"><i class="fa-solid fa-check"></i> Selesai (Lunas)</span>
          </td>
          <td>
            <strong style="color: var(--shopee-primary); font-size: 14.5px;"><?= rupiah($r['total_belanja']) ?></strong>
          </td>
          <td>
            <a href="<?= url('transaksi_detail', ['id' => $r['id_transaksi']]) ?>" class="btn outline-primary sm">
              <i class="fa-regular fa-eye"></i> Struk
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(!$recent): ?>
          <tr><td colspan="6" class="empty">Belum ada transaksi tersimpan.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require APP_PATH.'/views/layouts/footer.php'; ?>
