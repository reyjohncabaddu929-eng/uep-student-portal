<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/data.php';
$user = require_login();
$pageTitle = 'Student Requirements'; $active = 'requirements';
require __DIR__ . '/includes/header.php';
$reqs = requirement_list();
?>
<section class="card">
  <div class="card-head"><h3>Document checklist</h3><span class="badge muted" id="reqCount">0 of <?= count($reqs) ?> submitted</span></div>
  <div class="meter lg"><div id="reqBar" style="width:0%"></div></div>
  <p class="muted small">Demo mode: choosing a file marks it as submitted in this browser. Files are not uploaded to a server.</p>
</section>
<section class="card">
  <ul class="rows" id="reqList" data-user="<?= (int)$user['id'] ?>">
    <?php foreach ($reqs as $key => [$name, $desc]): ?>
      <li data-key="<?= e($key) ?>">
        <div><b><?= e($name) ?></b><p class="muted small"><?= e($desc) ?></p><p class="small fname"></p></div>
        <span class="badge warn status">Missing</span>
        <label class="btn primary sm"><?= icon('upload') ?> Choose file<input type="file" hidden accept=".pdf,.jpg,.jpeg,.png"></label>
      </li>
    <?php endforeach; ?>
  </ul>
</section>
<?php require __DIR__ . '/includes/footer.php';
