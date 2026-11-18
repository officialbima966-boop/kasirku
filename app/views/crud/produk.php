<?php require APP_PATH.'/views/layouts/main.php'; ?>

<div class="page-heading">
  <div>
    <h1><i class="fa-solid fa-boxes-stacked" style="color: var(--shopee-primary);"></i> Katalog Produk & Etalase Shopee</h1>
    <p>Kelola etalase toko, harga diskon, stok barang, dan kategori produk agar menarik pembeli.</p>
  </div>
  <div style="display: flex; gap: 10px;">
    <button type="button" class="btn primary" onclick="resetProductForm(); document.getElementById('formTambahProduk').scrollIntoView({behavior: 'smooth'});">
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
      <div style="display: flex; gap: 6px; align-items: center;">
        <a href="<?= url('transaksi_tambah') ?>" class="btn-add-cart" style="flex: 1; padding: 7px 10px; font-size: 13px;" title="Beli di Kasir">
          <i class="fa-solid fa-cart-shopping"></i> Kasir
        </a>
        <button type="button" class="btn secondary sm btn-edit-product"
                data-id="<?= $r['id_produk'] ?>"
                data-nama="<?= e($r['nama_produk']) ?>"
                data-kategori="<?= $r['id_kategori'] ?>"
                data-harga="<?= (float)$r['harga'] ?>"
                data-stok="<?= (int)$r['stok'] ?>"
                data-deskripsi="<?= e($r['deskripsi'] ?? '') ?>"
                data-barcode="<?= e($r['barcode'] ?? '') ?>"
                data-foto="<?= e($r['foto'] ?? '') ?>"
                title="Edit Produk"
                style="padding: 7px 10px; font-size: 13px;">
          <i class="fa-solid fa-pen-to-square"></i> Edit
        </button>
        <a class="danger-link" onclick="return confirm('Hapus produk <?= e(addslashes($r['nama_produk'])) ?>?')" href="<?= url('produk',['hapus'=>$r['id_produk']]) ?>" title="Hapus Produk" style="padding: 7px 10px; border: 1px solid #fee2e2; border-radius: var(--radius-sm); background: #fff5f5; font-size: 13px; display: inline-flex; align-items: center; justify-content: center;">
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
            <div style="display: flex; gap: 6px; align-items: center;">
              <button type="button" class="btn secondary sm btn-edit-product"
                      data-id="<?= $r['id_produk'] ?>"
                      data-nama="<?= e($r['nama_produk']) ?>"
                      data-kategori="<?= $r['id_kategori'] ?>"
                      data-harga="<?= (float)$r['harga'] ?>"
                      data-stok="<?= (int)$r['stok'] ?>"
                      data-deskripsi="<?= e($r['deskripsi'] ?? '') ?>"
                      data-barcode="<?= e($r['barcode'] ?? '') ?>"
                      data-foto="<?= e($r['foto'] ?? '') ?>"
                      title="Edit Produk">
                <i class="fa-solid fa-pen-to-square"></i> Edit
              </button>
              <a class="danger-link" onclick="return confirm('Hapus produk <?= e(addslashes($r['nama_produk'])) ?>?')" href="<?= url('produk',['hapus'=>$r['id_produk']]) ?>" title="Hapus Produk">
                <i class="fa-solid fa-trash"></i> Hapus
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Tambah / Edit Produk Form Panel -->
<div class="panel" id="formTambahProduk">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
    <h2 id="formTitle" style="margin-bottom: 0;">
      <i class="fa-solid fa-circle-plus" style="color: var(--shopee-primary);"></i> Tambah Produk Baru ke Etalase
    </h2>
    <button type="button" id="btnCancelEdit" class="btn secondary sm" style="display: none;" onclick="resetProductForm();">
      <i class="fa-solid fa-xmark"></i> Batal Edit
    </button>
  </div>
  <?php if(!empty($error)): ?>
    <div class="shopee-alert error" role="alert">
      <i class="fa-solid fa-circle-exclamation"></i>
      <div class="alert-text"><?= e($error) ?></div>
    </div>
  <?php endif; ?>
  <form method="post" class="form-grid" enctype="multipart/form-data" id="productForm">
    <?= csrf_field() ?>
    <input type="hidden" name="id" id="input-id" value="0">
    <label>
      Nama Produk
      <input name="nama_produk" id="input-nama" required placeholder="Contoh: Biskuit Cokelat Premium">
    </label>

    <label>
      Kategori Produk
      <select name="id_kategori" id="input-kategori" required>
        <?php foreach($categories as $c): ?>
          <option value="<?= $c['id_kategori'] ?>"><?= e($c['nama_kategori']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>
      Harga Jual (Rp)
      <input type="number" name="harga" id="input-harga" min="0" step="500" required placeholder="Contoh: 15000">
    </label>

    <label>
      Jumlah Stok
      <input type="number" name="stok" id="input-stok" min="0" required placeholder="Contoh: 50">
    </label>

    <label class="wide">
      Deskripsi Produk
      <input name="deskripsi" id="input-deskripsi" placeholder="Deskripsi singkat produk untuk menarik perhatian pembeli">
    </label>

    <label class="wide">
      Barcode Produk
      <input type="text" name="barcode" id="input-barcode" class="product-barcode-input" maxlength="32" autocomplete="off" placeholder="Scan atau masukkan barcode pada kemasan">
      <small>Opsional. Jika kosong, sistem membuat kode internal otomatis. Barcode harus unik.</small>
    </label>

    <label class="wide">
      Foto Produk
      <input type="file" name="foto" id="input-foto" accept="image/jpeg,image/png,image/webp" required>
      <small id="fotoHelpText">Wajib diisi untuk produk baru. Format JPG, PNG, atau WEBP, maksimal 5 MB.</small>
      <div id="currentPhotoPreview" style="display: none; margin-top: 10px; align-items: center; gap: 12px; padding: 10px; background: #f8fafc; border-radius: var(--radius-sm); border: 1px dashed var(--border);">
        <img id="currentPhotoImg" src="" alt="Foto saat ini" style="width: 54px; height: 54px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border);">
        <div style="font-size: 13px; color: var(--text-muted);">
          <strong style="color: var(--text); display: block;">Foto Saat Ini Tersimpan</strong>
          Biarkan input file di atas kosong jika tidak ingin mengganti foto ini.
        </div>
      </div>
    </label>

    <div class="wide" style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 6px;">
      <button type="button" id="btnCancelEditBottom" class="btn secondary" style="display: none;" onclick="resetProductForm();">
        <i class="fa-solid fa-xmark"></i> Batal
      </button>
      <button type="submit" id="btnSubmitProduct" class="btn primary" style="padding: 11px 24px;">
        <i class="fa-solid fa-floppy-disk"></i> <span id="btnSubmitText">Simpan ke Etalase</span>
      </button>
    </div>
  </form>
</div>

<script>
function editProduct(data) {
  document.getElementById('input-id').value = data.id;
  document.getElementById('input-nama').value = data.nama;
  document.getElementById('input-kategori').value = data.kategori;
  document.getElementById('input-harga').value = data.harga;
  document.getElementById('input-stok').value = data.stok;
  document.getElementById('input-deskripsi').value = data.deskripsi;
  document.getElementById('input-barcode').value = data.barcode;

  const fotoInput = document.getElementById('input-foto');
  fotoInput.removeAttribute('required');
  document.getElementById('fotoHelpText').innerHTML = 'Opsional saat edit. Pilih file baru jika ingin mengganti foto. Format JPG, PNG, atau WEBP (maks 5 MB).';

  if (data.foto) {
    document.getElementById('currentPhotoPreview').style.display = 'flex';
    document.getElementById('currentPhotoImg').src = '<?= BASE_URL ?>/uploads/produk/' + encodeURIComponent(data.foto);
  } else {
    document.getElementById('currentPhotoPreview').style.display = 'none';
  }

  document.getElementById('formTitle').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color: var(--shopee-primary);"></i> Edit Produk #' + data.id + ' (' + (data.nama || '') + ')';
  document.getElementById('btnSubmitText').innerText = 'Perbarui Produk';
  document.getElementById('btnCancelEdit').style.display = 'inline-flex';
  document.getElementById('btnCancelEditBottom').style.display = 'inline-flex';

  document.getElementById('formTambahProduk').scrollIntoView({behavior: 'smooth'});
  document.getElementById('input-nama').focus();
}

function resetProductForm() {
  document.getElementById('productForm').reset();
  document.getElementById('input-id').value = '0';
  const fotoInput = document.getElementById('input-foto');
  fotoInput.setAttribute('required', 'required');
  document.getElementById('fotoHelpText').innerText = 'Wajib diisi untuk produk baru. Format JPG, PNG, atau WEBP, maksimal 5 MB.';
  document.getElementById('currentPhotoPreview').style.display = 'none';

  document.getElementById('formTitle').innerHTML = '<i class="fa-solid fa-circle-plus" style="color: var(--shopee-primary);"></i> Tambah Produk Baru ke Etalase';
  document.getElementById('btnSubmitText').innerText = 'Simpan ke Etalase';
  document.getElementById('btnCancelEdit').style.display = 'none';
  document.getElementById('btnCancelEditBottom').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.btn-edit-product').forEach(function(btn) {
    btn.addEventListener('click', function() {
      editProduct({
        id: this.dataset.id,
        nama: this.dataset.nama,
        kategori: this.dataset.kategori,
        harga: this.dataset.harga,
        stok: this.dataset.stok,
        deskripsi: this.dataset.deskripsi,
        barcode: this.dataset.barcode,
        foto: this.dataset.foto
      });
    });
  });

  <?php if (!empty($editItem)): ?>
  editProduct({
    id: <?= json_encode($editItem['id_produk']) ?>,
    nama: <?= json_encode($editItem['nama_produk']) ?>,
    kategori: <?= json_encode($editItem['id_kategori']) ?>,
    harga: <?= json_encode((float)$editItem['harga']) ?>,
    stok: <?= json_encode((int)$editItem['stok']) ?>,
    deskripsi: <?= json_encode($editItem['deskripsi'] ?? '') ?>,
    barcode: <?= json_encode($editItem['barcode'] ?? '') ?>,
    foto: <?= json_encode($editItem['foto'] ?? '') ?>
  });
  <?php endif; ?>
});
</script>

<?php require APP_PATH.'/views/layouts/footer.php'; ?>
