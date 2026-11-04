
<?php
require __DIR__ . "/includes/config.php";
require __DIR__ . "/includes/data.php";

$user = require_login();

// Temporary fix for missing enlistments table
try {
    $codes = enlisted_codes((int)$user['id']);
} catch (PDOException $e) {
    $codes = [];
}

$S = subjects();

$units = array_sum(array_map(
    fn($c) => $S[$c]['units'] ?? 0,
    $codes
));

$pageTitle = 'Home';
$active = 'home';

require __DIR__ . '/includes/header.php';
?>

<div class="home-grid">

  <div class="col">

    <section class="card profile">
      <div class="banner">
        <div class="rays small"></div>
      </div>

      <div class="avatar lg">
        <?= e(initials($user)) ?>
      </div>

      <h2><?= e(full_name($user)) ?></h2>

      <p class="muted">
        Student · <?= e($user['student_no']) ?>
      </p>

      <p class="chip">
        <?= e($user['course']) ?> · Year <?= (int)$user['year_level'] ?>
      </p>
    </section>

    <div class="alert warn">
      <strong>ID card</strong> is not yet available. Please wait until further notice.
    </div>

    <div class="alert warn">
      <strong>ID lace</strong> is not yet available. Please wait until further notice.
    </div>

    <section class="card">
      <h3>Announcements</h3>

      <ul class="news">
        <?php foreach (announcements() as [$d, $t, $b]): ?>
          <li>
            <time><?= e($d) ?></time>
            <strong><?= e($t) ?></strong>
            <p><?= e($b) ?></p>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>

  </div>

  <div class="col wide">

    <section class="stats">

      <a class="stat" href="enlistment.php">
        <span>Enlisted subjects</span>
        <strong><?= count($codes) ?></strong>
        <small>Manage enlistment</small>
      </a>

      <a class="stat" href="enlistment.php">
        <span>Total units</span>
        <strong><?= $units ?></strong>
        <small>Max <?= MAX_UNITS ?> units</small>
      </a>

      <a class="stat" href="tuition.php">
        <span>Term</span>
        <strong class="term">1st Sem</strong>
        <small><?= e(TERM_LABEL) ?></small>
      </a>

    </section>

    <section class="card">
      <h3>Quick links</h3>

      <div class="tiles">
        <?php foreach (menu_items() as $k => [$l, $ic, $f]): ?>
          <?php if ($k === 'home') continue; ?>

          <a class="tile" href="<?= e($f) ?>">
            <?= icon($ic) ?>
            <span><?= e($l) ?></span>
          </a>

        <?php endforeach; ?>
      </div>
    </section>

    <section class="card prose">

      <h3>Mission</h3>

      <p>
        University of Eastern Pangasinan exist to develop highly competent
        professionals and morally responsible individuals through innovations
        and industry-oriented instruction, strong relevant research, responsive
        extension programs, value-based curricular offerings and
        principle-centered culture that define the way of life in the university.
      </p>

      <h3>Vision</h3>

      <p>
        University of Eastern Pangasinan is the best holistic higher education
        institution known to cultivate excellent and virtuous individuals to
        become catalyst of progress and development for both the local and
        the global communities.
      </p>

      <h3>UEP overview</h3>

      <p>
        The University of Eastern Pangasinan is the first institution of higher
        education in the town of Binalonan and the second one in Pangasinan
        to adopt a public-private partnership wherein the government assumes
        substantial financial grants and private entities take on the operations
        of the university. The establishment of UEP began in 2005 following
        a dissertation paper authored by its founding president, Dr. Ramon V.
        Guico III, structured around the idea of creating a world-class
        institution that will provide the best practices in education to
        students regardless of their social, cultural and financial background.
      </p>

    </section>

  </div>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
