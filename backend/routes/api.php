<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

// Endpoint untuk cek status order
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'check_order_status') {
    session_start();
    
    if (!isset($_SESSION['user'])) {
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $stmt = $conn->prepare("
        SELECT COUNT(*) as changes 
        FROM orders 
        WHERE user_id = ? AND status != 'pending' AND viewed = 0
    ");
    $stmt->execute([$_SESSION['user']['id']]);
    $result = $stmt->fetch();
    
    echo json_encode([
        'updated' => $result['changes'] > 0,
        'last_checked' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// 1. Load konfigurasi database HARUS DULUAN
require_once __DIR__ . '/../config/database.php';

// 2. Load Controller
require_once __DIR__ . '/../controllers/DashboardController.php';

// 3. Set header response JSON
header("Content-Type: application/json");

// 4. Inisialisasi Controller dengan koneksi database
$dashboardController = new DashboardController($pdo);

// 5. Handle Request API
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Endpoint: GET /api/dashboard/summary
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $requestPath === '/api/dashboard/summary') {
    $response = $dashboardController->getSummary();
    echo json_encode($response);
    exit;
}

// Jika endpoint tidak ditemukan
http_response_code(404);
echo json_encode([
    'success' => false,
    'message' => '404 Endpoint not found'
]);


?>