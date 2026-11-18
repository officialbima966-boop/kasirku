<?php require APP_PATH.'/views/layouts/main.php'; ?>

<div class="page-heading">
  <div>
    <h1><i class="fa-solid fa-cash-register" style="color: var(--shopee-primary);"></i> Kasir POS & Checkout Pesanan</h1>
    <p>Pilih produk dari etalase untuk ditambahkan ke keranjang belanja pelanggan.</p>
  </div>
  <div style="display: flex; gap: 8px; flex-wrap: wrap;">
    <?php if((current_user()['role'] ?? '') === 'kasir'): ?>
      <a href="<?= url('kasir_pesanan') ?>" class="btn secondary">
        <i class="fa-solid fa-clipboard-list"></i> Antrean Pembayaran
      </a>
    <?php endif; ?>
    <a href="<?= url('transaksi') ?>" class="btn secondary">
      <i class="fa-solid fa-arrow-left"></i> Riwayat Pesanan
    </a>
  </div>
</div>

<form method="post" action="<?= url('transaksi_simpan') ?>" id="transaction-form">
  <?= csrf_field() ?>
  
  <div class="pos-layout">
    <!-- Left Column: Visual Product Catalog -->
    <div class="pos-catalog-panel">
      <div class="panel-head">
        <h2><i class="fa-solid fa-store" style="color: var(--shopee-primary);"></i> Pilih Produk dari Etalase</h2>
        <span class="muted"><?= count($produk) ?> produk tersedia</span>
      </div>

      <div class="pos-search-wrap">
        <div class="shopee-search-bar" style="flex: 1;">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
          <input type="text" id="posSearchInput" placeholder="Ketik nama produk..." oninput="
            const q = this.value.toLowerCase();
            document.querySelectorAll('.pos-item-card').forEach(el => {
              el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
          ">
        </div>
      </div>

      <div class="pos-items-grid">
        <?php foreach($produk as $p): ?>
        <div class="pos-item-card">
          <div class="pos-item-title"><?= e($p['nama_produk']) ?></div>
          <div class="pos-item-stock"><i class="fa-solid fa-box"></i> Stok: <?= $p['stok'] ?></div>
          <div class="pos-item-price"><?= rupiah($p['harga']) ?></div>
          <button type="button" 
                  class="btn primary sm btn-add-pos-item" 
                  style="margin-top: 10px;"
                  data-id="<?= $p['id_produk'] ?>"
                  data-name="<?= e($p['nama_produk']) ?>"
                  data-price="<?= $p['harga'] ?>"
                  data-stock="<?= $p['stok'] ?>"
                  data-barcode="<?= e($p['barcode'] ?? '') ?>">
            <i class="fa-solid fa-plus"></i> Tambah
          </button>
        </div>
        <?php endforeach; ?>
        <?php if(!$produk): ?>
          <div class="empty" style="grid-column: 1 / -1;">
            Tidak ada produk dengan stok tersedia.
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Right Column: Shopee Order Summary / Cart -->
    <div class="pos-cart-panel">
      <div class="cart-header">
        <h2><i class="fa-solid fa-cart-shopping" style="color: var(--shopee-primary);"></i> Keranjang Pesanan</h2>
        <span class="badge-status success"><i class="fa-solid fa-bolt"></i> Kasir Aktif</span>
      </div>

      <label class="barcode-scan-label">
        <span><i class="fa-solid fa-barcode" style="color: var(--shopee-primary);"></i> Scan Barcode Produk</span>
        <input type="text" id="pos-barcode-input" autocomplete="off" autocapitalize="off" spellcheck="false" autofocus placeholder="Klik lalu scan barcode produk">
        <small>Scanner USB/Bluetooth berfungsi seperti keyboard; tekan Enter bila scanner tidak mengirim akhiran otomatis.</small>
      </label>
      <div id="pos-barcode-status" class="barcode-scan-status" role="status" aria-live="polite"></div>

      <!-- Customer Selector -->
      <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 700; margin-bottom: 16px;">
        <span><i class="fa-solid fa-user" style="color: var(--shopee-primary); margin-right: 4px;"></i> Pilih Pelanggan</span>
        <select name="id_pelanggan" required style="padding: 10px 12px; font-weight: 600;">
          <?php foreach($pelanggan as $p): ?>
            <option value="<?= $p['id_pelanggan'] ?>">
              <?= e($p['nama_pelanggan']) ?> <?= !empty($p['kota']) ? '('.e($p['kota']).')' : '' ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 700; margin-bottom: 16px;">
        <span><i class="fa-solid fa-credit-card" style="color: var(--shopee-primary); margin-right: 4px;"></i> Metode Pembayaran</span>
        <select name="metode_pembayaran" required style="padding: 10px 12px; font-weight: 600;">
          <option value="Tunai">Tunai</option>
          <option value="Transfer">Transfer</option>
          <option value="QRIS">QRIS</option>
        </select>
      </label>

      <!-- Cart Item List -->
      <div id="pos-cart-list" class="cart-items-list">
        <!-- Rendered by JS -->
      </div>

      <div id="pos-cart-empty" style="text-align: center; padding: 30px 16px; color: var(--text-light); background: #fafafa; border-radius: var(--radius-sm); border: 1px dashed var(--border-medium); margin-bottom: 16px;">
        <i class="fa-solid fa-basket-shopping" style="font-size: 36px; color: #ddd; margin-bottom: 8px; display: block;"></i>
        Keranjang masih kosong.<br><small>Pilih produk dari etalase untuk mulai.</small>
      </div>

      <!-- Hidden inputs populated dynamically for POS items -->
      <div id="pos-hidden-inputs"></div>

      <!-- Voucher / Diskon -->
      <label style="display: flex; flex-direction: column; gap: 6px; font-weight: 700; margin-bottom: 16px;">
        <span><i class="fa-solid fa-ticket" style="color: var(--shopee-primary); margin-right: 4px;"></i> Voucher Diskon (Rp)</span>
        <input type="number" name="diskon" id="pos-discount-input" min="0" value="0" placeholder="0">
      </label>

      <!-- Price Breakdown -->
      <div class="cart-summary">
        <div class="summary-row">
          <span>Subtotal Produk</span>
          <strong id="pos-subtotal">Rp 0</strong>
        </div>
        <div class="summary-row">
          <span>Pajak (PPN 11%)</span>
          <strong id="pos-tax">Rp 0</strong>
        </div>
        <div class="summary-row total-row">
          <span>Total Pembayaran</span>
          <span class="total-amount" id="pos-total">Rp 0</span>
        </div>
        <label class="payment-input-label">
          <span>Uang Dibayar (Rp)</span>
          <input type="number" name="uang_dibayar" id="pos-cash-input" min="0" step="1" inputmode="numeric" placeholder="Masukkan uang dari pelanggan" required>
        </label>
        <div class="payment-quick-buttons">
          <button type="button" class="quick-pay-btn" id="pos-btn-uang-pas">💵 Uang Pas</button>
          <button type="button" class="quick-pay-btn" onclick="window.posSetCash(10000)">10.000</button>
          <button type="button" class="quick-pay-btn" onclick="window.posSetCash(20000)">20.000</button>
          <button type="button" class="quick-pay-btn" onclick="window.posSetCash(50000)">50.000</button>
          <button type="button" class="quick-pay-btn" onclick="window.posSetCash(100000)">100.000</button>
        </div>
        <div class="summary-row change-row" id="pos-change-row">
          <span id="pos-change-label">Kembalian</span>
          <strong id="pos-change" aria-live="polite">Rp 0</strong>
        </div>
      </div>

      <div class="cart-guarantee-note">
        <i class="fa-solid fa-shield-halved"></i>
        <span>Garansi Shopee: Pembayaran aman & stok otomatis terpotong.</span>
      </div>

      <button type="submit" class="btn primary full" style="padding: 13px; font-size: 15px;">
        <i class="fa-solid fa-check-to-slot"></i> Proses & Simpan Transaksi
      </button>
    </div>
  </div>
</form>

<?php require APP_PATH.'/views/layouts/footer.php'; ?>
