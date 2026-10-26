<?php
require_once __DIR__ . '/includes/auth.php';
redirect(empty($_SESSION['id_user']) ? '/pages/login.php' : '/pages/dashboard.php');
