
<?php
require __DIR__ . "/includes/config.php";
require __DIR__ . "/includes/data.php";

$user = require_login();

$S = subjects();

// Temporary handling for missing enlistments table
try {
    $codes = enlisted_codes((int)$user['id']);
} catch (PDOException $e) {
    $codes = [];
}

$units = 0;
$lab = false;

foreach ($codes as $c) {
    if (isset($S[$c])) {
        $units += $S[$c]['units'] ?? 0;
        $lab = $lab || ($S[$c]['lab'] ?? false);
    }
}

$tuition = $units * TUITION_PER_UNIT;

$fees = [
    'Registration' => 300,
    'Library' => 250,
    'Athletics' => 200,
    'Medical and dental' => 150,
    'Student council' => 100,
    'ID and miscellaneous' => 150
];

if ($lab) {
    $fees['Laboratory'] = 600;
}

$total = $tuition + array_sum($fees);

$plan = [
    'Upon enrollment' => 0.40,
    'Prelim exam' => 0.20,
    'Midterm exam' => 0.20,
    'Final exam' => 0.20
];

$pageTitle = 'Tuition';
$active = 'tuition';

require __DIR__ . '/includes/header.php';
?>

<div class="two">

    <!-- TUITION ASSESSMENT -->
    <section class="card">

        <h3>Assessment · <?= e(TERM_LABEL) ?></h3>

        <?php if (!$codes): ?>

            <div class="empty">
                <p>
                    No assessment yet. Your tuition is computed
                    from the subjects you enlist.
                </p>

                <a class="btn primary" href="enlistment.php">
                    Go to enlistment
                </a>
            </div>

        <?php else: ?>

            <div class="table-wrap">
                <table>
                    <tbody>

                        <tr>
                            <td>
                                Tuition
                                (<?= $units ?> units ×
                                <?= peso(TUITION_PER_UNIT) ?>)
                            </td>

                            <td class="num">
                                <?= peso($tuition) ?>
                            </td>
                        </tr>

                        <?php foreach ($fees as $n => $a): ?>

                            <tr>
                                <td><?= e($n) ?></td>

                                <td class="num">
                                    <?= peso($a) ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                    <tfoot>
                        <tr>
                            <th>Total assessment</th>

                            <th class="num">
                                <?= peso($total) ?>
                            </th>
                        </tr>
                    </tfoot>

                </table>
            </div>

            <p class="muted small">
                Sample rates. Change TUITION_PER_UNIT
                in includes/data.php.
            </p>

        <?php endif; ?>

    </section>

    <!-- RIGHT SIDE -->
    <div class="col">

        <!-- BALANCE -->
        <section class="card balance">

            <span>Outstanding balance</span>

            <strong>
                <?= peso($total) ?>
            </strong>

            <small>No payments recorded yet</small>

        </section>

        <!-- PAYMENT SCHEDULE -->
        <section class="card">

            <h3>Payment schedule</h3>

            <?php if ($codes): ?>

                <ul class="plain">

                    <?php foreach ($plan as $l => $p): ?>

                        <li>

                            <span><?= e($l) ?></span>

                            <b>
                                <?= peso(round($total * $p, 2)) ?>
                            </b>

                            <em class="badge warn">
                                Unpaid
                            </em>

                        </li>

                    <?php endforeach; ?>

                </ul>

            <?php else: ?>

                <p class="muted">
                    Enlist subjects to see your payment schedule.
                </p>

            <?php endif; ?>

        </section>

        <!-- HOW TO PAY -->
        <section class="card">

            <h3>How to pay</h3>

            <details>
                <summary>Over the counter</summary>

                <p>
                    Pay at the Accounting Office and keep
                    the official receipt.
                </p>
            </details>

            <details>
                <summary>Bank deposit or online transfer</summary>

                <p>
                    Use the account details posted by the
                    Accounting Office. Send your proof of
                    payment to the office.
                </p>
            </details>

            <details>
                <summary>Scholarship or discount</summary>

                <p>
                    Bring your approval letter to the
                    Accounting Office before paying.
                </p>
            </details>

        </section>

    </div>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
