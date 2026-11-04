
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

// WEEK DAYS
$week = [
    'Mon' => 'Monday',
    'Tue' => 'Tuesday',
    'Wed' => 'Wednesday',
    'Thu' => 'Thursday',
    'Fri' => 'Friday',
    'Sat' => 'Saturday'
];

// ORGANIZE SUBJECTS BY DAY
$byDay = array_fill_keys(array_keys($week), []);

foreach ($codes as $c) {

    if (!isset($S[$c])) {
        continue;
    }

    foreach ($S[$c]['days'] as $d) {

        if (isset($byDay[$d])) {
            $byDay[$d][] = [$c, $S[$c]];
        }

    }
}

// SORT CLASSES BY START TIME
foreach ($byDay as &$list) {

    usort(
        $list,
        fn($a, $b) => strcmp(
            $a[1]['start'],
            $b[1]['start']
        )
    );

}

unset($list);

$pageTitle = 'Schedule';
$active = 'schedule';

require __DIR__ . '/includes/header.php';
?>

<!-- PAGE ACTIONS -->
<div class="card-head page-actions">

    <p class="muted">
        <?= e(TERM_LABEL) ?>
    </p>

    <button
        class="btn ghost"
        onclick="window.print()"
    >
        Print schedule
    </button>

</div>

<?php if (!$codes): ?>

    <!-- EMPTY SCHEDULE -->
    <section class="card">

        <div class="empty">

            <p>
                Your schedule is empty.
                Enlist subjects and they will appear here.
            </p>

            <a
                class="btn primary"
                href="enlistment.php"
            >
                Go to enlistment
            </a>

        </div>

    </section>

<?php else: ?>

    <!-- WEEKLY SCHEDULE -->
    <div class="week">

        <?php foreach ($week as $k => $name): ?>

            <section class="day">

                <h3>
                    <?= e($name) ?>
                </h3>

                <?php if (!$byDay[$k]): ?>

                    <p class="muted small">
                        No class
                    </p>

                <?php endif; ?>

                <?php foreach ($byDay[$k] as [$c, $s]): ?>

                    <article class="class">

                        <b>
                            <?= e($c) ?>
                        </b>

                        <span>
                            <?= e($s['title']) ?>
                        </span>

                        <small>
                            <?= t12($s['start']) ?>–<?= t12($s['end']) ?>
                            ·
                            <?= e($s['room']) ?>
                        </small>

                    </article>

                <?php endforeach; ?>

            </section>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
