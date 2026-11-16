<?php require APP_PATH.'/views/layouts/main.php'; ?>

<div class="page-heading">
  <div>
    <h1><i class="fa-solid fa-clock-rotate-left" style="color: var(--shopee-primary);"></i> Log Aktivitas & Trigger Database</h1>
    <p>Catatan otomatis setiap perubahan data pelanggan dan transaksi yang direkam oleh MySQL Trigger.</p>
  </div>
</div>

<div class="panel">
  <div class="panel-head">
    <h2><i class="fa-solid fa-list-check" style="color: var(--shopee-primary);"></i> Catatan Aktivitas Terkini</h2>
    <span class="badge-status success"><?= count($logs) ?> entri tercatat</span>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Aktivitas</th>
          <th>Deskripsi Perubahan</th>
          <th>Waktu Kejadian</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($logs as $l): ?>
        <tr>
          <td><strong>#<?= $l['id_log'] ?></strong></td>
          <td>
            <span class="badge-status <?= strpos($l['aktivitas'], 'INSERT') !== false ? 'success' : (strpos($l['aktivitas'], 'UPDATE') !== false ? 'warning' : 'danger') ?>">
              <?= e($l['aktivitas']) ?>
            </span>
          </td>
          <td><?= e($l['deskripsi']) ?></td>
          <td>
            <i class="fa-regular fa-clock" style="color: var(--text-light); margin-right: 4px;"></i>
            <?= e($l['waktu']) ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(!$logs): ?>
          <tr><td colspan="4" class="empty">Belum ada aktivitas tercatat di log trigger.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require APP_PATH.'/views/layouts/footer.php'; ?>
