<?php
require __DIR__ . "/includes/config.php";
require __DIR__ . "/includes/data.php";
$user = require_login();
$terms = sample_grades();
$pageTitle = 'Grades'; $active = 'grades';
require __DIR__ . '/includes/header.php';
?>
<div class="alert info">These are sample grades so you can see how this page works. Real grades will show here once your instructors post them.</div>
<section class="card">
  <div class="card-head"><h3>Grade report</h3>
    <select id="termSel" aria-label="Select term"><?php foreach (array_keys($terms) as $i => $t): ?><option value="t<?= $i ?>"><?= e($t) ?></option><?php endforeach; ?></select>
  </div>
  <?php foreach ($terms as $i => $rows):
    $idx = array_search($i, array_keys($terms), true);
    $u = array_sum(array_column($rows, 2));
    $gwa = $u ? array_sum(array_map(fn($r) => $r[2] * $r[3], $rows)) / $u : 0; ?>
    <div class="term-panel" data-term="t<?= $idx ?>"<?= $idx ? ' hidden' : '' ?>>
      <div class="table-wrap"><table><thead><tr><th>Code</th><th>Subject</th><th>Units</th><th>Grade</th><th>Remarks</th></tr></thead><tbody>
        <?php foreach ($rows as [$c, $t, $un, $g]): ?>
          <tr><td><b><?= e($c) ?></b></td><td><?= e($t) ?></td><td><?= $un ?></td><td><b><?= number_format($g, 2) ?></b></td><td><span class="badge ok"><?= $g <= 3.0 ? 'Passed' : 'Failed' ?></span></td></tr>
        <?php endforeach; ?></tbody>
        <tfoot><tr><th colspan="2">General weighted average</th><th><?= $u ?></th><th colspan="2"><?= number_format($gwa, 2) ?></th></tr></tfoot>
      </table></div>
    </div>
  <?php endforeach; ?>
</section>
<?php require __DIR__ . '/includes/footer.php';
