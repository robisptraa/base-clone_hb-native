<?php
require_once __DIR__ . '/../config/database.php';

class AuthController {
    protected $conn;

    public function __construct() {
        // Start session in constructor
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Gunakan koneksi dari database.php
        $this->conn = require __DIR__ . '/../config/database.php';
    }

    public function register($data) {
        try {
            // Validasi input
            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                throw new Exception("Semua field wajib diisi");
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Format email tidak valid");
            }

            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            $verificationCode = bin2hex(random_bytes(16));

            $stmt = $this->conn->prepare("
                INSERT INTO users (name, email, password, phone, verification_code, created_at) 
                VALUES (:name, :email, :password, :phone, :code, NOW())
            ");

            $stmt->execute([
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':password' => $hashedPassword,
                ':phone' => $data['phone'] ?? null,
                ':code' => $verificationCode
            ]);

            // Kirim email verifikasi
            $this->sendVerificationEmail($data['email'], $verificationCode);

            return [
                'success' => true,
                'message' => 'Registrasi berhasil. Silakan cek email Anda.'
            ];

        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                return [
                    'success' => false,
                    'message' => 'Email sudah terdaftar',
                    'user' => null
                ];
            }

            error_log("Database Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan database',
                'user' => null
            ];

        } catch (Exception $e) {
            error_log("Registration Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'user' => null
            ];
        }
    }

    public function login($email, $password) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, name, email, password, role, avatar, verified 
                FROM users 
                WHERE email = :email
                LIMIT 1
            ");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    if (!$user['verified']) {
                        throw new Exception("Akun belum diverifikasi. Silakan cek email Anda.");
                    }

                    // Return user data for session creation
                    return [
                        'success' => true,
                        'message' => 'Login berhasil',
                        'user' => [
                            'id' => $user['id'],
                            'name' => $user['name'],
                            'email' => $user['email'],
                            'role' => $user['role'],
                            'avatar' => $user['avatar'] ?? 'default-avatar.jpg'
                        ]
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Email atau password salah',
                'user' => null
            ];

        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'user' => null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'user' => null
            ];
        }
    }

    private function sendVerificationEmail($email, $code) {
        $verificationLink = "http://".$_SERVER['HTTP_HOST']."/HBN-Project/verify.php?code=$code";
        $subject = "Verifikasi Email Anda";
        $message = "Silakan klik link berikut untuk verifikasi: $verificationLink";
        
        error_log("Email verifikasi untuk $email: $message");
        return true;
    }
}