<?php require APP_PATH.'/views/layouts/main.php'; ?>

<div class="page-heading">
  <div>
    <h1><i class="fa-solid fa-receipt" style="color: var(--shopee-primary);"></i> Riwayat Transaksi & Pesanan</h1>
    <p>Daftar seluruh transaksi kasir yang telah berhasil diproses.</p>
  </div>
  <a class="btn primary" href="<?= url('transaksi_tambah') ?>">
    <i class="fa-solid fa-cart-plus"></i> Transaksi Baru
  </a>
</div>

<div class="panel">
  <div class="panel-head">
    <h2><i class="fa-solid fa-list-check" style="color: var(--shopee-primary);"></i> Semua Pesanan (<?= count($items) ?> Transaksi)</h2>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Kode Transaksi</th>
          <th>Tanggal</th>
          <th>Nama Pelanggan</th>
          <th>Metode Pembayaran</th>
          <th>Status Pesanan</th>
          <th>Total Belanja</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($items as $r): ?>
        <tr>
          <td>
            <strong><?= e($r['kode_transaksi']) ?></strong>
          </td>
          <td>
            <i class="fa-regular fa-calendar" style="color: var(--text-light); margin-right: 4px;"></i>
            <?= e($r['tanggal']) ?>
          </td>
          <td>
            <div style="font-weight: 700;"><?= e($r['nama_pelanggan']) ?></div>
            <?php if(!empty($r['kota'])): ?>
              <small style="color: var(--text-muted);"><i class="fa-solid fa-location-dot" style="font-size: 10px;"></i> <?= e($r['kota']) ?></small>
            <?php endif; ?>
          </td>
          <td><?= e($r['metode_pembayaran'] ?? 'Tunai') ?></td>
          <td>
            <span class="badge-status success"><i class="fa-solid fa-check"></i> Selesai (Lunas)</span>
          </td>
          <td>
            <strong style="color: var(--shopee-primary); font-size: 14.5px;"><?= rupiah($r['total_belanja']) ?></strong>
          </td>
          <td>
            <a href="<?= url('transaksi_detail', ['id' => $r['id_transaksi']]) ?>" class="btn outline-primary sm">
              <i class="fa-regular fa-eye"></i> Lihat Struk
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(!$items): ?>
          <tr><td colspan="7" class="empty">Belum ada transaksi tersimpan.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require APP_PATH.'/views/layouts/footer.php'; ?>
