<?php require APP_PATH.'/views/layouts/main.php'; ?>

<div class="page-heading">
  <div>
    <h1><i class="fa-solid fa-receipt" style="color: var(--shopee-primary);"></i> Pesanan Saya</h1>
    <p>Pesanan menunggu diproses oleh kasir. Diskon member 5% dihitung saat kartu RFID diverifikasi.</p>
  </div>
  <a class="btn primary" href="<?= url('member') ?>"><i class="fa-solid fa-cart-plus"></i> Belanja</a>
</div>

<div class="panel">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Kode Pesanan</th><th>Tanggal</th><th>Status</th><th>Subtotal</th></tr></thead>
      <tbody>
        <?php foreach($orders as $order): ?>
        <tr>
          <td><strong><?= e($order['kode_pesanan']) ?></strong></td>
          <td><?= e($order['dibuat_pada']) ?></td>
          <td><span class="badge-status <?= $order['status']==='dibayar'?'success':'warning' ?>"><?= e(ucfirst($order['status'])) ?></span></td>
          <td><?= rupiah($order['subtotal']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if(!$orders): ?>
          <tr><td colspan="4" class="empty">Belum ada pesanan.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require APP_PATH.'/views/layouts/footer.php'; ?>
