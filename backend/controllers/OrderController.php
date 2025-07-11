<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Di bagian penyimpanan data:
$stmt = $conn->prepare("
    INSERT INTO users (
        name, 
        email, 
        phone, 
        package, 
        project_description,
        payment_status,
        total_payment,
        role,
        order_status
    ) VALUES (?, ?, ?, ?, ?, 'pending', ?, 'client', 'waiting_payment')
");

// Handle bukti transfer
if (!empty($_FILES['payment_proof'])) {
    $proofFile = $_FILES['payment_proof'];
    $proofName = 'payment_' . uniqid() . '.' . pathinfo($proofFile['name'], PATHINFO_EXTENSION);
    $uploadDir = __DIR__ . '/../../Assets/payments/';
    
    if (move_uploaded_file($proofFile['tmp_name'], $uploadDir . $proofName)) {
        // Simpan nama file ke database
        $conn->query("UPDATE users SET payment_proof = '$proofName' WHERE id = $orderId");
    }
}
}
?>