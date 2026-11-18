<?php require APP_PATH.'/views/layouts/main.php'; ?>

<style>
  /* Kartu Member RFID Digital Shopee */
  .rfid-card-wrapper {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 24px;
    align-items: center;
  }
  .rfid-digital-card {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #334155 100%);
    border-radius: 16px;
    padding: 24px;
    color: #fff;
    box-shadow: 0 14px 28px rgba(0,0,0,0.22), 0 10px 10px rgba(0,0,0,0.18);
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 215, 0, 0.35);
    aspect-ratio: 1.586 / 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .rfid-digital-card::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(238, 77, 45, 0.4) 0%, rgba(238, 77, 45, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
  }
  .rfid-card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 2;
  }
  .card-logo {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 800;
    font-size: 15px;
    color: #ffd700;
    letter-spacing: 1px;
  }
  .card-chip-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .card-emv-chip {
    width: 38px;
    height: 28px;
    background: linear-gradient(135deg, #fce043 0%, #fbab7e 100%);
    border-radius: 5px;
    border: 1px solid #c99321;
    position: relative;
  }
  .card-emv-chip::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: rgba(0,0,0,0.25);
  }
  .card-contactless {
    font-size: 20px;
    color: rgba(255, 255, 255, 0.85);
    transform: rotate(90deg);
  }
  .rfid-card-mid {
    margin: 12px 0;
    position: relative;
    z-index: 2;
  }
  .rfid-card-uid-label {
    font-size: 10px;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 1.5px;
  }
  .rfid-card-uid {
    font-size: 20px;
    font-weight: 800;
    font-family: 'Courier New', Courier, monospace;
    letter-spacing: 3px;
    color: #fff;
    text-shadow: 0 2px 4px rgba(0,0,0,0.6);
  }
  .rfid-card-bottom {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    position: relative;
    z-index: 2;
  }
  .card-holder-name {
    font-size: 13.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #f8fafc;
  }
  .card-benefit-badge {
    background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%);
    color: #1e293b;
    font-weight: 900;
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 4px;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 4px;
  }
  @media(max-width: 800px) {
    .rfid-card-wrapper {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="page-heading">
  <div>
    <h1><i class="fa-solid fa-basket-shopping" style="color: var(--shopee-primary);"></i> Belanja Member & Kartu RFID</h1>
    <p>Halo, <strong><?= e($customer['nama_pelanggan']) ?></strong>! Pilih produk untuk dipesan, lalu lakukan pembayaran di kasir.</p>
  </div>
  <a class="btn secondary" href="<?= url('member_pesanan') ?>">
    <i class="fa-solid fa-receipt"></i> Pesanan Saya (<?= count($orders) ?>)
  </a>
</div>

<!-- Kartu Member RFID Section -->
<div class="panel">
  <div class="panel-head">
    <h2><i class="fa-solid fa-id-card" style="color: var(--shopee-primary);"></i> Kartu Member RFID Pelanggan</h2>
    <span class="badge-status success"><i class="fa-solid fa-percent"></i> Diskon Otomatis 5% Setiap Belanja</span>
  </div>

  <div class="rfid-card-wrapper">
    <!-- Visual RFID Card -->
    <div class="rfid-digital-card">
      <div class="rfid-card-top">
        <div class="card-logo">
          <i class="fa-solid fa-bag-shopping"></i> KASIRKU VIP
        </div>
        <div class="card-chip-wrap">
          <div class="card-emv-chip"></div>
          <i class="fa-solid fa-wifi card-contactless" title="RFID Contactless Wave"></i>
        </div>
      </div>

      <div class="rfid-card-mid">
        <div class="rfid-card-uid-label">RFID CARD UID</div>
        <div class="rfid-card-uid">
          <?= !empty($customer['rfid_uid']) ? e($customer['rfid_uid']) : 'BELUM TERHUBUNG' ?>
        </div>
      </div>

      <div class="rfid-card-bottom">
        <div>
          <div style="font-size: 9px; color: #94a3b8; letter-spacing: 1px;">NAMA MEMBER</div>
          <div class="card-holder-name"><?= e($customer['nama_pelanggan']) ?></div>
        </div>
        <div class="card-benefit-badge">
          <i class="fa-solid fa-crown"></i> DISKON 5% MEMBER
        </div>
      </div>
    </div>

    <!-- RFID Info & Link Form -->
    <div style="display: flex; flex-direction: column; gap: 12px;">
      <h3 style="margin: 0; font-size: 16px; color: var(--text-main);">
        <i class="fa-solid fa-shield-halved" style="color: var(--shopee-primary);"></i> Status Kartu RFID Anda
      </h3>
      <?php if(!empty($customer['rfid_uid'])): ?>
        <p style="margin: 0; color: #059669; font-weight: 700;">
          <i class="fa-solid fa-circle-check"></i> Kartu RFID Anda telah terhubung aktif: <code><?= e($customer['rfid_uid']) ?></code>
        </p>
        <p style="margin: 0; color: var(--text-muted); font-size: 13px;">
          Saat Anda memesan produk dan membayar di kasir, kasir akan men-tap/scan kartu ini. Diskon 5% akan otomatis langsung terpotong dari total tagihan!
        </p>
      <?php else: ?>
        <p style="margin: 0; color: #d97706; font-weight: 700;">
          <i class="fa-solid fa-circle-exclamation"></i> Anda belum menghubungkan kartu RFID member.
        </p>
        <p style="margin: 0; color: var(--text-muted); font-size: 13px;">
          Tempelkan kartu RFID pada card reader USB yang terhubung, atau klik tombol "Gunakan UID Demo" di bawah untuk menghubungkan kartu langsung.
        </p>
      <?php endif; ?>

      <form method="post" action="<?= url('member_kartu') ?>" style="margin-top: 6px;">
        <?= csrf_field() ?>
        <label style="font-weight: 700; font-size: 12.5px; margin-bottom: 6px; display: block;">
          Hubungkan / Ganti UID Kartu RFID:
        </label>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
          <input type="text" name="rfid_uid" id="memberRfidInput" maxlength="64" required autocomplete="off" placeholder="Scan kartu RFID atau klik Demo" style="flex: 1; min-width: 200px;">
          <button type="button" class="btn secondary" onclick="document.getElementById('memberRfidInput').value='RFID-'+Math.random().toString(36).substring(2,8).toUpperCase();">
            <i class="fa-solid fa-wand-magic-sparkles"></i> UID Demo
          </button>
          <button class="btn primary" type="submit">
            <i class="fa-solid fa-link"></i> Simpan Kartu
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Katalog & Pemesanan Produk -->
<div class="panel">
  <div class="panel-head">
    <h2><i class="fa-solid fa-store" style="color: var(--shopee-primary);"></i> Etalase Produk Siap Beli</h2>
    <span class="muted"><?= count($products) ?> produk tersedia untuk dipesan</span>
  </div>

  <form method="post" action="<?= url('member_pesan') ?>">
    <?= csrf_field() ?>
    
    <div class="shopee-product-grid" style="grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));">
      <?php foreach($products as $idx => $product): 
        $discounts = [10, 15, 20, 25];
        $discPct = $discounts[$idx % 4];
        $oriPrice = $product['harga'] / (1 - ($discPct / 100));
        $pid = (int)$product['id_produk'];
      ?>
      <div class="shopee-product-card" id="card_<?= $pid ?>">
        <div class="product-thumb-wrap">
          <span class="card-badge-mall">STAR+</span>
          <span class="card-badge-discount">-<?= $discPct ?>%</span>
          <i class="fa-solid fa-box-open product-thumb-icon"></i>
        </div>
        <div class="product-card-body">
          <div class="product-card-title"><?= e($product['nama_produk']) ?></div>
          <div class="product-price-row">
            <span class="original-price"><?= rupiah($oriPrice) ?></span>
            <span class="shopee-price"><?= rupiah($product['harga']) ?></span>
          </div>
          <div class="product-meta-row">
            <span class="product-rating"><i class="fa-solid fa-star"></i> 4.9</span>
            <span class="stock-tag">Stok: <?= (int)$product['stok'] ?></span>
          </div>
          
          <div style="background: #fbfbfb; padding: 10px; border-radius: var(--radius-sm); border: 1px solid var(--border-light); margin-top: 8px;">
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 700; cursor: pointer;">
              <input type="checkbox" name="produk[]" value="<?= $pid ?>" id="chk_<?= $pid ?>" style="width: 18px; height: 18px;">
              <span>Beli Produk Ini</span>
            </label>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px;">
              <span style="font-size: 12px; color: var(--text-muted);">Jumlah:</span>
              <input type="number" name="jumlah[<?= $pid ?>]" min="1" max="<?= (int)$product['stok'] ?>" value="1" style="width: 80px; padding: 6px; text-align: center;" oninput="document.getElementById('chk_<?= $pid ?>').checked = true;">
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if(!$products): ?>
        <div class="empty" style="grid-column: 1 / -1;">
          Belum ada produk dengan stok tersedia saat ini.
        </div>
      <?php endif; ?>
    </div>

    <?php if($products): ?>
      <div style="position: sticky; bottom: 20px; background: rgba(255,255,255,0.95); backdrop-filter: blur(8px); padding: 16px 24px; border-radius: var(--radius-md); box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: space-between; margin-top: 24px; z-index: 100;">
        <div>
          <div style="font-weight: 800; font-size: 15px; color: var(--text-main);">
            <i class="fa-solid fa-circle-info" style="color: var(--shopee-primary);"></i> Siap Memesan?
          </div>
          <small style="color: var(--text-muted);">Centang produk yang ingin dibeli, lalu kirimkan ke kasir untuk proses pembayaran.</small>
        </div>
        <button class="btn primary" type="submit" style="padding: 12px 28px; font-size: 15px;">
          <i class="fa-solid fa-paper-plane"></i> Kirim Pesanan ke Kasir
        </button>
      </div>
    <?php endif; ?>
  </form>
</div>

<!-- Pesanan Terbaru Pelanggan -->
<div class="panel">
  <div class="panel-head">
    <h2><i class="fa-solid fa-clock-rotate-left" style="color: var(--shopee-primary);"></i> Pesanan Saya Terbaru</h2>
    <a href="<?= url('member_pesanan') ?>" class="btn secondary sm">Lihat Semua Pesanan →</a>
  </div>
  <?php if($orders): ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Kode Pesanan</th>
            <th>Waktu Dibuat</th>
            <th>Status</th>
            <th>Estimasi Subtotal</th>
            <th>Catatan</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach(array_slice($orders, 0, 5) as $order): ?>
          <tr>
            <td><strong><?= e($order['kode_pesanan']) ?></strong></td>
            <td><?= e($order['dibuat_pada']) ?></td>
            <td>
              <?php if($order['status'] === 'menunggu'): ?>
                <span class="badge-status warning"><i class="fa-solid fa-clock"></i> Menunggu Kasir</span>
              <?php elseif($order['status'] === 'dibayar'): ?>
                <span class="badge-status success"><i class="fa-solid fa-check"></i> Selesai (Lunas)</span>
              <?php else: ?>
                <span class="badge-status danger"><?= e(ucfirst($order['status'])) ?></span>
              <?php endif; ?>
            </td>
            <td><strong style="color: var(--shopee-primary);"><?= rupiah($order['subtotal']) ?></strong></td>
            <td>
              <?php if($order['status'] === 'menunggu'): ?>
                <span style="font-size: 12px; color: var(--shopee-primary);">Silakan sebutkan kode ini di kasir untuk bayar.</span>
              <?php else: ?>
                <span style="font-size: 12px; color: #059669;">Pembayaran berhasil diproses.</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p class="empty">Belum ada riwayat pesanan.</p>
  <?php endif; ?>
</div>

<?php require APP_PATH.'/views/layouts/footer.php'; ?>
