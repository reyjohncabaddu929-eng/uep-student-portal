
<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/data.php';

$user = require_login();
$uid = (int)$user['id'];

$offices = offices_for_evaluation();
$mine = [];
$dbError = false;

// HANDLE FORM SUBMISSION
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    csrf_check();

    $office = (string)($_POST['office'] ?? '');
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = trim((string)($_POST['comment'] ?? ''));

    if (!in_array($office, $offices, true)) {

        flash('err', 'Choose an office to evaluate.');

    } elseif ($rating < 1 || $rating > 5) {

        flash('err', 'Select a rating from 1 to 5.');

    } elseif (mb_strlen($comment) > 1000) {

        flash('err', 'Comments are limited to 1,000 characters.');

    } else {

        try {

            db()->prepare(
                'INSERT INTO service_evaluations
                (user_id, office, rating, comment)
                VALUES (?, ?, ?, ?)'
            )->execute([
                $uid,
                $office,
                $rating,
                $comment ?: null
            ]);

            flash('ok', 'Thank you. Your evaluation was submitted.');

        } catch (PDOException $e) {

            flash(
                'err',
                'Database error. Please check the service_evaluations table.'
            );

        }

    }

    redirect('service-evaluation.php');
}

// FETCH RECENT EVALUATIONS
try {

    $st = db()->prepare(
        'SELECT office, rating, comment, created_at
         FROM service_evaluations
         WHERE user_id = ?
         ORDER BY id DESC
         LIMIT 10'
    );

    $st->execute([$uid]);

    $mine = $st->fetchAll();

} catch (PDOException $e) {

    $mine = [];
    $dbError = true;

}

$pageTitle = 'Service Evaluation';
$active = 'service-evaluation';

require __DIR__ . '/includes/header.php';
?>

<div class="two">

    <!-- EVALUATION FORM -->
    <section class="card">

        <h3>Rate a university office</h3>

        <form method="post" novalidate>

            <?= csrf_field() ?>

            <!-- OFFICE -->
            <label class="field">

                <span>Office</span>

                <select name="office" required>

                    <option value="">
                        Select an office
                    </option>

                    <?php foreach ($offices as $o): ?>

                        <option value="<?= e($o) ?>">
                            <?= e($o) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </label>

            <!-- RATING -->
            <fieldset class="field">

                <legend>Rating</legend>

                <div class="stars">

                    <?php for ($i = 5; $i >= 1; $i--): ?>

                        <input
                            type="radio"
                            id="r<?= $i ?>"
                            name="rating"
                            value="<?= $i ?>"
                        >

                        <label
                            for="r<?= $i ?>"
                            title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>"
                        >
                            ★
                        </label>

                    <?php endfor; ?>

                </div>

            </fieldset>

            <!-- COMMENTS -->
            <label class="field">

                <span>
                    Comments <em>(optional)</em>
                </span>

                <textarea
                    name="comment"
                    rows="4"
                    maxlength="1000"
                    placeholder="Tell us what went well or what we can improve"
                ></textarea>

            </label>

            <!-- SUBMIT -->
            <button
                class="btn primary"
                type="submit"
            >
                Submit evaluation
            </button>

        </form>

    </section>

    <!-- RECENT EVALUATIONS -->
    <section class="card">

        <h3>My recent evaluations</h3>

        <?php if ($dbError): ?>

            <p class="muted">
                Evaluation records are currently unavailable.
                Please check the database table.
            </p>

        <?php elseif (!$mine): ?>

            <p class="muted">
                You have not submitted any evaluation yet.
            </p>

        <?php else: ?>

            <ul class="plain">

                <?php foreach ($mine as $m): ?>

                    <li class="stack">

                        <span>

                            <b>
                                <?= e($m['office']) ?>
                            </b>

                            <span class="stars-static">

                                <?= str_repeat(
                                    '★',
                                    (int)$m['rating']
                                ) ?>

                                <?= str_repeat(
                                    '☆',
                                    5 - (int)$m['rating']
                                ) ?>

                            </span>

                        </span>

                        <?php if ($m['comment']): ?>

                            <p class="muted small">
                                <?= e($m['comment']) ?>
                            </p>

                        <?php endif; ?>

                        <small class="muted">

                            <?= e(
                                date(
                                    'M j, Y',
                                    strtotime($m['created_at'])
                                )
                            ) ?>

                        </small>

                    </li>

                <?php endforeach; ?>

            </ul>

        <?php endif; ?>

    </section>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
