<?php require APP_PATH.'/views/layouts/main.php'; ?>

<div class="page-heading">
  <div>
    <h1><i class="fa-solid fa-clipboard-list" style="color: var(--shopee-primary);"></i> Antrean Pembayaran</h1>
    <p>Pilih pesanan pelanggan, scan kartu RFID untuk diskon member, lalu selesaikan pembayaran.</p>
  </div>
  <a class="btn primary" href="<?= url('transaksi_tambah') ?>">
    <i class="fa-solid fa-cart-plus"></i> Transaksi Baru (POS)
  </a>
</div>

<div class="panel">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Kode Pesanan</th><th>Pelanggan</th><th>Waktu</th><th>Subtotal</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php foreach($orders as $order): ?>
        <tr>
          <td><strong><?= e($order['kode_pesanan']) ?></strong></td>
          <td><?= e($order['nama_pelanggan']) ?></td>
          <td><?= e($order['dibuat_pada']) ?></td>
          <td><?= rupiah($order['subtotal']) ?></td>
          <td><a class="btn primary sm" href="<?= url('kasir_bayar',['id'=>$order['id_pesanan']]) ?>"><i class="fa-solid fa-money-bill-wave"></i> Proses Pembayaran</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if(!$orders): ?>
          <tr><td colspan="5" class="empty">Belum ada pesanan yang menunggu pembayaran.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require APP_PATH.'/views/layouts/footer.php'; ?>
