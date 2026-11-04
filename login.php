<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/data.php';
if (!empty($_SESSION['uid'])) redirect('home.php');

$error = '';
$login = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $login = trim((string)($_POST['username'] ?? ''));
    $pass  = (string)($_POST['password'] ?? '');
    if ($login === '' || $pass === '') {
        $error = 'Enter your username and password.';
    } else {
        $st = db()->prepare('SELECT id, password_hash FROM users WHERE username = ? OR email = ? LIMIT 1');
        $st->execute([$login, $login]);
        $row = $st->fetch();
        if ($row && password_verify($pass, $row['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['uid'] = (int)$row['id'];
            redirect('home.php');
        }
        $error = 'Incorrect username or password.';
    }
}
$flash = take_flash();
$pageTitle = 'Login';
require __DIR__ . '/includes/auth_top.php';
?>
    <h2>Welcome!</h2>
    <p class="sub">Sign in to enlist, check your balance, and view your grades.</p>
    <?php if ($flash): ?><div class="alert <?= e($flash[0]) ?>"><?= e($flash[1]) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert err" role="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" autocomplete="on" novalidate>
      <?= csrf_field() ?>
      <label class="field"><span>Username or email</span>
        <input type="text" name="username" placeholder="Enter your username" value="<?= e($login) ?>" autocomplete="username" required autofocus>
      </label>
      <label class="field"><span>Password</span>
        <div class="pw">
          <input type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
          <button type="button" class="pw-toggle" aria-label="Show password"><?= icon('eye') ?></button>
        </div>
      </label>
      <button class="btn primary block" type="submit">Login</button>
    </form>
    <div class="auth-links">
      <a href="forgot-password.php">Forgot password?</a>
      <span>New here? <a href="signup.php">Create an account</a></span>
    </div>
    <p class="fine">By logging in, you agree with the Terms and Conditions.</p>
<?php require __DIR__ . '/includes/auth_bottom.php';
