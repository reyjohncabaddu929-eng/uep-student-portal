<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/data.php';
$user = require_login();
$pageTitle = 'Clearance'; $active = 'clearance';
require __DIR__ . '/includes/header.php';
$offices = clearance_offices();
?>
<section class="card">
  <div class="card-head"><h3>Clearance status</h3><span class="badge muted" id="clrCount">0 of <?= count($offices) ?> cleared</span></div>
  <div class="meter lg"><div id="clrBar" style="width:0%"></div></div>
  <p class="muted small">Request clearance from each office. Status updates are stored in this browser for the demo.</p>
</section>
<section class="card">
  <ul class="rows" id="clrList" data-user="<?= (int)$user['id'] ?>">
    <?php foreach ($offices as $name => $desc): ?>
      <li data-office="<?= e($name) ?>">
        <div><b><?= e($name) ?></b><p class="muted small"><?= e($desc) ?></p></div>
        <span class="badge warn status">Not requested</span>
        <button class="btn primary sm act" type="button">Request</button>
      </li>
    <?php endforeach; ?>
  </ul>
</section>
<?php require __DIR__ . '/includes/footer.php';
