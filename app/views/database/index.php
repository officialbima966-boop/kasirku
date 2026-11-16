<?php require APP_PATH.'/views/layouts/main.php'; ?>

<div class="page-heading">
  <div>
    <h1><i class="fa-solid fa-database" style="color: var(--shopee-primary);"></i> Fitur Advanced Database Programming</h1>
    <p>Demonstrasi hasil View, Stored Procedure, Stored Function, Trigger, JOIN, dan Subquery analitis toko.</p>
  </div>
</div>

<?php foreach($results as $label => $rows): ?>
<div class="panel">
  <div class="panel-head">
    <h2><i class="fa-solid fa-bolt" style="color: var(--shopee-primary);"></i> <?= e($label) ?></h2>
    <?php if(is_array($rows) && !isset($rows['error'])): ?>
      <span class="badge-status success"><i class="fa-solid fa-check"></i> <?= count($rows) ?> baris data</span>
    <?php endif; ?>
  </div>
  
  <?php if(isset($rows['error'])): ?>
    <div class="shopee-alert error">
      <i class="fa-solid fa-circle-exclamation"></i>
      <div class="alert-text"><?= e($rows['error']) ?> — pastikan skrip SQL database telah terpasang.</div>
    </div>
  <?php elseif(!$rows): ?>
    <p class="empty">Belum ada data untuk query ini.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <?php foreach(array_keys($rows[0]) as $col): ?>
              <th><?= e($col) ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach($rows as $row): ?>
          <tr>
            <?php foreach($row as $v): ?>
              <td><?= e($v) ?></td>
            <?php endforeach; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<?php endforeach; ?>

<?php require APP_PATH.'/views/layouts/footer.php'; ?>
