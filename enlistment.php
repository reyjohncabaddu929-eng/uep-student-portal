
<?php
require __DIR__ . "/includes/config.php";
require __DIR__ . "/includes/data.php";

$user = require_login();
$S = subjects();
$uid = (int)$user['id'];

$codes = [];
$units = 0;

try {

    // HANDLE ENLISTMENT AND DROP
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        csrf_check();

        $code = (string)($_POST['code'] ?? '');
        $act  = (string)($_POST['action'] ?? '');

        $codes = enlisted_codes($uid);

        if (!isset($S[$code])) {

            flash('err', 'Subject not found.');

        } elseif ($act === 'drop') {

            db()->prepare(
                'DELETE FROM enlistments
                 WHERE user_id = ? AND subject_code = ?'
            )->execute([$uid, $code]);

            flash('ok', "$code dropped.");

        } elseif ($act === 'enlist') {

            $units = array_sum(array_map(
                fn($c) => $S[$c]['units'] ?? 0,
                $codes
            ));

            if (in_array($code, $codes, true)) {

                flash('err', "$code is already enlisted.");

            } elseif (
                $units + $S[$code]['units'] > MAX_UNITS
            ) {

                flash(
                    'err',
                    'This would go over the ' .
                    MAX_UNITS .
                    '-unit limit. Drop a subject first.'
                );

            } elseif (
                ($cf = find_conflict($code, $codes)) !== null
            ) {

                flash(
                    'err',
                    "$code conflicts with $cf. Choose a different section or drop $cf."
                );

            } else {

                db()->prepare(
                    'INSERT INTO enlistments
                     (user_id, subject_code)
                     VALUES (?, ?)'
                )->execute([$uid, $code]);

                flash('ok', "$code enlisted.");
            }

        } else {

            flash('err', 'Invalid action.');
        }

        redirect('enlistment.php');
    }

    // GET ENLISTED SUBJECTS
    $codes = enlisted_codes($uid);

} catch (PDOException $e) {

    $codes = [];

    flash(
        'err',
        'Database error: The enlistments table is missing or unavailable. Please check your database.'
    );

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        redirect('enlistment.php');
    }
}

// COMPUTE TOTAL UNITS
$units = array_sum(array_map(
    fn($c) => $S[$c]['units'] ?? 0,
    $codes
));

$pageTitle = 'Enlistment';
$active = 'enlistment';

require __DIR__ . '/includes/header.php';
?>

<div class="two enl">

  <!-- AVAILABLE SUBJECTS -->
  <section class="card">

    <div class="card-head">

      <h3>Available subjects</h3>

      <input
        class="search"
        id="filter"
        type="search"
        placeholder="Search code or title"
        aria-label="Search subjects"
      >

    </div>

    <div class="table-wrap">

      <table id="subjTable">

        <thead>
          <tr>
            <th>Code</th>
            <th>Subject</th>
            <th>Units</th>
            <th>Schedule</th>
            <th>Room</th>
            <th></th>
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
              <?= e(implode('/', $s['days'])) ?>
              ·
              <?= t12($s['start']) ?>–<?= t12($s['end']) ?>
            </td>

            <td>
              <?= e($s['room']) ?>
            </td>

            <td>

              <form method="post">

                <?= csrf_field() ?>

                <input
                  type="hidden"
                  name="code"
                  value="<?= e($c) ?>"
                >

                <?php if ($on): ?>

                  <button
                    class="btn ghost sm"
                    name="action"
                    value="drop"
                  >
                    Drop
                  </button>

                <?php else: ?>

                  <button
                    class="btn primary sm"
                    name="action"
                    value="enlist"
                  >
                    Enlist
                  </button>

                <?php endif; ?>

              </form>

            </td>

          </tr>

        <?php endforeach; ?>

        </tbody>

      </table>

    </div>

  </section>

  <!-- MY ENLISTMENT -->
  <aside class="card sticky">

    <h3>My enlistment</h3>

    <div class="meter">

      <div
        style="width:<?= min(
            100,
            round($units / MAX_UNITS * 100)
        ) ?>%"
      ></div>

    </div>

    <p class="muted small">
      <?= $units ?> of <?= MAX_UNITS ?> units
    </p>

    <?php if (!$codes): ?>

      <p class="muted">
        Nothing enlisted yet. Pick subjects from the list.
      </p>

    <?php else: ?>

      <ul class="plain">

        <?php foreach ($codes as $c): ?>

          <?php if (isset($S[$c])): ?>

            <li>

              <span>
                <b><?= e($c) ?></b>
                <?= e($S[$c]['title']) ?>
              </span>

              <b>
                <?= (int)$S[$c]['units'] ?>u
              </b>

            </li>

          <?php endif; ?>

        <?php endforeach; ?>

      </ul>

      <a
        class="btn primary block"
        href="schedule.php"
      >
        View schedule
      </a>

    <?php endif; ?>

  </aside>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
