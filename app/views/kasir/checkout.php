<?php require APP_PATH.'/views/layouts/main.php'; ?>

<style>
  .rfid-scanner-box {
    background: linear-gradient(135deg, #fff5f2 0%, #ffefe8 100%);
    border: 2px dashed var(--shopee-primary);
    border-radius: var(--radius-md);
    padding: 24px;
    margin-bottom: 24px;
    text-align: center;
    position: relative;
  }
  .rfid-tap-icon {
    font-size: 48px;
    color: var(--shopee-primary);
    margin-bottom: 12px;
    display: inline-block;
    animation: tap-pulse 2s infinite ease-in-out;
  }
  @keyframes tap-pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.12); opacity: 0.8; }
  }
  .payment-quick-buttons {
    display: flex;
    gap: 8px;
    margin-top: 8px;
    flex-wrap: wrap;
  }
  .quick-pay-btn {
    background: #fff;
    border: 1px solid var(--border-medium);
    padding: 6px 12px;
    border-radius: var(--radius-sm);
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .quick-pay-btn:hover {
    background: var(--shopee-primary-light);
    border-color: var(--shopee-primary);
    color: var(--shopee-primary);
  }
</style>

<div class="page-heading">
  <div>
    <h1><i class="fa-solid fa-cash-register" style="color: var(--shopee-primary);"></i> Pembayaran Pesanan Pelanggan</h1>
    <p>Kode Pesanan: <strong><?= e($order['kode_pesanan']) ?></strong> · Pelanggan: <strong><?= e($order['nama_pelanggan']) ?></strong></p>
  </div>
  <a class="btn secondary" href="<?= url('kasir_pesanan') ?>">
    <i class="fa-solid fa-arrow-left"></i> Kembali ke Antrean
  </a>
</div>

<div class="panel">
  <div class="panel-head">
    <h2><i class="fa-solid fa-list-check" style="color: var(--shopee-primary);"></i> Rincian Produk Pesanan</h2>
    <span class="badge-status warning"><i class="fa-solid fa-clock"></i> Menunggu Kasir</span>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Nama Produk</th>
          <th style="text-align: center;">Jumlah</th>
          <th style="text-align: right;">Harga Satuan</th>
          <th style="text-align: right;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($order['items'] as $item): ?>
        <tr>
          <td><strong><?= e($item['nama_produk']) ?></strong></td>
          <td style="text-align: center;"><span class="badge-status success"><?= (int)$item['jumlah'] ?></span></td>
          <td style="text-align: right;"><?= rupiah($item['harga']) ?></td>
          <td style="text-align: right; font-weight: 700;"><?= rupiah($item['subtotal']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="panel">
  <h2><i class="fa-solid fa-id-card" style="color: var(--shopee-primary);"></i> Scan Kartu RFID & Proses Pembayaran</h2>
  
  <!-- RFID Scanner Box -->
  <div class="rfid-scanner-box">
    <div class="rfid-tap-icon">
      <i class="fa-solid fa-wifi" style="transform: rotate(90deg);"></i>
    </div>
    <h3 style="margin: 0 0 6px; font-size: 17px; font-weight: 800; color: var(--shopee-primary);">
      Tap / Scan Kartu RFID Member
    </h3>
    <p style="margin: 0 0 16px; font-size: 13px; color: var(--text-muted);">
      Tempelkan kartu RFID pada card reader USB untuk mendapatkan <strong>Diskon Member Otomatis 5%</strong>.
    </p>

    <?php if(!empty($order['rfid_uid'])): ?>
      <div style="margin-bottom: 14px;">
        <span style="font-size: 12px; color: #059669; font-weight: 700;">
          <i class="fa-solid fa-circle-check"></i> Pelanggan ini memiliki kartu terdaftar: <code><?= e($order['rfid_uid']) ?></code>
        </span>
        <div style="margin-top: 6px;">
          <button type="button" class="btn primary sm" onclick="simulateTapRfid('<?= e($order['rfid_uid']) ?>')">
            <i class="fa-solid fa-hand-pointer"></i> Simulasi Tap Kartu Member (<?= e($order['rfid_uid']) ?>)
          </button>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <form method="post" action="<?= url('kasir_proses') ?>" id="order-payment-form" data-preview-url="<?= e(url('kasir_preview',['id'=>$order['id_pesanan']])) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="id_pesanan" value="<?= (int)$order['id_pesanan'] ?>">

    <label>
      <span><i class="fa-solid fa-barcode"></i> UID Kartu RFID (Input Otomatis saat Tap)</span>
      <div style="display: flex; gap: 8px;">
        <input type="text" name="rfid_uid" id="checkout-rfid" maxlength="64" autocomplete="off" placeholder="Scan kartu RFID di sini atau ketik UID" style="font-family: monospace; font-size: 15px; font-weight: 700; letter-spacing: 1px;">
        <button type="button" class="btn secondary" onclick="document.getElementById('checkout-rfid').value=''; document.getElementById('checkout-rfid').dispatchEvent(new Event('input'));">
          Reset
        </button>
      </div>
    </label>

    <div id="rfid-preview-status" style="margin: 10px 0 16px; padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 700; background: #fbfbfb; border: 1px solid var(--border-light);">
      Menunggu scan kartu RFID member...
    </div>

    <!-- Ringkasan Tagihan Realtime -->
    <div class="table-wrap" style="margin-bottom: 20px;">
      <table>
        <tbody>
          <tr>
            <th style="width: 50%;">Subtotal Produk</th>
            <td id="checkout-subtotal" style="font-size: 15px; font-weight: 700;"><?= rupiah($preview['subtotal']) ?></td>
          </tr>
          <tr>
            <th>Diskon Member (5% Otomatis)</th>
            <td id="checkout-discount" style="font-size: 15px; font-weight: 800; color: #059669;"><?= rupiah($preview['discount']) ?></td>
          </tr>
          <tr>
            <th>Pajak (PPN 11%)</th>
            <td id="checkout-tax" style="font-size: 14px;"><?= rupiah($preview['tax']) ?></td>
          </tr>
          <tr style="background: var(--shopee-primary-light);">
            <th style="font-size: 16px; color: var(--shopee-primary);">TOTAL HARUS DIBAYAR</th>
            <td><strong id="checkout-total" style="font-size: 22px; color: var(--shopee-primary);"><?= rupiah($preview['total']) ?></strong></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pembayaran -->
    <div class="form-grid">
      <label>
        Metode Pembayaran
        <select name="metode_pembayaran" required style="font-weight: 700;">
          <option value="Tunai">💵 Uang Tunai (Cash)</option>
          <option value="QRIS">📱 QRIS / E-Wallet</option>
          <option value="Transfer">🏦 Transfer Bank</option>
        </select>
      </label>

      <label>
        Uang Diterima / Dibayar (Rp)
        <input type="number" name="uang_dibayar" id="inputUangDibayar" min="0" step="500" required placeholder="0" style="font-size: 16px; font-weight: 800; color: var(--text-main);">
        <div class="payment-quick-buttons">
          <button type="button" class="quick-pay-btn" id="btnUangPas">Uang Pas</button>
          <button type="button" class="quick-pay-btn" onclick="setCash(50000)">Rp 50.000</button>
          <button type="button" class="quick-pay-btn" onclick="setCash(100000)">Rp 100.000</button>
          <button type="button" class="quick-pay-btn" onclick="setCash(200000)">Rp 200.000</button>
        </div>
      </label>

      <div class="wide" id="kembalianWrap" style="display: none; padding: 12px 16px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: var(--radius-sm); font-size: 14px; font-weight: 700; color: #065f46;">
        Kembalian: <span id="kembalianAmount" style="font-size: 18px; font-weight: 900; color: #059669;">Rp 0</span>
      </div>
    </div>

    <div style="margin-top: 24px;">
      <button type="submit" class="btn primary full" id="checkout-submit" style="padding: 14px; font-size: 16px;">
        <i class="fa-solid fa-circle-check"></i> Selesaikan Pembayaran & Cetak Struk
      </button>
    </div>
  </form>
</div>

<script>
(() => {
  const form = document.getElementById('order-payment-form');
  const rfid = document.getElementById('checkout-rfid');
  const status = document.getElementById('rfid-preview-status');
  const submit = document.getElementById('checkout-submit');
  const uangDibayar = document.getElementById('inputUangDibayar');
  const kembalianWrap = document.getElementById('kembalianWrap');
  const kembalianAmount = document.getElementById('kembalianAmount');
  let currentTotalNumeric = <?= (float)$preview['total'] ?>;
  let timer;
  let requestId = 0;
  const money = value => new Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', maximumFractionDigits: 0}).format(value);

  window.simulateTapRfid = function(uid) {
    rfid.value = uid;
    rfid.dispatchEvent(new Event('input'));
  };

  window.setCash = function(nominal) {
    uangDibayar.value = nominal;
    uangDibayar.dispatchEvent(new Event('input'));
  };

  document.getElementById('btnUangPas').addEventListener('click', () => {
    uangDibayar.value = Math.round(currentTotalNumeric);
    uangDibayar.dispatchEvent(new Event('input'));
  });

  uangDibayar.addEventListener('input', () => {
    const val = parseFloat(uangDibayar.value) || 0;
    const diff = val - currentTotalNumeric;
    if (val >= currentTotalNumeric && currentTotalNumeric > 0) {
      kembalianWrap.style.display = 'block';
      kembalianAmount.textContent = money(diff);
    } else {
      kembalianWrap.style.display = 'none';
    }
  });

  async function updatePreview() {
    const currentRequest = ++requestId;
    submit.disabled = true;
    const url = form.dataset.previewUrl + '&rfid=' + encodeURIComponent(rfid.value.trim());
    try {
      const response = await fetch(url, {headers: {'Accept': 'application/json'}});
      const data = await response.json();
      if (currentRequest !== requestId) return;
      if (!response.ok) throw new Error(data.error || 'Kartu tidak dapat diverifikasi.');
      
      currentTotalNumeric = data.total;
      document.getElementById('checkout-subtotal').textContent = money(data.subtotal);
      document.getElementById('checkout-discount').textContent = money(data.discount);
      document.getElementById('checkout-tax').textContent = money(data.tax);
      document.getElementById('checkout-total').textContent = money(data.total);

      if (data.member) {
        status.style.background = '#ecfdf5';
        status.style.color = '#065f46';
        status.style.borderColor = '#a7f3d0';
        status.innerHTML = '<i class="fa-solid fa-circle-check" style="color: #059669;"></i> <strong>Kartu RFID Member Terverifikasi!</strong> Diskon member 5% (' + money(data.discount) + ') berhasil diterapkan secara otomatis.';
      } else {
        status.style.background = '#fbfbfb';
        status.style.color = 'var(--text-main)';
        status.style.borderColor = 'var(--border-light)';
        status.innerHTML = '<i class="fa-solid fa-circle-info" style="color: var(--text-muted);"></i> Tanpa scan kartu member; harga normal tanpa diskon member.';
      }
      
      // Update change calculation if cash input is filled
      if (uangDibayar.value) uangDibayar.dispatchEvent(new Event('input'));
      submit.disabled = false;
    } catch (error) {
      if (currentRequest !== requestId) return;
      status.style.background = '#fef2f2';
      status.style.color = '#991b1b';
      status.style.borderColor = '#fecaca';
      status.innerHTML = '<i class="fa-solid fa-circle-xmark" style="color: #dc2626;"></i> ' + error.message;
      submit.disabled = true;
    }
  }

  rfid.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(updatePreview, 250);
  });
  rfid.addEventListener('keydown', event => {
    if (event.key === 'Enter') {
      event.preventDefault();
      updatePreview();
    }
  });

  updatePreview();
})();
</script>

<?php require APP_PATH.'/views/layouts/footer.php'; ?>
