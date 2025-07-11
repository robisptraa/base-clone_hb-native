<?php
// backend/services/AuthService.php
class AuthService {
    private $conn;
    private $sessionKey = 'auth_user';

    public function __construct() {
        require_once __DIR__.'/../config/database.php';
        $this->conn = $conn;
        
        if (session_status() === PHP_SESSION_NONE) {
            require_once __DIR__.'/../config/session.php';
        }
    }

    public function checkAuth() {
        // Validate session exists
        if (empty($_SESSION[$this->sessionKey])) {
            $this->redirectToLogin();
        }

        // Validate session security
        $this->validateSessionSecurity();

        return $_SESSION[$this->sessionKey];
    }

    private function validateSessionSecurity() {
        $session = $_SESSION[$this->sessionKey];
        
        // IP and User Agent validation
        if ($session['ip'] !== $_SERVER['REMOTE_ADDR'] || 
            $session['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            $this->destroySession();
            $this->redirectToLogin('session_invalid');
        }

        // Inactivity timeout (30 minutes)
        if (time() - $session['last_activity'] > 1800) {
            $this->destroySession();
            $this->redirectToLogin('session_expired');
        }

        // Update activity timestamp
        $_SESSION[$this->sessionKey]['last_activity'] = time();
    }

    public function refreshUserData() {
        $userId = $_SESSION[$this->sessionKey]['id'] ?? null;
        
        try {
            $stmt = $this->conn->prepare("
                SELECT id, nama, email, phone, avatar, is_active, last_login 
                FROM pelanggan 
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
            
            if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $_SESSION[$this->sessionKey] = array_merge(
                    $_SESSION[$this->sessionKey],
                    $this->sanitizeUserData($user)
                );
                return true;
            }
            
            $this->destroySession();
            return false;
            
        } catch (PDOException $e) {
            error_log("AuthService refresh error: ".$e->getMessage());
            return false;
        }
    }

    private function sanitizeUserData($user) {
        return [
            'id' => $user['id'],
            'nama' => htmlspecialchars($user['nama']),
            'email' => filter_var($user['email'], FILTER_SANITIZE_EMAIL),
            'phone' => preg_replace('/[^0-9]/', '', $user['phone'] ?? ''),
            'avatar' => $user['avatar'] ?? 'default-avatar.jpg',
            'is_active' => (bool)$user['is_active'],
            'last_login' => $user['last_login'] ?? null
        ];
    }

    private function redirectToLogin($reason = null) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        if ($reason) {
            header("Location: ../../login.php?error=$reason");
        } else {
            header("Location: ../../login.php");
        }
        exit;
    }

    private function destroySession() {
        session_unset();
        session_destroy();
    }
}
?>