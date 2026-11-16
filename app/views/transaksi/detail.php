<?php require APP_PATH.'/views/layouts/main.php'; $h = $data['header']; ?>
<?php
$subtotal = array_sum(array_column($data['items'], 'subtotal'));
$totalItems = array_sum(array_column($data['items'], 'jumlah'));
$transactionTimestamp = strtotime($h['dibuat_pada']);
$payment = (float)($h['uang_dibayar'] ?? $h['total_belanja']);
$change = (float)($h['kembalian'] ?? max(0, $payment - (float)$h['total_belanja']));
?>

<div class="page-heading">
  <div>
    <h1><i class="fa-solid fa-file-invoice-dollar" style="color: var(--shopee-primary);"></i> Struk & Detail Transaksi</h1>
    <p>Kode: <strong><?= e($h['kode_transaksi']) ?></strong> · Tanggal: <?= e($h['tanggal']) ?></p>
  </div>
  <div style="display: flex; gap: 10px;">
    <a href="<?= url('transaksi') ?>" class="btn secondary">
      <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
    <button type="button" class="btn primary" id="printReceiptButton">
      <i class="fa-solid fa-print"></i> Cetak Struk
    </button>
  </div>
</div>

<div class="receipt-card" id="receiptCard">
  <div class="receipt-header">
    <div class="receipt-store-icon" aria-hidden="true"><i class="fa-solid fa-store"></i></div>
    <div class="receipt-brand-logo">BIMA SURYA</div>
    <div class="receipt-subtitle">NOTA PEMBELIAN</div>
    <div class="receipt-welcome">Selamat datang, terima kasih telah berbelanja</div>
  </div>

  <div class="receipt-meta-grid">
    <div class="receipt-meta-item">
      <span>No.</span>
      <strong><?= e($h['kode_transaksi']) ?></strong>
    </div>
    <div class="receipt-meta-item">
      <span>Tanggal</span>
      <strong><?= e(date('d/m/Y', $transactionTimestamp)) ?></strong>
    </div>
    <div class="receipt-meta-item">
      <span>Jam</span>
      <strong><?= e(date('H:i', $transactionTimestamp)) ?></strong>
    </div>
    <div class="receipt-meta-item">
      <span>Pelanggan</span>
      <strong><?= e($h['nama_pelanggan']) ?></strong>
    </div>
  </div>

  <div class="receipt-section-title">RINCIAN PEMBELIAN</div>
  <div class="receipt-items">
    <?php foreach($data['items'] as $index => $i): ?>
    <div class="receipt-item">
      <div class="receipt-product-name"><?= $index + 1 ?>. <?= e($i['nama_produk']) ?></div>
      <div class="receipt-line-calculation">
        <span><?= (int)$i['jumlah'] ?> × <?= rupiah($i['harga']) ?></span>
        <strong><?= rupiah($i['subtotal']) ?></strong>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="receipt-summary">
    <div class="receipt-summary-row">
      <span>TOTAL QTY : <?= $totalItems ?></span>
    </div>
    <div class="receipt-summary-row">
      <span>Sub Total</span>
      <strong><?= rupiah($subtotal) ?></strong>
    </div>
    <?php if((float)$h['diskon'] > 0): ?>
      <div class="receipt-summary-row">
        <span>Diskon</span>
        <strong>- <?= rupiah($h['diskon']) ?></strong>
      </div>
    <?php endif; ?>
    <?php if((float)$h['pajak'] > 0): ?>
      <div class="receipt-summary-row">
        <span>Pajak (PPN 11%)</span>
        <strong><?= rupiah($h['pajak']) ?></strong>
      </div>
    <?php endif; ?>
    <div class="receipt-grand-total">
      <span>Total</span>
      <strong><?= rupiah($h['total_belanja']) ?></strong>
    </div>
    <div class="receipt-summary-row receipt-payment-row">
      <span>Bayar (Cash)</span>
      <strong><?= rupiah($payment) ?></strong>
    </div>
    <div class="receipt-summary-row receipt-change-row">
      <span>Kembali</span>
      <strong><?= rupiah($change) ?></strong>
    </div>
  </div>

  <div class="receipt-thankyou">
    <strong>Terima kasih telah berbelanja</strong>
    <span>BIMA SURYA</span>
  </div>
</div>

<?php require APP_PATH.'/views/layouts/footer.php'; ?>
