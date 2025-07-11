<?php
session_start();

// Set header keamanan
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Cek apakah user sudah login
if (!isset($_SESSION['user']) || !$_SESSION['user']['logged_in']) {
    // Simpan URL yang diminta untuk redirect setelah login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 401 Unauthorized');
    header('Location: login.php');
    exit;
}

// Cek kesesuaian IP dan user agent untuk mencegah session hijacking
if ($_SESSION['user']['ip'] !== $_SERVER['REMOTE_ADDR'] || 
    $_SESSION['user']['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_unset();
    session_destroy();
    header('Location: login.php?security=1');
    exit;
}

// Generate CSRF token jika belum ada
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Invalid CSRF token'
    ]));
}
?>