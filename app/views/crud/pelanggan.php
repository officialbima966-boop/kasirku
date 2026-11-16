<?php require APP_PATH.'/views/layouts/main.php'; ?>

<div class="page-heading">
  <div>
    <h1><i class="fa-solid fa-users" style="color: var(--shopee-primary);"></i> Data Pelanggan & Member Toko</h1>
    <p>Kelola identitas pembeli, kontak pelanggan, dan riwayat loyalitas toko.</p>
  </div>
  <a class="btn primary" href="<?= url('transaksi_tambah') ?>">
    <i class="fa-solid fa-cart-plus"></i> Transaksi Baru
  </a>
</div>

<div class="panel">
  <h2><i class="fa-solid fa-user-plus" style="color: var(--shopee-primary);"></i> Tambah / Perbarui Pelanggan</h2>
  <form method="post" class="form-grid" id="formPelanggan">
    <?= csrf_field() ?>
    <input type="hidden" name="id" id="edit-id">
    
    <label>
      Nama Lengkap Pelanggan
      <input name="nama_pelanggan" id="input-nama" required placeholder="Contoh: Andi Pratama">
    </label>

    <label>
      Kota Domisili
      <input name="kota" id="input-kota" placeholder="Contoh: Ponorogo, Surabaya">
    </label>

    <label>
      Nomor Handphone / WhatsApp
      <input name="no_hp" id="input-hp" placeholder="Contoh: 081234567890">
    </label>

    <label>
      Alamat Pengiriman
      <input name="alamat" id="input-alamat" placeholder="Contoh: Jl. Mawar No. 12">
    </label>

    <div class="wide" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 6px;">
      <button type="button" class="btn secondary" onclick="document.getElementById('formPelanggan').reset(); document.getElementById('edit-id').value='';">
        Reset
      </button>
      <button class="btn primary">
        <i class="fa-solid fa-floppy-disk"></i> Simpan Data Pelanggan
      </button>
    </div>
  </form>
</div>

<div class="panel">
  <div class="panel-head">
    <h2><i class="fa-solid fa-address-book" style="color: var(--shopee-primary);"></i> Daftar Pelanggan Aktif</h2>
    <span class="badge-status success"><i class="fa-solid fa-user-check"></i> <?= count($items) ?> Pelanggan Terdaftar</span>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Pelanggan</th>
          <th>Kota</th>
          <th>No. HP</th>
          <th>Alamat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($items as $r): ?>
        <tr>
          <td><strong>#<?= $r['id_pelanggan'] ?></strong></td>
          <td>
            <div style="display: flex; align-items: center; gap: 8px;">
              <div class="chip-avatar" style="width: 26px; height: 26px; font-size: 11px;">
                <?= strtoupper(substr($r['nama_pelanggan'], 0, 1)) ?>
              </div>
              <strong><?= e($r['nama_pelanggan']) ?></strong>
            </div>
          </td>
          <td>
            <i class="fa-solid fa-location-dot" style="color: var(--shopee-primary); font-size: 11px; margin-right: 4px;"></i>
            <?= e($r['kota'] ?: '-') ?>
          </td>
          <td>
            <i class="fa-solid fa-phone" style="color: #059669; font-size: 11px; margin-right: 4px;"></i>
            <?= e($r['no_hp'] ?: '-') ?>
          </td>
          <td><?= e($r['alamat'] ?: '-') ?></td>
          <td>
            <div style="display: flex; gap: 8px;">
              <button type="button" 
                      class="btn secondary sm"
                      onclick="
                        document.getElementById('edit-id').value = '<?= $r['id_pelanggan'] ?>';
                        document.getElementById('input-nama').value = '<?= e(addslashes($r['nama_pelanggan'])) ?>';
                        document.getElementById('input-kota').value = '<?= e(addslashes($r['kota'] ?? '')) ?>';
                        document.getElementById('input-hp').value = '<?= e(addslashes($r['no_hp'] ?? '')) ?>';
                        document.getElementById('input-alamat').value = '<?= e(addslashes($r['alamat'] ?? '')) ?>';
                        document.getElementById('formPelanggan').scrollIntoView({behavior: 'smooth'});
                      ">
                <i class="fa-solid fa-pen-to-square"></i> Edit
              </button>
              <a class="danger-link" onclick="return confirm('Hapus pelanggan <?= e(addslashes($r['nama_pelanggan'])) ?>?')" href="<?= url('pelanggan',['hapus'=>$r['id_pelanggan']]) ?>">
                <i class="fa-solid fa-trash"></i> Hapus
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(!$items): ?>
          <tr><td colspan="6" class="empty">Belum ada data pelanggan.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require APP_PATH.'/views/layouts/footer.php'; ?>
