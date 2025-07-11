<?php
// Memeriksa status session sebelum memulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';

// Header keamanan
header("X-Frame-Options: DENY"); // Mencegah clickjacking
header("X-Content-Type-Options: nosniff"); // Mencegah sniffing konten
header("X-XSS-Protection: 1; mode=block"); // Proteksi XSS
header('Content-Type: application/json'); // Set tipe konten JSON

$response = [
    'loggedIn' => false, // Status login default
    'avatar' => 'default-avatar.jpg' // Avatar default
];

// Cek apakah user sudah login
if (isset($_SESSION['user_id'])) {
    $response['loggedIn'] = true;
    $response['avatar'] = $_SESSION['user_avatar'] ?? 'default-avatar.jpg';
    
    // Validasi keamanan session - cek IP dan browser
    if (!isset($_SESSION['ip']) || $_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'] || 
        !isset($_SESSION['user_agent']) || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        // Hapus session jika tidak valid
        session_unset();
        session_destroy();
        header('Location: ../../login.php?error=session_tidak_valid');
        exit;
    }
} else {
    // Jika belum login, arahkan ke halaman login
    header('Location: ../../login.php');
    exit;
}

// Jika ini request AJAX, kembalikan response JSON
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    echo json_encode($response);
    exit;
}

// Untuk request normal, lanjutkan ke halaman yang diminta
?>