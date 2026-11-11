<?php
require_once __DIR__ . '/auth.php';
require_login();
$nav = [
  'dashboard' => ['Dashboard', '/pages/dashboard.php', '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>'],
  'menu' => ['Data Menu', '/pages/data-menu.php', '<path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/>'],
  'transaksi' => ['Transaksi', '/pages/transaksi.php', '<rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/>'],
  'riwayat' => ['Riwayat Transaksi', '/pages/riwayat-transaksi.php', '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l3 2"/>'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title ?? 'Futura') ?> - Futura Coffee</title>
<link rel="icon" type="image/png" href="../images/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="<?= asset("css/style.css") ?>">
</head>
<body>
<div class="app">
<aside class="sidebar">
  <div class="brand"><img src="<?= BASE_URL ?>/images/logo.png" alt=""><div><b>Futura</b><small>Tea &amp; Coffee Space</small></div></div>
  <nav>
  <?php foreach ($nav as $k => $n): ?>
    <a href="<?= BASE_URL . $n[1] ?>" class="<?= ($active ?? '') === $k ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $n[2] ?></svg><?= $n[0] ?></a>
  <?php endforeach; ?>
  </nav>
  <button type="button" class="logout" data-open="m-logout">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg>Logout</button>
</aside>
<main class="main">
