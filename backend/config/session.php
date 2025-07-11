<?php
// Pastikan tidak ada output (spasi/HTML) sebelum tag pembuka PHP

// Konfigurasi Session
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Nonaktifkan jika di localhost
ini_set('session.cookie_samesite', 'Lax'); // Lebih fleksibel dari 'Strict'
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', 1800); // 30 menit

// Mulai Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug: Uncomment untuk cek session
echo "<pre>Session: "; print_r($_SESSION); echo "</pre>";
?>