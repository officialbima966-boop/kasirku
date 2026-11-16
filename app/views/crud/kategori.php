<?php require APP_PATH.'/views/layouts/main.php'; ?>

<div class="page-heading">
  <div>
    <h1><i class="fa-solid fa-tags" style="color: var(--shopee-primary);"></i> Kategori Produk Shopee</h1>
    <p>Kelompokkan produk ke dalam kategori yang tepat agar pembeli mudah menemukan barang.</p>
  </div>
</div>

<div class="panel">
  <h2><i class="fa-solid fa-plus-circle" style="color: var(--shopee-primary);"></i> Tambah Kategori Baru</h2>
  <form method="post" class="inline-form">
    <?= csrf_field() ?>
    <label style="flex: 1; max-width: 400px;">
      Nama Kategori Produk
      <input name="nama_kategori" required placeholder="Contoh: Makanan Ringan, Elektronik, dll">
    </label>
    <button class="btn primary">
      <i class="fa-solid fa-plus"></i> Tambah Kategori
    </button>
  </form>
</div>

<div class="panel">
  <div class="panel-head">
    <h2><i class="fa-solid fa-list" style="color: var(--shopee-primary);"></i> Daftar Kategori Toko</h2>
    <span class="badge-status success"><i class="fa-solid fa-tag"></i> <?= count($items) ?> Kategori Tersedia</span>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Kategori</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($items as $r): ?>
        <tr>
          <td><strong>#<?= $r['id_kategori'] ?></strong></td>
          <td>
            <div style="display: flex; align-items: center; gap: 8px;">
              <span class="chip-avatar" style="width: 24px; height: 24px; font-size: 11px; background: var(--shopee-primary-light); color: var(--shopee-primary);">
                <i class="fa-solid fa-tag"></i>
              </span>
              <strong><?= e($r['nama_kategori']) ?></strong>
            </div>
          </td>
          <td>
            <a class="danger-link" onclick="return confirm('Hapus kategori <?= e(addslashes($r['nama_kategori'])) ?>?')" href="<?= url('kategori',['hapus'=>$r['id_kategori']]) ?>">
              <i class="fa-solid fa-trash"></i> Hapus
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(!$items): ?>
          <tr><td colspan="3" class="empty">Belum ada kategori yang ditambahkan.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require APP_PATH.'/views/layouts/footer.php'; ?>
