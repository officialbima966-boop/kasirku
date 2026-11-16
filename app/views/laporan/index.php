<?php require APP_PATH.'/views/layouts/main.php'; ?>

<div class="page-heading">
  <div>
    <h1><i class="fa-solid fa-chart-line" style="color: var(--shopee-primary);"></i> Laporan Penjualan & Pendapatan</h1>
    <p>Rekapitulasi omzet dan performa transaksi toko berdasarkan rentang tanggal.</p>
  </div>
  <button class="btn secondary" onclick="window.print()">
    <i class="fa-solid fa-print"></i> Cetak Laporan
  </button>
</div>

<form class="panel inline-form" method="get">
  <input type="hidden" name="page" value="laporan">
  <label>
    <span><i class="fa-regular fa-calendar"></i> Dari Tanggal</span>
    <input type="date" name="mulai" value="<?= e($start) ?>">
  </label>
  <label>
    <span><i class="fa-regular fa-calendar"></i> Sampai Tanggal</span>
    <input type="date" name="sampai" value="<?= e($end) ?>">
  </label>
  <button class="btn primary">
    <i class="fa-solid fa-filter"></i> Filter Periode
  </button>
</form>

<div class="stats" style="grid-template-columns: repeat(2, 1fr);">
  <div class="stat">
    <div class="stat-icon-wrap">
      <i class="fa-solid fa-money-bill-trend-up"></i>
    </div>
    <div class="stat-body">
      <span>Total Pendapatan Periode Ini</span>
      <b style="color: var(--shopee-primary);"><?= rupiah($total) ?></b>
      <small><i class="fa-solid fa-calendar-check"></i> <?= e($start) ?> s/d <?= e($end) ?></small>
    </div>
  </div>

  <div class="stat stat-blue">
    <div class="stat-icon-wrap">
      <i class="fa-solid fa-cart-arrow-down"></i>
    </div>
    <div class="stat-body">
      <span>Jumlah Transaksi Berhasil</span>
      <b><?= count($items) ?> Transaksi</b>
      <small><i class="fa-solid fa-calculator"></i> Rata-rata: <?= count($items) > 0 ? rupiah($total / count($items)) : 'Rp 0' ?> / transaksi</small>
    </div>
  </div>
</div>

<div class="panel">
  <div class="panel-head">
    <h2><i class="fa-solid fa-table-list" style="color: var(--shopee-primary);"></i> Rincian Transaksi Periode</h2>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Kode Transaksi</th>
          <th>Nama Pelanggan</th>
          <th>Status</th>
          <th>Total Belanja</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($items as $r): ?>
        <tr>
          <td>
            <i class="fa-regular fa-calendar" style="color: var(--text-light); margin-right: 4px;"></i>
            <?= e($r['tanggal']) ?>
          </td>
          <td><strong><?= e($r['kode_transaksi']) ?></strong></td>
          <td><?= e($r['nama_pelanggan']) ?></td>
          <td><span class="badge-status success"><i class="fa-solid fa-check"></i> Lunas</span></td>
          <td><strong style="color: var(--shopee-primary); font-size: 14.5px;"><?= rupiah($r['total_belanja']) ?></strong></td>
          <td>
            <a href="<?= url('transaksi_detail', ['id' => $r['id_transaksi']]) ?>" class="btn outline-primary sm">
              <i class="fa-regular fa-eye"></i> Struk
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(!$items): ?>
          <tr><td colspan="6" class="empty">Tidak ada transaksi pada periode yang dipilih.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require APP_PATH.'/views/layouts/footer.php'; ?>
