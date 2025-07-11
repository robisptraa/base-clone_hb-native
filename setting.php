<?php
declare(strict_types=1);

// ==================== KONFIGURASI AWAL ====================
// Aktifkan output buffering
ob_start();

// Set error handling
set_error_handler(function(int $errno, string $errstr, string $errfile, int $errline) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});

// Konfigurasi error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1'); // Hanya untuk development
ini_set('log_errors', '1');
ini_set('error_log', __DIR__.'/../logs/php_errors.log');

// ==================== FUNGSI UTILITAS ====================
/**
 * Mendapatkan path gambar dengan fallback yang aman
 */
function getSafeImagePath(string $relativePath, string $default = 'default-avatar.jpg'): string {
    $imageDir = __DIR__.'/Assets/Img/';
    
    if (!is_dir($imageDir)) {
        throw new RuntimeException("Direktori gambar tidak valid: {$imageDir}");
    }

    $targetPath = __DIR__.'/'.$relativePath;
    $defaultPath = $imageDir.$default;

    if (file_exists($targetPath) && is_file($targetPath)) {
        return $relativePath;
    }

    if (file_exists($defaultPath)) {
        return 'Assets/Img/'.$default;
    }

    // Fallback ke placeholder SVG
    return 'data:image/svg+xml;base64,'.base64_encode(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="#cccccc"><rect width="100" height="100"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial" font-size="10">Image not found</text></svg>'
    );
}

/**
 * Handle upload avatar dengan validasi ketat
 */
function handleAvatarUpload(?array $file, string $currentAvatar): string {
    if (empty($file['tmp_name'])) {
        return $currentAvatar;
    }

    $uploadDir = __DIR__.'/Assets/Img/avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
        file_put_contents($uploadDir.'index.html', '');
    }

    // Validasi tipe file
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif'
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowedTypes[$mimeType])) {
        throw new InvalidArgumentException('Hanya file JPG, PNG, atau GIF yang diperbolehkan');
    }

    // Generate nama file unik
    $extension = $allowedTypes[$mimeType];
    $newFilename = 'avatar_'.bin2hex(random_bytes(8)).'.'.$extension;
    $targetPath = $uploadDir.$newFilename;

    // Proses gambar
    $image = match($mimeType) {
        'image/jpeg' => imagecreatefromjpeg($file['tmp_name']),
        'image/png' => imagecreatefrompng($file['tmp_name']),
        'image/gif' => imagecreatefromgif($file['tmp_name']),
        default => throw new RuntimeException('Tipe gambar tidak didukung')
    };

    // Resize gambar jika terlalu besar
    $maxSize = 500;
    $origWidth = imagesx($image);
    $origHeight = imagesy($image);

    if ($origWidth > $maxSize || $origHeight > $maxSize) {
        $ratio = $origWidth / $origHeight;
        $newWidth = $ratio > 1 ? $maxSize : $maxSize * $ratio;
        $newHeight = $ratio > 1 ? $maxSize / $ratio : $maxSize;

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        // Simpan gambar yang diresize
        switch($mimeType) {
            case 'image/jpeg':
                imagejpeg($resized, $targetPath, 90);
                break;
            case 'image/png':
                imagepng($resized, $targetPath, 9);
                break;
            case 'image/gif':
                imagegif($resized, $targetPath);
                break;
        }

        imagedestroy($resized);
    } else {
        // Simpan gambar asli
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException('Gagal menyimpan file upload');
        }
    }

    imagedestroy($image);

    // Hapus avatar lama jika bukan default
    if ($currentAvatar !== 'default-avatar.jpg' && file_exists($uploadDir.$currentAvatar)) {
        unlink($uploadDir.$currentAvatar);
    }

    return $newFilename;
}

/**
 * Dapatkan data pesanan dari database
 */
function getOrders(PDO $conn, int $userId): array {
    try {
        $query = "SELECT o.*, p.name as package_name 
                 FROM orders o
                 JOIN packages p ON o.package_id = p.id
                 WHERE o.user_id = ?
                 ORDER BY o.created_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error mendapatkan pesanan: ".$e->getMessage());
        return [];
    }
}

// ==================== LOGIKA APLIKASI ====================
try {
    // Load konfigurasi penting
    $configFiles = [
        'session' => __DIR__.'/backend/config/session.php',
        'database' => __DIR__.'/backend/config/database.php',
        'security' => __DIR__.'/backend/config/security.php'
    ];

    foreach ($configFiles as $name => $path) {
        if (!file_exists($path)) {
            throw new RuntimeException("File konfigurasi {$name} tidak ditemukan");
        }
        require_once $path;
    }

    // Validasi session
    if (empty($_SESSION['user']['id'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'] ?? '/';
        header('Location: login.php');
        exit;
    }

    $userId = (int)$_SESSION['user']['id'];

    // Dapatkan data user
    $userQuery = "SELECT id, nama, email, phone, avatar, last_login FROM pelanggan WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($userQuery);
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_unset();
        session_destroy();
        header('Location: login.php?error=user_not_found');
        exit;
    }

    // Update data session
    $_SESSION['user'] = [
        'id' => $user['id'],
        'nama' => htmlspecialchars($user['nama'], ENT_QUOTES, 'UTF-8'),
        'email' => filter_var($user['email'], FILTER_SANITIZE_EMAIL),
        'phone' => preg_replace('/[^0-9]/', '', $user['phone'] ?? ''),
        'avatar' => $user['avatar'] ?? 'default-avatar.jpg',
        'last_login' => $user['last_login'] ?? null
    ];

    // Handle form submit
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        // Validasi CSRF token
        if (empty($_POST['csrf_token'])) {
            throw new RuntimeException('CSRF token tidak ditemukan');
        }
        
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new RuntimeException('CSRF token tidak valid');
        }

        // Proses dan validasi input
        $nama = trim(htmlspecialchars($_POST['nama'] ?? '', ENT_QUOTES, 'UTF-8'));
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $phone = preg_replace('/[^0-9]/', '', $_POST['phone'] ?? '');

        if (empty($nama) || strlen($nama) > 100) {
            throw new InvalidArgumentException('Nama harus diisi (maks 100 karakter)');
        }

        if (!$email) {
            throw new InvalidArgumentException('Email tidak valid');
        }

        // Proses upload avatar jika ada
        $avatar = handleAvatarUpload($_FILES['avatar'] ?? null, $_SESSION['user']['avatar']);

        // Update database dalam transaction
        $conn->beginTransaction();
        try {
            $updateQuery = "UPDATE pelanggan SET 
                            nama = :nama, 
                            email = :email, 
                            phone = :phone, 
                            avatar = :avatar, 
                            updated_at = NOW() 
                            WHERE id = :id";
            
            $stmt = $conn->prepare($updateQuery);
            $stmt->execute([
                ':nama' => $nama,
                ':email' => $email,
                ':phone' => !empty($phone) ? $phone : null,
                ':avatar' => $avatar,
                ':id' => $userId
            ]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('Tidak ada data yang diupdate');
            }

            $conn->commit();

            // Update session
            $_SESSION['user']['nama'] = $nama;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['phone'] = $phone;
            $_SESSION['user']['avatar'] = $avatar;

            $_SESSION['success'] = "Profil berhasil diperbarui";
            header('Location: setting.php');
            exit;

        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    // Dapatkan data untuk tampilan
    $orders = getOrders($conn, $userId);
    $avatarPath = getSafeImagePath('Assets/Img/'.$_SESSION['user']['avatar']);
    $emptyOrderPath = getSafeImagePath('Assets/Img/empty-order.png');
    $emptyFormPath = getSafeImagePath('Assets/Img/empty-form-icon.jpg');
    $logoPath = getSafeImagePath('Assets/Img/Gambar1.jpg');

    // Bersihkan buffer sebelum output HTML
    ob_end_clean();

} catch (Throwable $e) {
    // Handle error
    ob_end_clean();
    http_response_code(500);
    
    // Log error
    error_log(sprintf(
        "[%s] Error in %s:%d - %s\nStack trace:\n%s",
        date('Y-m-d H:i:s'),
        $e->getFile(),
        $e->getLine(),
        $e->getMessage(),
        $e->getTraceAsString()
    ));
    
    // Tampilkan halaman error sederhana
    echo '<!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - Sistem</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100">
        <div class="min-h-screen flex items-center justify-center">
            <div class="bg-white p-8 rounded-lg shadow-md max-w-2xl w-full">
                <h1 class="text-2xl font-bold text-red-600 mb-4">Terjadi Kesalahan</h1>
                <p class="mb-4">Maaf, terjadi kesalahan saat memproses permintaan Anda.</p>';
    
    if (ini_get('display_errors')) {
        echo '<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                <h2 class="font-bold text-red-800">Detail Error:</h2>
                <p class="text-red-700">'.htmlspecialchars($e->getMessage()).'</p>
                <p class="text-sm text-red-600">File: '.htmlspecialchars($e->getFile()).' (Line: '.$e->getLine().')</p>
              </div>';
    }
    
    echo '<a href="index.php" class="text-blue-600 hover:text-blue-800">Kembali ke halaman utama</a>
            </div>
        </div>
    </body>
    </html>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Profile - <?= htmlspecialchars($_SESSION['user']['nama'], ENT_QUOTES, 'UTF-8') ?></title>
    
    <!-- CSS External -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- CSS Local -->
    <link rel="stylesheet" href="<?= htmlspecialchars(getSafeImagePath('Assets/css/style.css')) ?>"/>
    
    <style>
        .status-badge { background-color: #f3f4f6; }
        .Complete .status-badge { background-color: #d1fae5; }
        .Canceled .status-badge { background-color: #fee2e2; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <!-- Navbar -->
    <nav class="shadow bg-white">
        <div class="h-20 mx-auto px-5 flex items-center justify-between">
            <a class="navbar-brand ps-3" href="index.php">
                <img src="<?= htmlspecialchars($logoPath) ?>" width="50" height="50" alt="Logo" loading="lazy">
            </a>

            <ul class="flex items-center gap-5">
                <li><a class="hover:text-cyan-500 transition-colors" href="index.php#portfolio">Portofolio</a></li>
                <li><a class="hover:text-cyan-500 transition-colors" href="index.php#about">Tentang Kami</a></li>
                <li><a class="hover:text-cyan-500 transition-colors" href="index.php#contact">Kontak</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?= htmlspecialchars($avatarPath) ?>" class="rounded-circle" width="30" height="30" alt="Profile" loading="lazy">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="setting.php"><i class="fas fa-user-cog me-2"></i> Profile & Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="backend/logout.php" method="post">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto mt-8 flex flex-col md:flex-row gap-6 px-4 md:px-5 p-8">
        <!-- Profile Sidebar -->
        <aside class="bg-white w-full md:w-72 p-8 flex flex-col items-center">
            <img alt="User profile" class="mb-4 rounded-full w-20 h-20 object-cover" 
                 src="<?= htmlspecialchars($avatarPath) ?>" loading="lazy">
            <p class="text-sm font-semibold text-gray-800 mb-1">
                <?= htmlspecialchars($_SESSION['user']['nama'], ENT_QUOTES, 'UTF-8') ?>
            </p>
            <p class="text-xs text-gray-500">
                <?= htmlspecialchars($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        </aside>

        <!-- Content Area -->
        <section class="bg-white flex-1 p-6">
            <!-- Tabs Navigation -->
            <nav class="flex space-x-6 border-b border-gray-200 mb-4">
                <button class="tab-btn px-1 py-2 text-sm font-medium text-sky-500 border-b-2 border-sky-500" data-tab="pesanan">Pesanan</button>
                <button class="tab-btn px-1 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="form">Form</button>
                <button class="tab-btn px-1 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="pengaturan">Pengaturan</button>
            </nav>

            <!-- Notification Messages -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger mb-4 p-3 bg-red-100 text-red-700 rounded">
                    <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success mb-4 p-3 bg-green-100 text-green-700 rounded">
                    <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <!-- Tab Contents -->
            <div class="tab-contents">
                <!-- Pesanan Tab -->
                <div class="tab-content active" id="pesanan-content">
                    <?php if (!empty($orders)): ?>
                        <div class="space-y-4">
                            <?php foreach ($orders as $order): ?>
                                <article class="border rounded-lg p-4 <?= htmlspecialchars(strtolower($order['status']), ENT_QUOTES, 'UTF-8') ?>">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="font-medium"><?= htmlspecialchars($order['package_name'], ENT_QUOTES, 'UTF-8') ?></h4>
                                        <span class="status-badge px-3 py-1 rounded-full text-xs">
                                            <?php switch($order['status']) {
                                                case 'Complete': 
                                                    echo '<i class="fas fa-check-circle text-green-500"></i> Approved';
                                                    break;
                                                case 'Canceled': 
                                                    echo '<i class="fas fa-times-circle text-red-500"></i> Rejected';
                                                    break;
                                                default: 
                                                    echo '<i class="fas fa-spinner fa-spin text-yellow-500"></i> Pending';
                                            } ?>
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500">Order ID</p>
                                            <p>#<?= htmlspecialchars($order['id'], ENT_QUOTES, 'UTF-8') ?></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Tanggal</p>
                                            <p><?= date('d M Y', strtotime($order['created_at'])) ?></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Status</p>
                                            <p><?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8') ?></p>
                                        </div>
                                    </div>
                                    <?php if ($order['status'] == 'Complete'): ?>
                                        <div class="mt-3 p-3 bg-green-50 rounded-lg">
                                            <p class="text-green-700 text-sm"><i class="fas fa-check"></i> Paket telah disetujui dan sedang diproses</p>
                                        </div>
                                    <?php elseif ($order['status'] == 'Canceled'): ?>
                                        <div class="mt-3 p-3 bg-red-50 rounded-lg">
                                            <p class="text-red-700 text-sm"><i class="fas fa-info-circle"></i> Pesanan ditolak. Silakan hubungi admin.</p>
                                        </div>
                                    <?php endif; ?>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center mt-12">
                            <img src="<?= htmlspecialchars($emptyOrderPath) ?>" alt="Empty orders" class="mb-4" width="150" loading="lazy">
                            <p class="text-xs text-gray-600 mb-4">Kamu belum membuat pesanan</p>
                            <a href="index.php#pricing" class="bg-gray-900 text-white text-xs rounded px-4 py-2 hover:bg-gray-800 transition-colors">
                                Pesan Sekarang
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Form Tab -->
                <div class="tab-content hidden" id="form-content">
                    <div class="flex flex-col items-center justify-center mt-12">
                        <img alt="Empty form" class="mb-4" height="150" src="<?= htmlspecialchars($emptyFormPath) ?>" width="150" loading="lazy"/>
                        <p class="text-xs text-gray-600 mb-4">Belum ada form tersedia</p>
                        <button class="bg-gray-900 text-white text-xs rounded px-4 py-2 hover:bg-gray-800 transition-colors" type="button">
                            Buat Form Baru
                        </button>
                    </div>
                </div>

                <!-- Pengaturan Tab -->
                <div class="tab-content hidden" id="pengaturan-content">
                    <form method="post" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" value="<?= htmlspecialchars($_SESSION['user']['nama'], ENT_QUOTES, 'UTF-8') ?>" 
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8') ?>" 
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                            <input type="file" name="avatar" accept="image/jpeg,image/png" 
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            
                            <?php if ($_SESSION['user']['avatar'] !== 'default-avatar.jpg'): ?>
                                <div class="mt-2 flex items-center">
                                    <img src="<?= htmlspecialchars($avatarPath) ?>" class="rounded-full w-16 h-16 object-cover mr-3" loading="lazy">
                                    <span class="text-sm text-gray-600">Foto saat ini</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pt-2">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-2 text-xs md:text-sm">
                <h3 class="font-semibold text-white">Info Kontak</h3>
                <p><span class="font-semibold">Email</span><br/>hbndesign@gmail.com</p>
                <p><span class="font-semibold">No Telepon / WhatsApp</span><br/>+62 857-2476-5884</p>
                <p><span class="font-semibold">Alamat</span><br/>Cimahpar, Bogor Utara, Kota Bogor, Jawa Barat.</p>
                <p><span class="font-semibold">Temukan Kami</span><br/>
                    <a aria-label="Instagram" class="inline-block text-gray-300 hover:text-white" href="https://www.instagram.com/hbn_design">
                        <i class="fab fa-instagram text-lg"></i>
                    </a>
                </p>
            </div>
            
            <div class="space-y-2 text-xs md:text-sm">
                <h3 class="font-semibold text-white mb-2">Metode Pembayaran</h3>
                <div class="grid grid-cols-2 gap-2 max-w-xs">
                    <img alt="BCA" class="rounded" height="40" src="https://storage.googleapis.com/a1aa/image/87108c73-0dea-4f1b-be37-957bbfa4f3a6.jpg" width="80" loading="lazy"/>
                    <img alt="BNI" class="rounded" height="40" src="https://storage.googleapis.com/a1aa/image/e212d1f8-2613-4d2a-ccad-1c0be771b57d.jpg" width="80" loading="lazy"/>
                    <img alt="Permata Bank" class="rounded" height="40" src="https://storage.googleapis.com/a1aa/image/8d3c78ff-b9c8-4d90-7525-5b4844eccfff.jpg" width="80" loading="lazy"/>
                    <img alt="Gopay" class="rounded" height="40" src="https://storage.googleapis.com/a1aa/image/7d6bf473-0e28-4605-f606-187b6c6d86f9.jpg" width="80" loading="lazy"/>
                    <img alt="OVO" class="rounded" height="40" src="https://storage.googleapis.com/a1aa/image/99324274-1fe3-436f-ea96-8ac8a23e23f0.jpg" width="80" loading="lazy"/>
                    <img alt="DANA" class="rounded" height="40" src="https://storage.googleapis.com/a1aa/image/3a43b817-145a-4377-53d0-88deda248f4c.jpg" width="80" loading="lazy"/>
                    <img alt="Shopee Pay" class="rounded" height="40" src="https://storage.googleapis.com/a1aa/image/4d446012-d033-42af-e59c-88d7c89c3ac0.jpg" width="80" loading="lazy"/>
                    <img alt="Alfamart" class="rounded" height="40" src="https://storage.googleapis.com/a1aa/image/cd5b332a-0656-4083-90c5-24d72ea79c92.jpg" width="80" loading="lazy"/>
                    <img alt="Mandiri" class="rounded" height="40" src="https://storage.googleapis.com/a1aa/image/fbff4c38-d5d3-48c1-6b6f-4b3b0791ecad.jpg" width="80" loading="lazy"/>
                </div>
            </div>
            
            <div class="space-y-2 text-xs md:text-sm">
                <h3 class="font-semibold text-white">Tentang</h3>
                <p class="font-semibold text-gray-300 text-xs md:text-sm">Official hbn_design</p>
                <p class="text-gray-400 text-xs md:text-sm leading-relaxed">
                    Saya Hiban Sakif, logo & brand identity designer dengan 5+ tahun pengalaman.
                    Telah membantu lebih dari 50 bisnis membangun identitas visual yang kuat dan
                    berkarakter sesuai dengan 'brand value' mereka.
                </p>
                <a class="inline-flex items-center text-gray-400 hover:text-white text-xs md:text-sm" href="index.html">
                    <i class="fas fa-angle-right mr-1"></i> Selengkapnya
                </a>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 text-gray-500 text-xs text-center">
            Copyright © <?= date('Y') ?> YN Merch. All Rights Reserved.
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Tab functionality
    document.querySelectorAll('.tab-btn').forEach(button => {
        button.addEventListener('click', () => {
            const tab = button.dataset.tab;
            
            // Update active tab button
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('text-sky-500', 'border-sky-500');
                btn.classList.add('text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            });
            button.classList.add('text-sky-500', 'border-sky-500');
            button.classList.remove('text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            
            // Show selected tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('active');
            });
            document.getElementById(`${tab}-content`).classList.remove('hidden');
            document.getElementById(`${tab}-content`).classList.add('active');
        });
    });

    // Handle form submission with fetch API
    const profileForm = document.querySelector('#pengaturan-content form');
    if (profileForm) {
        profileForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    const result = await response.text();
                    console.error('Unexpected response:', result);
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan perubahan');
                window.location.reload();
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }
    </script>
</body>
</html>