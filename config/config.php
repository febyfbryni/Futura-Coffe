<?php
// BASE_URL otomatis: dihitung dari URL halaman yang sedang dibuka, tanpa bergantung pada DOCUMENT_ROOT.
// Contoh: /futura-coffee (http://localhost/futura-coffee/) atau kosong (http://futura-coffee.test/).
if (!defined('BASE_URL')) {
    $root = str_replace('\\', '/', realpath(__DIR__ . '/..'));
    $file = str_replace('\\', '/', realpath($_SERVER['SCRIPT_FILENAME'] ?? '') ?: '');
    $name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $base = '';
    if ($file !== '' && strncasecmp($file, $root, strlen($root)) === 0) {
        $rel = substr($file, strlen($root));            // contoh: /pages/login.php
        if ($rel !== '' && strcasecmp(substr($name, -strlen($rel)), $rel) === 0) {
            $base = substr($name, 0, strlen($name) - strlen($rel));
        }
    }
    $seg = array_map('rawurlencode', array_filter(explode('/', $base), 'strlen'));
    define('BASE_URL', $seg ? '/' . implode('/', $seg) : '');
}
function asset($p) {
    $f = __DIR__ . '/../' . $p;
    return BASE_URL . '/' . $p . (is_file($f) ? '?v=' . filemtime($f) : '');
}
