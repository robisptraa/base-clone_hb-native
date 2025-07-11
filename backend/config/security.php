<?php
// Pastikan session sudah aktif
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hanya set headers jika belum ada output
if (!headers_sent()) {
    // Nonaktifkan HSTS di localhost
    if ($_SERVER['HTTP_HOST'] !== 'localhost' && !str_contains($_SERVER['HTTP_HOST'], '127.0.0.1')) {
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
    }
    
    header("Content-Security-Policy: default-src 'self' 'unsafe-inline'; img-src 'self' data:"); // Lebih fleksibel
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: SAMEORIGIN"); // Izinkan iframe dari domain sama
    header("X-XSS-Protection: 1; mode=block");
    header_remove('X-Powered-By');
}

// CSRF Token (hanya generate jika tidak ada)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>