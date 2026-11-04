
<?php
require __DIR__ . "/includes/config.php";
require __DIR__ . "/includes/data.php";

$user = require_login();

$S = subjects();
$N = next_semester();

// Temporary handling for missing enlistments table
try {
    $codes = enlisted_codes((int)$user['id']);
} catch (PDOException $e) {
    $codes = [];
}

// COMPUTE CURRICULUM UNITS
$totalU = array_sum(array_column($S, 'units'))
        + array_sum(array_column($N, 1));

$doneU = array_sum(array_map(
    fn($c) => $S[$c]['units'] ?? 0,
    $codes
));

$pct = $totalU ? round($doneU / $totalU * 100) : 0;

$pageTitle = 'Evaluation';
$active = 'evaluation';

require __DIR__ . '/includes/header.php';
?>

<!-- CURRICULUM PROGRESS -->
<section class="card">

    <h3>
        Curriculum progress · <?= e($user['course']) ?>
    </h3>

    <div class="meter lg">
        <div style="width:<?= $pct ?>%"></div>
    </div>

    <p class="muted small">
        <?= $doneU ?> of <?= $totalU ?> units enlisted or taken
        (<?= $pct ?>%). Sample curriculum.
    </p>

</section>

<div class="two">

    <!-- YEAR 1 FIRST SEMESTER -->
    <section class="card">

        <h3>Year 1 · 1st Semester</h3>

        <div class="table-wrap">

            <table>

                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Subject</th>
                        <th>Units</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($S as $c => $s): ?>

                    <?php $on = in_array($c, $codes, true); ?>

                    <tr>

                        <td>
                            <b><?= e($c) ?></b>
                        </td>

                        <td>
                            <?= e($s['title']) ?>
                        </td>

                        <td>
                            <?= (int)$s['units'] ?>
                        </td>

                        <td>

                            <span class="badge <?= $on ? 'ok' : 'muted' ?>">
                                <?= $on ? 'Enlisted' : 'Not yet taken' ?>
                            </span>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </section>

    <!-- YEAR 1 SECOND SEMESTER -->
    <section class="card">

        <h3>Year 1 · 2nd Semester</h3>

        <div class="table-wrap">

            <table>

                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Subject</th>
                        <th>Units</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($N as $c => [$t, $u]): ?>

                    <tr>

                        <td>
                            <b><?= e($c) ?></b>
                        </td>

                        <td>
                            <?= e($t) ?>
                        </td>

                        <td>
                            <?= (int)$u ?>
                        </td>

                        <td>

                            <span class="badge muted">
                                Next semester
                            </span>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </section>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
