<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/koneksi.php';
if (session_status() === PHP_SESSION_NONE) session_start();

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rupiah($n) { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }
function redirect($p) { header('Location: ' . BASE_URL . $p); exit; }
function require_login() { if (empty($_SESSION['id_user'])) redirect('/pages/login.php'); }
function csrf() { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf() . '">'; }
function csrf_check() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(400); exit('Token tidak valid. Muat ulang halaman.'); }
}
function q($sql, $types = '', $params = []) {
    global $koneksi;
    $st = mysqli_prepare($koneksi, $sql);
    if ($types) mysqli_stmt_bind_param($st, $types, ...$params);
    mysqli_stmt_execute($st);
    return $st;
}
function rows($sql, $types = '', $params = []) {
    return mysqli_fetch_all(mysqli_stmt_get_result(q($sql, $types, $params)), MYSQLI_ASSOC);
}
function row($sql, $types = '', $params = []) { $r = rows($sql, $types, $params); return $r[0] ?? null; }
function menu_img($g) {
    return BASE_URL . '/images/menu/' . ($g && is_file(__DIR__ . '/../images/menu/' . $g) ? rawurlencode($g) : 'default.svg');
}
