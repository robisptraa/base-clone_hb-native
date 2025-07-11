<?php
// ==================== BAGIAN PHP ====================
session_start();
header("Content-Type: text/html; charset=UTF-8");

// Tambahkan header keamanan
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Proses login jika ada request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Content-Type: application/json");
    
    // Generate CSRF token jika belum ada
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    // Validasi CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid CSRF token'
        ]);
        exit;
    }

    $host = "localhost";
    $dbname = "hbn_production";
    $username = "root";
    $password = "";
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validasi input
        if (empty($email) || empty($password)) {
            throw new Exception("Email dan password harus diisi");
        }

        // Validasi format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format email tidak valid");
        }

        // Cari user dengan prepared statement
        $stmt = $conn->prepare("SELECT id, nama, email, password FROM pelanggan WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            // Delay untuk mencegah timing attack
            sleep(1);
            throw new Exception("Email atau password salah");
        }

        // Verifikasi password
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Email atau password salah");
        }

        // Regenerate session ID untuk mencegah session fixation
        session_regenerate_id(true);

        // Set session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['nama'],
            'email' => $user['email'],
            'logged_in' => true,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'last_activity' => time()
        ];

        // Update last login
        $conn->prepare("UPDATE pelanggan SET last_login = NOW() WHERE id = ?")
             ->execute([$user['id']]);

// Di bagian response JSON, modifikasi:
echo json_encode([
    'status' => 'success',
    'message' => 'Login berhasil',
    'redirect' => '/HBN-Project/index.php', // Gunakan absolute path
    'user' => [
        'id' => $user['id'],
        'name' => $user['nama'],
        'email' => $user['email']
    ]
]);
        exit;
        
    } catch (Exception $e) {
        error_log('Login error: ' . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
        exit;
    }
}


?>

<!-- ==================== BAGIAN HTML ==================== -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pelanggan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        html, body {
            height: 100%;
        }

        .spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <div class="shadow bg-white">
        <div class="h-20 mx-auto px-5 flex items-center justify-between">
            <a class="navbar-brand ps-3" href="index.php">
                <img src="Assets\Img\Gambar1.jpg" width="50" height="50" alt="">
            </a>

            <ul class="flex items-center gap-7">
                <li>
                    <a class="hover:text-cyan-500 transition-colors" href="login.php">Masuk</a>
                </li>
                <li>
                    <a class="hover:text-cyan-500 transition-colors" href="register.php">Daftar</a>
                </li>
            </ul>
        </div>
    </div>

    <main class="flex-grow flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-center mb-6">Masuk ke Akun Anda</h2>
                
                <form id="loginForm" method="post" class="space-y-4">
                    <!-- Tambahkan CSRF token -->
                    <input type="hidden" name="csrf_token" value="<?= isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '' ?>">
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" required
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition flex justify-center items-center gap-2">
                        <span id="submitText">Masuk</span>
                        <span id="spinner" class="spinner hidden"></span>
                    </button>
                </form>
                
                <p class="text-center mt-4 text-sm">
                    Belum punya akun? <a href="register.php" class="text-blue-600 hover:underline">Daftar disini</a>
                </p>
            </div>
        </div>
    </main>

    <!-- ==================== BAGIAN JAVASCRIPT ==================== -->
    <script>
   document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitButton = form.querySelector('button[type="submit"]');
    const submitText = document.getElementById('submitText');
    const spinner = document.getElementById('spinner');
    
    // Tampilkan loading
    submitText.textContent = "Memproses...";
    spinner.classList.remove('hidden');
    submitButton.disabled = true;
    
    try {
        const formData = new FormData(form);
        const response = await fetch('login.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) throw new Error('Network response was not ok');
        
        const result = await response.json();
        console.log("Debug:", result); // Untuk troubleshooting
        
        if (result.status !== 'success') throw new Error(result.message);
        
        // Simpan data user
        if (result.user) {
            sessionStorage.setItem('auth', JSON.stringify(result.user));
        }
        
        // Tampilkan SweetAlert kemudian redirect
        await Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: result.message,
            confirmButtonText: 'OK',
            timer: 2000,
            timerProgressBar: true,
            willClose: () => {
                // Redirect setelah SweetAlert tertutup
                window.location.href = result.redirect; 
            }
        });
        
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: error.message,
            confirmButtonText: 'Mengerti'
        });
    } finally {
        submitText.textContent = "Masuk";
        spinner.classList.add('hidden');
        submitButton.disabled = false;
    }
});
    </script>
</body>
</html>