<?php
class DashboardController {
    private $pdo;

    // Constructor (Menerima koneksi database)
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Method untuk mengambil ringkasan data
    public function getSummary() {
        try {
            // 1. Hitung total pelanggan
            $stmt = $this->pdo->query("SELECT COUNT(*) AS total_customers FROM users");
            $customers = $stmt->fetch()['total_customers'];

            // 2. Hitung total transaksi
            $stmt = $this->pdo->query("SELECT COUNT(*) AS total_orders FROM orders");
            $orders = $stmt->fetch()['total_orders'];

            // 3. Hitung total pemasangan (installations)
            $stmt = $this->pdo->query("SELECT COUNT(*) AS total_installations FROM installations WHERE status = 'completed'");
            $installations = $stmt->fetch()['total_installations'];

            // 4. Hitung total keluhan (complaints)
            $stmt = $this->pdo->query("SELECT COUNT(*) AS total_complaints FROM complaints");
            $complaints = $stmt->fetch()['total_complaints'];

            // 5. Hitung total pendapatan (revenue)
            $stmt = $this->pdo->query("SELECT SUM(amount) AS total_revenue FROM payments WHERE status = 'success'");
            $revenue = $stmt->fetch()['total_revenue'] ?? 0;

            // Return data dalam format JSON
            return [
                'success' => true,
                'data' => [
                    'customers' => (int)$customers,
                    'orders' => (int)$orders,
                    'installations' => (int)$installations,
                    'complaints' => (int)$complaints,
                    'revenue' => (float)$revenue
                ]
            ];

        } catch (PDOException $e) {
            // Jika terjadi error database
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
}
?>