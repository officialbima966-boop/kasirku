<?php require APP_PATH.'/views/layouts/main.php'; ?>

<div class="page-heading">
  <div>
    <h1><i class="fa-solid fa-boxes-stacked" style="color: var(--shopee-primary);"></i> Katalog Produk & Etalase Shopee</h1>
    <p>Kelola etalase toko, harga diskon, stok barang, dan kategori produk agar menarik pembeli.</p>
  </div>
  <div style="display: flex; gap: 10px;">
    <button type="button" class="btn primary" onclick="document.getElementById('formTambahProduk').scrollIntoView({behavior: 'smooth'});">
      <i class="fa-solid fa-plus"></i> Tambah Produk
    </button>
  </div>
</div>

<!-- Category Tabs & View Switcher -->
<div class="shopee-catalog-header">
  <div class="shopee-category-tabs">
    <button type="button" class="category-tab-btn active" data-cat="all">
      <i class="fa-solid fa-border-all"></i> Semua (<?= count($items) ?>)
    </button>
    <?php foreach($categories as $c): ?>
      <button type="button" class="category-tab-btn" data-cat="<?= $c['id_kategori'] ?>">
        <?= e($c['nama_kategori']) ?>
      </button>
    <?php endforeach; ?>
  </div>

  <div class="shopee-view-switcher">
    <button type="button" class="view-btn active" data-view="grid" title="Tampilan Grid Etalase">
      <i class="fa-solid fa-table-cells-large"></i> Grid
    </button>
    <button type="button" class="view-btn" data-view="table" title="Tampilan Tabel">
      <i class="fa-solid fa-table-list"></i> Tabel
    </button>
  </div>
</div>

<!-- Shopee Grid Catalog View -->
<div class="shopee-product-grid" id="catalogGridView">
  <?php foreach($items as $idx => $r): 
    $discounts = [10, 15, 20, 25];
    $discPct = $discounts[$idx % 4];
    $oriPrice = $r['harga'] / (1 - ($discPct / 100));
    $icons = ['Makanan' => 'fa-utensils', 'Minuman' => 'fa-mug-hot', 'Perlengkapan' => 'fa-box-archive'];
    $catIcon = $icons[$r['nama_kategori'] ?? ''] ?? 'fa-bag-shopping';
  ?>
  <div class="shopee-product-card" data-cat="<?= $r['id_kategori'] ?>">
    <div class="product-thumb-wrap">
      <span class="card-badge-mall">STAR+</span>
      <span class="card-badge-discount">-<?= $discPct ?>%</span>
      <?php if(!empty($r['foto'])): ?>
        <img class="product-thumb-image" src="<?= BASE_URL ?>/uploads/produk/<?= e(rawurlencode($r['foto'])) ?>" alt="Foto <?= e($r['nama_produk']) ?>" loading="lazy">
      <?php else: ?>
        <i class="fa-solid <?= $catIcon ?> product-thumb-icon"></i>
      <?php endif; ?>
    </div>
    <div class="product-card-body">
      <span class="product-cat-pill"><?= e($r['nama_kategori'] ?? 'Umum') ?></span>
      <div class="product-card-title"><?= e($r['nama_produk']) ?></div>
      <?= barcode_svg((string)($r['barcode'] ?? '')) ?>
      <div class="product-card-desc"><?= e($r['deskripsi'] ?: 'Produk pilihan bermutu tinggi, siap dikirim.') ?></div>
      <div class="product-price-row">
        <span class="original-price"><?= rupiah($oriPrice) ?></span>
        <span class="shopee-price"><?= rupiah($r['harga']) ?></span>
      </div>
      <div class="product-meta-row">
        <span class="product-rating"><i class="fa-solid fa-star"></i> 4.9</span>
        <span class="product-sold">150+ Terjual</span>
        <span class="stock-tag <?= $r['stok'] <= 10 ? 'low' : '' ?>">Stok: <?= $r['stok'] ?></span>
      </div>
      <div style="display: flex; gap: 8px;">
        <a href="<?= url('transaksi_tambah') ?>" class="btn-add-cart" style="flex: 1;">
          <i class="fa-solid fa-cart-shopping"></i> Beli di Kasir
        </a>
        <a class="danger-link" onclick="return confirm('Hapus produk <?= e(addslashes($r['nama_produk'])) ?>?')" href="<?= url('produk',['hapus'=>$r['id_produk']]) ?>" title="Hapus Produk" style="padding: 8px; border: 1px solid #fee2e2; border-radius: var(--radius-sm); background: #fff5f5;">
          <i class="fa-solid fa-trash"></i>
        </a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if(!$items): ?>
    <div class="panel empty" style="grid-column: 1 / -1;">
      Belum ada produk yang ditambahkan. Silakan tambahkan produk di bawah.
    </div>
  <?php endif; ?>
</div>

<!-- Table Catalog View (Hidden by default, toggled via switcher) -->
<div class="panel" id="catalogTableView" style="display: none;">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Foto</th>
          <th>Nama Produk</th>
          <th>Barcode</th>
          <th>Kategori</th>
          <th>Harga Jual</th>
          <th>Stok</th>
          <th>Deskripsi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($items as $r): ?>
        <tr class="product-table-row" data-cat="<?= $r['id_kategori'] ?>">
          <td><strong>#<?= $r['id_produk'] ?></strong></td>
          <td>
            <?php if(!empty($r['foto'])): ?>
              <img class="product-table-image" src="<?= BASE_URL ?>/uploads/produk/<?= e(rawurlencode($r['foto'])) ?>" alt="Foto <?= e($r['nama_produk']) ?>" loading="lazy">
            <?php else: ?>
              <i class="fa-solid fa-image product-table-placeholder" aria-label="Belum ada foto"></i>
            <?php endif; ?>
          </td>
          <td>
            <strong><?= e($r['nama_produk']) ?></strong>
          </td>
          <td>
            <?= barcode_svg((string)($r['barcode'] ?? '')) ?>
            <form method="post" action="<?= url('produk') ?>" style="display: flex; gap: 6px; min-width: 190px;">
              <?= csrf_field() ?>
              <input type="hidden" name="aksi" value="update_barcode">
              <input type="hidden" name="id" value="<?= (int)$r['id_produk'] ?>">
              <input type="text" name="barcode" value="<?= e($r['barcode'] ?? '') ?>" maxlength="32" required aria-label="Barcode <?= e($r['nama_produk']) ?>" style="min-width: 0; padding: 6px;">
              <button type="submit" class="btn secondary sm">Simpan</button>
            </form>
          </td>
          <td>
            <span class="badge-status success"><?= e($r['nama_kategori'] ?? '-') ?></span>
          </td>
          <td>
            <strong style="color: var(--shopee-primary); font-size: 14px;"><?= rupiah($r['harga']) ?></strong>
          </td>
          <td>
            <span class="stock-tag <?= $r['stok'] <= 10 ? 'low' : '' ?>">
              <?= $r['stok'] ?> unit
            </span>
          </td>
          <td>
            <small style="color: var(--text-muted);"><?= e($r['deskripsi'] ?: '-') ?></small>
          </td>
          <td>
            <a class="danger-link" onclick="return confirm('Hapus produk <?= e(addslashes($r['nama_produk'])) ?>?')" href="<?= url('produk',['hapus'=>$r['id_produk']]) ?>">
              <i class="fa-solid fa-trash"></i> Hapus
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Tambah Produk Form Panel -->
<div class="panel" id="formTambahProduk">
  <h2><i class="fa-solid fa-circle-plus" style="color: var(--shopee-primary);"></i> Tambah Produk Baru ke Etalase</h2>
  <?php if(!empty($error)): ?>
    <div class="shopee-alert error" role="alert">
      <i class="fa-solid fa-circle-exclamation"></i>
      <div class="alert-text"><?= e($error) ?></div>
    </div>
  <?php endif; ?>
  <form method="post" class="form-grid" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <label>
      Nama Produk
      <input name="nama_produk" required placeholder="Contoh: Biskuit Cokelat Premium">
    </label>

    <label>
      Kategori Produk
      <select name="id_kategori" required>
        <?php foreach($categories as $c): ?>
          <option value="<?= $c['id_kategori'] ?>"><?= e($c['nama_kategori']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>
      Harga Jual (Rp)
      <input type="number" name="harga" min="0" step="500" required placeholder="Contoh: 15000">
    </label>

    <label>
      Jumlah Stok Awal
      <input type="number" name="stok" min="0" required placeholder="Contoh: 50">
    </label>

    <label class="wide">
      Deskripsi Produk
      <input name="deskripsi" placeholder="Deskripsi singkat produk untuk menarik perhatian pembeli">
    </label>

    <label class="wide">
      Barcode Produk
      <input type="text" name="barcode" class="product-barcode-input" maxlength="32" autocomplete="off" placeholder="Scan atau masukkan barcode pada kemasan">
      <small>Opsional. Jika kosong, sistem membuat kode internal otomatis. Barcode harus unik.</small>
    </label>

    <label class="wide">
      Foto Produk
      <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" required>
      <small>Wajib diisi. Format JPG, PNG, atau WEBP, maksimal 5 MB.</small>
    </label>

    <div class="wide" style="display: flex; justify-content: flex-end; margin-top: 6px;">
      <button class="btn primary" style="padding: 11px 24px;">
        <i class="fa-solid fa-floppy-disk"></i> Simpan ke Etalase
      </button>
    </div>
  </form>
</div>

<?php require APP_PATH.'/views/layouts/footer.php'; ?>
