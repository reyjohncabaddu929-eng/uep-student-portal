
<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/data.php';

if (!empty($_SESSION['uid'])) redirect('home.php');

$errors = [];

$v = [
    'first_name' => '',
    'middle_name' => '',
    'last_name' => '',
    'username' => '',
    'email' => '',
    'course' => '',
    'year_level' => '1'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    foreach ($v as $k => $_) {
        $v[$k] = trim((string)($_POST[$k] ?? ''));
    }

    $pass  = (string)($_POST['password'] ?? '');
    $pass2 = (string)($_POST['password2'] ?? '');

    if ($v['first_name'] === '' || mb_strlen($v['first_name']) > 60) {
        $errors[] = 'Enter your first name.';
    }

    if ($v['last_name'] === '' || mb_strlen($v['last_name']) > 60) {
        $errors[] = 'Enter your last name.';
    }

    if (mb_strlen($v['middle_name']) > 60) {
        $errors[] = 'Middle name is too long.';
    }

    if (!preg_match('/^[A-Za-z0-9_.]{4,30}$/', $v['username'])) {
        $errors[] = 'Username must be 4–30 characters: letters, numbers, dot or underscore.';
    }

    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }

    if (!in_array($v['course'], courses(), true)) {
        $errors[] = 'Choose your course.';
    }

    if (!in_array($v['year_level'], ['1', '2', '3', '4'], true)) {
        $errors[] = 'Choose your year level.';
    }

    if (strlen($pass) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if ($pass !== $pass2) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        try {
            $pdo = db();

            $st = $pdo->prepare(
                'INSERT INTO users
                (first_name, middle_name, last_name, username, email, course, year_level, password_hash)
                VALUES (?,?,?,?,?,?,?,?)'
            );

            $st->execute([
                $v['first_name'],
                $v['middle_name'] ?: null,
                $v['last_name'],
                $v['username'],
                strtolower($v['email']),
                $v['course'],
                (int)$v['year_level'],
                password_hash($pass, PASSWORD_DEFAULT),
            ]);

            $id = (int)$pdo->lastInsertId();

            $pdo->prepare('UPDATE users SET student_no = ? WHERE id = ?')
                ->execute([
                    sprintf('UEP-%s-%05d', date('Y'), $id),
                    $id
                ]);

            session_regenerate_id(true);

            $_SESSION['uid'] = $id;

            flash('ok', 'Account created. Welcome to the UEP Student Portal!');

            redirect('home.php');

        } catch (PDOException $ex) {
            $errors[] = $ex->getCode() === '23000'
                ? 'That username or email is already registered.'
                : 'Could not create your account. Please try again.';
        }
    }
}

$pageTitle = 'Sign up';

require __DIR__ . '/includes/auth_top.php';
?>

<h2>Create your account</h2>

<p class="sub">
    Anyone can sign up. Your name will appear on your dashboard.
</p>

<?php if ($errors): ?>
    <div class="alert err" role="alert">
        <?php foreach ($errors as $er): ?>
            <div><?= e($er) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" novalidate autocomplete="off">

    <?= csrf_field() ?>

    <div class="grid3">

        <label class="field">
            <span>First name</span>
            <input
                type="text"
                name="first_name"
                value="<?= e($v['first_name']) ?>"
                placeholder="First name"
                autocomplete="off"
                required
            >
        </label>

        <label class="field">
            <span>Middle name <em>(optional)</em></span>
            <input
                type="text"
                name="middle_name"
                value="<?= e($v['middle_name']) ?>"
                placeholder="Middle name"
                autocomplete="off"
            >
        </label>

        <label class="field">
            <span>Last name</span>
            <input
                type="text"
                name="last_name"
                value="<?= e($v['last_name']) ?>"
                placeholder="Last name"
                autocomplete="off"
                required
            >
        </label>

    </div>

    <div class="grid2">

        <label class="field">
            <span>Username</span>
            <input
                type="text"
                name="username"
                value="<?= e($v['username']) ?>"
                placeholder="Choose a username"
                autocomplete="off"
                required
            >
        </label>

        <label class="field">
            <span>Email</span>
            <input
                type="email"
                name="email"
                value="<?= e($v['email']) ?>"
                placeholder="you@example.com"
                autocomplete="off"
                required
            >
        </label>

    </div>

    <div class="grid2 course-row">

        <label class="field">
            <span>Course</span>

            <select name="course" autocomplete="off" required>
                <option value="">Select your course</option>

                <?php foreach (courses() as $c): ?>
                    <option<?= $v['course'] === $c ? ' selected' : '' ?>>
                        <?= e($c) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </label>

        <label class="field">
            <span>Year level</span>

            <select name="year_level" autocomplete="off">

                <?php foreach (
                    ['1' => '1st year', '2' => '2nd year', '3' => '3rd year', '4' => '4th year']
                    as $k => $l
                ): ?>

                    <option
                        value="<?= $k ?>"
                        <?= $v['year_level'] === (string)$k ? 'selected' : '' ?>
                    >
                        <?= $l ?>
                    </option>

                <?php endforeach; ?>

            </select>
        </label>

    </div>

    <div class="grid2">

        <label class="field">
            <span>Password</span>

            <div class="pw">

                <input
                    type="password"
                    name="password"
                    placeholder="At least 8 characters"
                    autocomplete="off"
                    required
                >

                <button
                    type="button"
                    class="pw-toggle"
                    aria-label="Show password"
                >
                    <?= icon('eye') ?>
                </button>

            </div>
        </label>

        <label class="field">
            <span>Confirm password</span>

            <input
                type="password"
                name="password2"
                placeholder="Repeat password"
                autocomplete="off"
                required
            >
        </label>

    </div>

    <button class="btn primary block" type="submit">
        Sign up
    </button>

</form>

<div class="auth-links">
    <span>
        Already have an account?
        <a href="login.php">Login</a>
    </span>
</div>

<?php require __DIR__ . '/includes/auth_bottom.php'; ?>
