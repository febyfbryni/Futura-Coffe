<?php
require_once __DIR__ . '/../includes/auth.php';
if (!empty($_SESSION['id_user'])) redirect('/pages/dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $u = trim($_POST['username'] ?? '');
    $user = $u !== '' ? row("SELECT * FROM tb_user WHERE username = ?", 's', [$u]) : null;
    if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['id_user'] = (int)$user['id_user'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        redirect('/pages/dashboard.php');
    }
    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Futura Coffee</title>
<link rel="icon" type="image/png" href="../images/favicon.png">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="<?= asset("css/style.css") ?>">
</head>
<body class="login-page">
<div class="login">
  <div class="login-art" style="background-image:url('<?= BASE_URL ?>/images/login-bg.jpg')"></div>
  <div class="login-form">
    <form method="post" autocomplete="off">
      <h1>Selamat Datang</h1>
      <p class="muted">Silakan login untuk melanjutkan</p>
      <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
      <?= csrf_field() ?>
      <input class="field" name="username" placeholder="Username" value="<?= e($_POST['username'] ?? '') ?>" required autofocus>
      <div class="pw"><input class="field" type="password" name="password" id="pw" placeholder="Password" required>
        <button type="button" id="togglePw" aria-label="Tampilkan password">&#128065;</button></div>
      <button class="btn block">Login</button>
      <p class="muted small center">Sistem Point of Sale &amp; Manajemen Kedai Kopi Futura</p>
    </form>
  </div>
</div>
<script src="<?= asset("js/app.js") ?>"></script>
</body>
</html>
