<?php
// ==================== BAGIAN PHP ====================
header("Content-Type: text/html; charset=UTF-8");

// Proses registrasi jika ada request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Content-Type: application/json");
    
    // Koneksi database
    $host = "localhost";
    $dbname = "hbn_production";
    $username = "root";
    $password = "";
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Validasi input
        $errors = [];
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($name)) $errors[] = "Nama harus diisi";
        if (empty($email)) $errors[] = "Email harus diisi";
        if (empty($password)) $errors[] = "Password harus diisi";
        if ($password !== $confirm_password) $errors[] = "Password tidak cocok";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid";
        if (strlen($password) < 8) $errors[] = "Password minimal 8 karakter";

        if (!empty($errors)) {
            throw new Exception(implode(", ", $errors));
        }

        // Cek email sudah terdaftar
        $stmt = $conn->prepare("SELECT id FROM pelanggan WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("Email sudah terdaftar");
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Simpan ke database
        $stmt = $conn->prepare("INSERT INTO pelanggan (nama, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $hashedPassword]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Registrasi berhasil!',
            'redirect' => 'login.php'
        ]);
        exit;
        
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: '.$e->getMessage()]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
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
    <title>Registrasi Pelanggan</title>
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
            <h2 class="text-2xl font-bold text-center mb-6">Daftar Akun Baru</h2>
            
            <form id="registerForm" method="post" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" required
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">No. Telepon</label>
                    <input type="tel" name="phone"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Konfirmasi Password</label>
                    <input type="password" name="confirm_password" required minlength="8"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition flex justify-center items-center gap-2">
                    <span id="submitText">Daftar</span>
                    <span id="spinner" class="spinner hidden"></span>
                </button>
            </form>
            
            <p class="text-center mt-4 text-sm">
                Sudah punya akun? <a href="login.php" class="text-blue-600 hover:underline">Masuk disini</a>
            </p>
        </div>
    </div>

    <!-- ==================== BAGIAN JAVASCRIPT ==================== -->
    <script>
    document.getElementById('registerForm').addEventListener('submit', async function(e) {
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
            // Kirim data form
            const formData = new FormData(form);
            const response = await fetch('register.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.status !== 'success') {
                throw new Error(result.message);
            }
            
            // Tampilkan pesan sukses
            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                confirmButtonText: 'OK'
            });
            
            // Redirect jika ada
            if (result.redirect) {
                window.location.href = result.redirect;
            }
            
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: error.message,
                confirmButtonText: 'Mengerti'
            });
        } finally {
            // Reset form
            submitText.textContent = "Daftar";
            spinner.classList.add('hidden');
            submitButton.disabled = false;
        }
    });
    </script>
</body>
</html>