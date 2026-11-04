<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/data.php';
$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    // Demo only: no email is sent. Connect a mailer (e.g. PHPMailer) here for real resets.
    $sent = true;
}
$pageTitle = 'Forgot password';
require __DIR__ . '/includes/auth_top.php';
?>
    <h2>Forgot password?</h2>
    <p class="sub">Enter the email you used to sign up and we will send you reset instructions.</p>
    <?php if ($sent): ?>
      <div class="alert ok">If that email is registered, reset instructions are on the way.</div>
    <?php endif; ?>
    <form method="post" novalidate>
      <?= csrf_field() ?>
      <label class="field"><span>Email</span>
        <input type="email" name="email" placeholder="you@example.com" required>
      </label>
      <button class="btn primary block" type="submit">Send reset link</button>
    </form>
    <div class="auth-links"><a href="login.php">Back to login</a></div>
<?php require __DIR__ . '/includes/auth_bottom.php';
