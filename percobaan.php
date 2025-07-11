

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paket Layanan - HBN Design</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .package-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .pricing-card {
            width: 300px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            background: white;
        }
        
        .pricing-card:hover {
            transform: translateY(-10px);
        }
        
        .pricing-header {
            padding: 20px;
            text-align: center;
            color: white;
        }
        
        .pricing-header.starter {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .pricing-header.premium {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }
        
        .pricing-title {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        
        .pricing-timeline {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .pricing-features {
            padding: 20px;
        }
        
        .pricing-features ul {
            list-style: none;
            padding: 0;
        }
        
        .pricing-features li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .pricing-features li:last-child {
            border-bottom: none;
        }
        
        .pricing-btn {
            width: 100%;
            border: none;
            padding: 12px;
            font-weight: bold;
            border-radius: 0;
        }
        
        .section-title {
            text-align: center;
            margin-top: 50px;
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }
        
        .custom-swal-container {
            z-index: 2000 !important;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">HBN Design</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Paket Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Portofolio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Kontak</a>
                    </li>
                    <?php if(isset($_SESSION['user'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- New Pricing Section -->
    <h2 class="section-title">Paket Layanan Kami</h2>

    <div class="package-container">
        <!-- Tombol Paket Starter -->
        <div class="package-btn starter" onclick="showPackageWizard('starter')">
            <div class="pricing-card">
                <div class="pricing-header starter">
                    <h3 class="pricing-title">Starter Package</h3>
                    <div class="pricing-timeline">
                        <i class="far fa-clock"></i>
                        5-7 Hari</div>
                </div>
                <div class="pricing-features">
                    <ul>
                        <li>Primary Logo</li>
                        <li>Color Palette</li>
                        <li>Typography Pairing</li>
                        <li>3D Mockups</li>
                        <li>A3 Bradboard Guideline</li>
                    </ul>
                </div>
                <button id="btnStarter" class="pricing-btn btn btn-outline-success" data-paket="starter">Pilih Paket</button>
            </div>
        </div>

        <!-- Tombol Paket Exclusive -->
        <div class="package-btn exclusive" onclick="showPackageWizard('exclusive')">
            <div class="pricing-card">
                <div class="pricing-header" style="background: linear-gradient(135deg, #007bff, #6610f2);">
                    <h3 class="pricing-title">Exclusive Plus</h3>
                    <div class="pricing-timeline">
                        <i class="far fa-clock"></i>
                        3-4 Minggu</div>
                </div>
                <div class="pricing-features">
                    <ul>
                        <li>Primary Logo</li>
                        <li>Logo Variation</li>
                        <li>Color Palette</li>
                        <li>Typography Pairing</li>
                        <li>3D Mockups</li>
                        <li>Brand Pattern/Element</li>
                        <li>Stationary Design</li>
                        <li>Social Media Kit</li>
                        <li>Brand Guidelines</li>
                    </ul>
                </div>
                <button id="btnExclusive" class="pricing-btn btn btn-outline-primary" data-paket="exclusive">Pilih Paket</button>
            </div>
        </div>

        <!-- Tombol Paket Premium -->
        <div class="package-btn premium" onclick="showPackageWizard('premium')">
            <div class="pricing-card">
                <div class="pricing-header premium">
                    <h3 class="pricing-title">Premium Package</h3>
                    <div class="pricing-timeline">
                        <i class="far fa-clock"></i>
                        12 Minggu</div>
                </div>
                <div class="pricing-features">
                    <ul>
                        <li>Primary Logo</li>
                        <li>Logo Variation</li>
                        <li>Color Palette</li>
                        <li>Typography Pairing</li>
                        <li>3D Mockups</li>
                        <li>Brand Guidelines</li>
                        <li>Unlimited Revisions</li>
                        <li>Priority Support</li>
                        <li>Dedicated Designer</li>
                    </ul>
                </div>
                <button id="btnPremium" class="pricing-btn btn btn-outline-warning" data-paket="premium">Pilih Paket</button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Konfigurasi wizard untuk setiap paket
        const packageConfigs = {
            starter: {
                title: "Starter Package",
                steps: [
                    {
                        title: "Deskripsi Paket",
                        html: `
                            <p><strong>Order ID:</strong> ${generateOrderId('ST')}</p>
                            <p>Layanan dasar dengan timeline 5-7 hari kerja</p>
                            <div class="info-box" style="background:#f8f9fa;padding:15px;border-radius:5px;margin-top:10px">
                                <p>2 interval pembayaran</p>
                                <p>Termin 1: <strong>Rp 99.500</strong></p>
                                <p>Termin 2: <strong>Rp 99.500</strong></p>
                                <p>Total biaya: <strong>Rp 199.000</strong></p>
                            </div>
                        `,
                        icon: "info"
                    }, 
                    {
                        title: "Deskripsi & Upload Dokumen",
                        html: `
                            <div class="form-group">
                                <label>Deskripsi Proyek*</label>
                                <textarea 
                                    id="projectDescription" 
                                    class="form-control" 
                                    rows="3" 
                                    placeholder="Jelaskan detail proyek Anda..."
                                    style="width:100%; margin-bottom:15px; padding:8px; border-radius:5px; border:1px solid #ddd"
                                    required
                                ></textarea>
                                <div id="descError" style="color:red; font-size:12px; display:none; margin-top:5px">
                                    <i class="fas fa-exclamation-circle"></i> Deskripsi wajib diisi
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Upload Referensi (Maks 3 foto)</label>
                                <div id="imagePreview" style="
                                    display: flex;
                                    flex-wrap: wrap;
                                    gap: 10px;
                                    margin-bottom: 15px;
                                    min-height: 90px;
                                    border: 2px dashed #eee;
                                    padding: 10px;
                                    border-radius: 5px;
                                ">
                                    <div style="
                                        width:100%;
                                        text-align:center;
                                        color:#6c757d;
                                        padding:20px 0;
                                    ">
                                        <i class="fas fa-images" style="font-size:24px"></i>
                                        <p style="margin-top:5px">Belum ada foto</p>
                                    </div>
                                </div>
                                
                                <input 
                                    type="file" 
                                    id="imageUpload" 
                                    accept="image/*" 
                                    multiple
                                    style="display:none"
                                    onchange="handleFileUpload(this)"
                                >
                                
                                <button 
                                    type="button"
                                    onclick="document.getElementById('imageUpload').click()" 
                                    style="
                                        width: 100%;
                                        padding: 10px;
                                        background: #f8f9fa;
                                        border: 1px solid #ddd;
                                        border-radius: 5px;
                                        cursor: pointer;
                                        transition: all 0.3s;
                                    "
                                    onmouseover="this.style.background='#e9ecef'" 
                                    onmouseout="this.style.background='#f8f9fa'"
                                >
                                    <i class="fas fa-cloud-upload-alt"></i> Pilih Foto
                                </button>
                                <small style="display:block; margin-top:5px; color:#6c757d">
                                    Format: JPG/PNG (Maks 2MB per foto)
                                </small>
                            </div>
                        `,
                        icon: "info",
                        didOpen: () => {
                            // Inisialisasi variabel untuk menyimpan file
                            window.uploadedFiles = [];

                            // Fungsi untuk menampilkan preview
                            window.handleFileUpload = function (input) {
                                const preview = document.getElementById('imagePreview');
                                const files = input.files;

                                // Reset dan batasi maksimal 3 file
                                if (files.length > 3) {
                                    Swal.showValidationMessage('Maksimal 3 foto yang diupload');
                                    return;
                                }

                                // Validasi ukuran file
                                for (let file of files) {
                                    if (file.size > 2 * 1024 * 1024) {
                                        Swal.showValidationMessage(`File ${file.name} melebihi 2MB`);
                                        return;
                                    }
                                }

                                // Simpan file
                                window.uploadedFiles = Array.from(files);

                                // Tampilkan preview
                                preview.innerHTML = '';

                                if (window.uploadedFiles.length === 0) {
                                    preview.innerHTML = `
                                        <div style="
                                            width:100%;
                                            text-align:center;
                                            color:#6c757d;
                                            padding:20px 0;
                                        ">
                                            <i class="fas fa-images" style="font-size:24px"></i>
                                            <p style="margin-top:5px">Belum ada foto</p>
                                        </div>
                                    `;
                                } else {
                                    window.uploadedFiles.forEach((file, index) => {
                                        const reader = new FileReader();
                                        reader.onload = function (e) {
                                            const previewItem = document.createElement('div');
                                            previewItem.style.position = 'relative';
                                            previewItem.style.width = '80px';

                                            // Gambar preview
                                            const img = document.createElement('img');
                                            img.src = e.target.result;
                                            img.style.width = '80px';
                                            img.style.height = '80px';
                                            img.style.objectFit = 'cover';
                                            img.style.borderRadius = '5px';
                                            img.style.border = '1px solid #ddd';

                                            // Tombol hapus
                                            const deleteBtn = document.createElement('button');
                                            deleteBtn.innerHTML = '<i class="fas fa-times"></i>';
                                            deleteBtn.style.position = 'absolute';
                                            deleteBtn.style.top = '-5px';
                                            deleteBtn.style.right = '-5px';
                                            deleteBtn.style.background = 'red';
                                            deleteBtn.style.color = 'white';
                                            deleteBtn.style.border = 'none';
                                            deleteBtn.style.borderRadius = '50%';
                                            deleteBtn.style.width = '20px';
                                            deleteBtn.style.height = '20px';
                                            deleteBtn.style.cursor = 'pointer';
                                            deleteBtn.style.display = 'flex';
                                            deleteBtn.style.alignItems = 'center';
                                            deleteBtn.style.justifyContent = 'center';
                                            deleteBtn.style.fontSize = '10px';
                                            deleteBtn.onclick = (event) => {
                                                event.stopPropagation();
                                                window.uploadedFiles.splice(index, 1);
                                                handleFileUpload({files: window.uploadedFiles});
                                            };

                                            previewItem.appendChild(img);
                                            previewItem.appendChild(deleteBtn);
                                            preview.appendChild(previewItem);
                                        };
                                        reader.readAsDataURL(file);
                                    });
                                }
                            };
                        },
                        preConfirm: () => {
                            const desc = document.getElementById('projectDescription').value;
                            const descError = document.getElementById('descError');

                            // Validasi deskripsi
                            if (!desc || desc.trim() === '') {
                                descError.style.display = 'block';
                                Swal.showValidationMessage('Deskripsi proyek harus diisi');
                                return false;
                            } else {
                                descError.style.display = 'none';
                            }

                            return {
                                description: desc,
                                images: window.uploadedFiles || []
                            };
                        }
                    }, 
                    {
                        title: "Konfirmasi Pembayaran",
                        html: `
                            <div class="payment-confirmation">
                                <p>Anda akan memesan <strong>Starter Package</strong></p>
                                <p>Termin 1: <strong>Rp 99.500</strong></p>
                                <p>Termin 2: <strong>Rp 99.500</strong></p>
                                <p>Total: <strong>Rp 199.000</strong></p>
                                
                                <div class="bank-info">
                                    <h4>Transfer ke Rekening Bank:</h4>
                                    <p><strong>Bank Mandiri</strong></p>
                                    <p>No. Rek: 123-456-7890</p>
                                    <p>a.n. HBN-Design</p>
                                </div>
                                
                                <div class="form-group">
                                    <label>Email untuk konfirmasi*</label>
                                    <input type="email" id="confirmEmail" class="form-control" placeholder="your@email.com" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Upload Bukti Transfer*</label>
                                    <input type="file" id="paymentProof" class="form-control" accept="image/*,.pdf" required>
                                    <small class="form-text text-muted">Format: JPG/PNG/PDF (Maks 2MB)</small>
                                    <div id="proofPreview" style="margin-top:10px;"></div>
                                </div>
                            </div>
                        `,
                        icon: "warning",
                        confirmButtonText: "Konfirmasi Pembelian",
                        confirmButtonColor: "#4CAF50",
                        buttonsStyling: true,
                        showCancelButton: true,
                        cancelButtonText: "< Kembali",
                        didOpen: () => {
                            const proofInput = document.getElementById('paymentProof');
                            proofInput.addEventListener('change', function (e) {
                                const file = e.target.files[0];
                                if (!file) return;
                                
                                // Validasi ukuran file
                                if (file.size > 2 * 1024 * 1024) {
                                    Swal.showValidationMessage('Ukuran file maksimal 2MB');
                                    return;
                                }

                                // Tampilkan preview
                                const preview = document.getElementById('proofPreview');
                                if (file.type.startsWith('image/')) {
                                    const reader = new FileReader();
                                    reader.onload = function (e) {
                                        preview.innerHTML = `
                                            <img src="${e.target.result}" style="max-width:200px; max-height:200px; border:1px solid #ddd;">
                                        `;
                                    };
                                    reader.readAsDataURL(file);
                                } else {
                                    preview.innerHTML = `<p>File: ${file.name}</p>`;
                                }
                            });
                        },
                        preConfirm: () => {
                            const email = document.getElementById('confirmEmail').value;
                            const proof = document.getElementById('paymentProof').files[0];

                            // Validasi email
                            if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
                                Swal.showValidationMessage('Email tidak valid');
                                return false;
                            }

                            // Validasi bukti transfer
                            if (!proof) {
                                Swal.showValidationMessage('Bukti transfer wajib diupload');
                                return false;
                            }

                            return {
                                email: email,
                                payment_proof: proof
                            };
                        }
                    }
                ]
            },
            exclusive: {
                title: "Exclusive Plus",
                steps: [
                    {
                        title: "Deskripsi Paket",
                        html: `
                            <p><strong>Order ID:</strong> ${generateOrderId('EX')}</p>
                            <p>Layanan premium dengan timeline 3-4 minggu</p>
                            <div class="info-box" style="background:#f8f9fa;padding:15px;border-radius:5px;margin-top:10px">
                                <p>2 interval pembayaran</p>
                                <p>Termin 1: <strong>Rp 249.500</strong></p>
                                <p>Termin 2: <strong>Rp 249.500</strong></p>
                                <p>Total: <strong>Rp 499.000</strong></p>
                                <p>Termasuk revisi 3x</p>
                            </div>
                        `,
                        icon: "info"
                    },
                    // Langkah 2 dan 3 sama dengan starter package
{
                        title: "Deskripsi & Upload Dokumen",
                        html: `
                            <div class="form-group">
                                <label>Deskripsi Proyek*</label>
                                <textarea 
                                    id="projectDescription" 
                                    class="form-control" 
                                    rows="3" 
                                    placeholder="Jelaskan detail proyek Anda..."
                                    style="width:100%; margin-bottom:15px; padding:8px; border-radius:5px; border:1px solid #ddd"
                                    required
                                ></textarea>
                                <div id="descError" style="color:red; font-size:12px; display:none; margin-top:5px">
                                    <i class="fas fa-exclamation-circle"></i> Deskripsi wajib diisi
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Upload Referensi (Maks 3 foto)</label>
                                <div id="imagePreview" style="
                                    display: flex;
                                    flex-wrap: wrap;
                                    gap: 10px;
                                    margin-bottom: 15px;
                                    min-height: 90px;
                                    border: 2px dashed #eee;
                                    padding: 10px;
                                    border-radius: 5px;
                                ">
                                    <div style="
                                        width:100%;
                                        text-align:center;
                                        color:#6c757d;
                                        padding:20px 0;
                                    ">
                                        <i class="fas fa-images" style="font-size:24px"></i>
                                        <p style="margin-top:5px">Belum ada foto</p>
                                    </div>
                                </div>
                                
                                <input 
                                    type="file" 
                                    id="imageUpload" 
                                    accept="image/*" 
                                    multiple
                                    style="display:none"
                                    onchange="handleFileUpload(this)"
                                >
                                
                                <button 
                                    type="button"
                                    onclick="document.getElementById('imageUpload').click()" 
                                    style="
                                        width: 100%;
                                        padding: 10px;
                                        background: #f8f9fa;
                                        border: 1px solid #ddd;
                                        border-radius: 5px;
                                        cursor: pointer;
                                        transition: all 0.3s;
                                    "
                                    onmouseover="this.style.background='#e9ecef'" 
                                    onmouseout="this.style.background='#f8f9fa'"
                                >
                                    <i class="fas fa-cloud-upload-alt"></i> Pilih Foto
                                </button>
                                <small style="display:block; margin-top:5px; color:#6c757d">
                                    Format: JPG/PNG (Maks 2MB per foto)
                                </small>
                            </div>
                        `,
                        icon: "info",
                        didOpen: () => {
                            // Inisialisasi variabel untuk menyimpan file
                            window.uploadedFiles = [];

                            // Fungsi untuk menampilkan preview
                            window.handleFileUpload = function (input) {
                                const preview = document.getElementById('imagePreview');
                                const files = input.files;

                                // Reset dan batasi maksimal 3 file
                                if (files.length > 3) {
                                    Swal.showValidationMessage('Maksimal 3 foto yang diupload');
                                    return;
                                }

                                // Validasi ukuran file
                                for (let file of files) {
                                    if (file.size > 2 * 1024 * 1024) {
                                        Swal.showValidationMessage(`File ${file.name} melebihi 2MB`);
                                        return;
                                    }
                                }

                                // Simpan file
                                window.uploadedFiles = Array.from(files);

                                // Tampilkan preview
                                preview.innerHTML = '';

                                if (window.uploadedFiles.length === 0) {
                                    preview.innerHTML = `
                                        <div style="
                                            width:100%;
                                            text-align:center;
                                            color:#6c757d;
                                            padding:20px 0;
                                        ">
                                            <i class="fas fa-images" style="font-size:24px"></i>
                                            <p style="margin-top:5px">Belum ada foto</p>
                                        </div>
                                    `;
                                } else {
                                    window.uploadedFiles.forEach((file, index) => {
                                        const reader = new FileReader();
                                        reader.onload = function (e) {
                                            const previewItem = document.createElement('div');
                                            previewItem.style.position = 'relative';
                                            previewItem.style.width = '80px';

                                            // Gambar preview
                                            const img = document.createElement('img');
                                            img.src = e.target.result;
                                            img.style.width = '80px';
                                            img.style.height = '80px';
                                            img.style.objectFit = 'cover';
                                            img.style.borderRadius = '5px';
                                            img.style.border = '1px solid #ddd';

                                            // Tombol hapus
                                            const deleteBtn = document.createElement('button');
                                            deleteBtn.innerHTML = '<i class="fas fa-times"></i>';
                                            deleteBtn.style.position = 'absolute';
                                            deleteBtn.style.top = '-5px';
                                            deleteBtn.style.right = '-5px';
                                            deleteBtn.style.background = 'red';
                                            deleteBtn.style.color = 'white';
                                            deleteBtn.style.border = 'none';
                                            deleteBtn.style.borderRadius = '50%';
                                            deleteBtn.style.width = '20px';
                                            deleteBtn.style.height = '20px';
                                            deleteBtn.style.cursor = 'pointer';
                                            deleteBtn.style.display = 'flex';
                                            deleteBtn.style.alignItems = 'center';
                                            deleteBtn.style.justifyContent = 'center';
                                            deleteBtn.style.fontSize = '10px';
                                            deleteBtn.onclick = (event) => {
                                                event.stopPropagation();
                                                window.uploadedFiles.splice(index, 1);
                                                handleFileUpload({files: window.uploadedFiles});
                                            };

                                            previewItem.appendChild(img);
                                            previewItem.appendChild(deleteBtn);
                                            preview.appendChild(previewItem);
                                        };
                                        reader.readAsDataURL(file);
                                    });
                                }
                            };
                        },
                        preConfirm: () => {
                            const desc = document.getElementById('projectDescription').value;
                            const descError = document.getElementById('descError');

                            // Validasi deskripsi
                            if (!desc || desc.trim() === '') {
                                descError.style.display = 'block';
                                Swal.showValidationMessage('Deskripsi proyek harus diisi');
                                return false;
                            } else {
                                descError.style.display = 'none';
                            }

                            return {
                                description: desc,
                                images: window.uploadedFiles || []
                            };
                        }
                    }, 
                    {
                        title: "Konfirmasi Pembayaran",
                        html: `
                            <div class="payment-confirmation">
                                <p>Anda akan memesan <strong>Starter Package</strong></p>
                                <p>Termin 1: <strong>Rp 99.500</strong></p>
                                <p>Termin 2: <strong>Rp 99.500</strong></p>
                                <p>Total: <strong>Rp 199.000</strong></p>
                                
                                <div class="bank-info">
                                    <h4>Transfer ke Rekening Bank:</h4>
                                    <p><strong>Bank Mandiri</strong></p>
                                    <p>No. Rek: 123-456-7890</p>
                                    <p>a.n. HBN-Design</p>
                                </div>
                                
                                <div class="form-group">
                                    <label>Email untuk konfirmasi*</label>
                                    <input type="email" id="confirmEmail" class="form-control" placeholder="your@email.com" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Upload Bukti Transfer*</label>
                                    <input type="file" id="paymentProof" class="form-control" accept="image/*,.pdf" required>
                                    <small class="form-text text-muted">Format: JPG/PNG/PDF (Maks 2MB)</small>
                                    <div id="proofPreview" style="margin-top:10px;"></div>
                                </div>
                            </div>
                        `,
                        icon: "warning",
                        confirmButtonText: "Konfirmasi Pembelian",
                        confirmButtonColor: "#4CAF50",
                        buttonsStyling: true,
                        showCancelButton: true,
                        cancelButtonText: "< Kembali",
                        didOpen: () => {
                            const proofInput = document.getElementById('paymentProof');
                            proofInput.addEventListener('change', function (e) {
                                const file = e.target.files[0];
                                if (!file) return;
                                
                                // Validasi ukuran file
                                if (file.size > 2 * 1024 * 1024) {
                                    Swal.showValidationMessage('Ukuran file maksimal 2MB');
                                    return;
                                }

                                // Tampilkan preview
                                const preview = document.getElementById('proofPreview');
                                if (file.type.startsWith('image/')) {
                                    const reader = new FileReader();
                                    reader.onload = function (e) {
                                        preview.innerHTML = `
                                            <img src="${e.target.result}" style="max-width:200px; max-height:200px; border:1px solid #ddd;">
                                        `;
                                    };
                                    reader.readAsDataURL(file);
                                } else {
                                    preview.innerHTML = `<p>File: ${file.name}</p>`;
                                }
                            });
                        },
                        preConfirm: () => {
                            const email = document.getElementById('confirmEmail').value;
                            const proof = document.getElementById('paymentProof').files[0];

                            // Validasi email
                            if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
                                Swal.showValidationMessage('Email tidak valid');
                                return false;
                            }

                            // Validasi bukti transfer
                            if (!proof) {
                                Swal.showValidationMessage('Bukti transfer wajib diupload');
                                return false;
                            }

                            return {
                                email: email,
                                payment_proof: proof
                            };
                        }
                    }
                ]
            },
            premium: {
                title: "Premium Package",
                steps: [
                    {
                        title: "Deskripsi Paket",
                        html: `
                            <p><strong>Order ID:</strong> ${generateOrderId('PM')}</p>
                            <p>Layanan lengkap dengan timeline 12 minggu</p>
                            <div class="info-box" style="background:#f8f9fa;padding:15px;border-radius:5px;margin-top:10px">
                                <p>2 interval pembayaran</p>
                                <p>Termin 1: <strong>Rp 499.500</strong></p>
                                <p>Termin 2: <strong>Rp 499.500</strong></p>
                                <p>Total: <strong>Rp 999.000</strong></p>
                                <p>Termasuk unlimited revisi</p>
                            </div>
                        `,
                        icon: "info"
                    },
                    // Langkah 2 dan 3 sama dengan starter package
{
                        title: "Deskripsi & Upload Dokumen",
                        html: `
                            <div class="form-group">
                                <label>Deskripsi Proyek*</label>
                                <textarea 
                                    id="projectDescription" 
                                    class="form-control" 
                                    rows="3" 
                                    placeholder="Jelaskan detail proyek Anda..."
                                    style="width:100%; margin-bottom:15px; padding:8px; border-radius:5px; border:1px solid #ddd"
                                    required
                                ></textarea>
                                <div id="descError" style="color:red; font-size:12px; display:none; margin-top:5px">
                                    <i class="fas fa-exclamation-circle"></i> Deskripsi wajib diisi
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Upload Referensi (Maks 3 foto)</label>
                                <div id="imagePreview" style="
                                    display: flex;
                                    flex-wrap: wrap;
                                    gap: 10px;
                                    margin-bottom: 15px;
                                    min-height: 90px;
                                    border: 2px dashed #eee;
                                    padding: 10px;
                                    border-radius: 5px;
                                ">
                                    <div style="
                                        width:100%;
                                        text-align:center;
                                        color:#6c757d;
                                        padding:20px 0;
                                    ">
                                        <i class="fas fa-images" style="font-size:24px"></i>
                                        <p style="margin-top:5px">Belum ada foto</p>
                                    </div>
                                </div>
                                
                                <input 
                                    type="file" 
                                    id="imageUpload" 
                                    accept="image/*" 
                                    multiple
                                    style="display:none"
                                    onchange="handleFileUpload(this)"
                                >
                                
                                <button 
                                    type="button"
                                    onclick="document.getElementById('imageUpload').click()" 
                                    style="
                                        width: 100%;
                                        padding: 10px;
                                        background: #f8f9fa;
                                        border: 1px solid #ddd;
                                        border-radius: 5px;
                                        cursor: pointer;
                                        transition: all 0.3s;
                                    "
                                    onmouseover="this.style.background='#e9ecef'" 
                                    onmouseout="this.style.background='#f8f9fa'"
                                >
                                    <i class="fas fa-cloud-upload-alt"></i> Pilih Foto
                                </button>
                                <small style="display:block; margin-top:5px; color:#6c757d">
                                    Format: JPG/PNG (Maks 2MB per foto)
                                </small>
                            </div>
                        `,
                        icon: "info",
                        didOpen: () => {
                            // Inisialisasi variabel untuk menyimpan file
                            window.uploadedFiles = [];

                            // Fungsi untuk menampilkan preview
                            window.handleFileUpload = function (input) {
                                const preview = document.getElementById('imagePreview');
                                const files = input.files;

                                // Reset dan batasi maksimal 3 file
                                if (files.length > 3) {
                                    Swal.showValidationMessage('Maksimal 3 foto yang diupload');
                                    return;
                                }

                                // Validasi ukuran file
                                for (let file of files) {
                                    if (file.size > 2 * 1024 * 1024) {
                                        Swal.showValidationMessage(`File ${file.name} melebihi 2MB`);
                                        return;
                                    }
                                }

                                // Simpan file
                                window.uploadedFiles = Array.from(files);

                                // Tampilkan preview
                                preview.innerHTML = '';

                                if (window.uploadedFiles.length === 0) {
                                    preview.innerHTML = `
                                        <div style="
                                            width:100%;
                                            text-align:center;
                                            color:#6c757d;
                                            padding:20px 0;
                                        ">
                                            <i class="fas fa-images" style="font-size:24px"></i>
                                            <p style="margin-top:5px">Belum ada foto</p>
                                        </div>
                                    `;
                                } else {
                                    window.uploadedFiles.forEach((file, index) => {
                                        const reader = new FileReader();
                                        reader.onload = function (e) {
                                            const previewItem = document.createElement('div');
                                            previewItem.style.position = 'relative';
                                            previewItem.style.width = '80px';

                                            // Gambar preview
                                            const img = document.createElement('img');
                                            img.src = e.target.result;
                                            img.style.width = '80px';
                                            img.style.height = '80px';
                                            img.style.objectFit = 'cover';
                                            img.style.borderRadius = '5px';
                                            img.style.border = '1px solid #ddd';

                                            // Tombol hapus
                                            const deleteBtn = document.createElement('button');
                                            deleteBtn.innerHTML = '<i class="fas fa-times"></i>';
                                            deleteBtn.style.position = 'absolute';
                                            deleteBtn.style.top = '-5px';
                                            deleteBtn.style.right = '-5px';
                                            deleteBtn.style.background = 'red';
                                            deleteBtn.style.color = 'white';
                                            deleteBtn.style.border = 'none';
                                            deleteBtn.style.borderRadius = '50%';
                                            deleteBtn.style.width = '20px';
                                            deleteBtn.style.height = '20px';
                                            deleteBtn.style.cursor = 'pointer';
                                            deleteBtn.style.display = 'flex';
                                            deleteBtn.style.alignItems = 'center';
                                            deleteBtn.style.justifyContent = 'center';
                                            deleteBtn.style.fontSize = '10px';
                                            deleteBtn.onclick = (event) => {
                                                event.stopPropagation();
                                                window.uploadedFiles.splice(index, 1);
                                                handleFileUpload({files: window.uploadedFiles});
                                            };

                                            previewItem.appendChild(img);
                                            previewItem.appendChild(deleteBtn);
                                            preview.appendChild(previewItem);
                                        };
                                        reader.readAsDataURL(file);
                                    });
                                }
                            };
                        },
                        preConfirm: () => {
                            const desc = document.getElementById('projectDescription').value;
                            const descError = document.getElementById('descError');

                            // Validasi deskripsi
                            if (!desc || desc.trim() === '') {
                                descError.style.display = 'block';
                                Swal.showValidationMessage('Deskripsi proyek harus diisi');
                                return false;
                            } else {
                                descError.style.display = 'none';
                            }

                            return {
                                description: desc,
                                images: window.uploadedFiles || []
                            };
                        }
                    }, 
                    {
                        title: "Konfirmasi Pembayaran",
                        html: `
                            <div class="payment-confirmation">
                                <p>Anda akan memesan <strong>Starter Package</strong></p>
                                <p>Termin 1: <strong>Rp 99.500</strong></p>
                                <p>Termin 2: <strong>Rp 99.500</strong></p>
                                <p>Total: <strong>Rp 199.000</strong></p>
                                
                                <div class="bank-info">
                                    <h4>Transfer ke Rekening Bank:</h4>
                                    <p><strong>Bank Mandiri</strong></p>
                                    <p>No. Rek: 123-456-7890</p>
                                    <p>a.n. HBN-Design</p>
                                </div>
                                
                                <div class="form-group">
                                    <label>Email untuk konfirmasi*</label>
                                    <input type="email" id="confirmEmail" class="form-control" placeholder="your@email.com" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Upload Bukti Transfer*</label>
                                    <input type="file" id="paymentProof" class="form-control" accept="image/*,.pdf" required>
                                    <small class="form-text text-muted">Format: JPG/PNG/PDF (Maks 2MB)</small>
                                    <div id="proofPreview" style="margin-top:10px;"></div>
                                </div>
                            </div>
                        `,
                        icon: "warning",
                        confirmButtonText: "Konfirmasi Pembelian",
                        confirmButtonColor: "#4CAF50",
                        buttonsStyling: true,
                        showCancelButton: true,
                        cancelButtonText: "< Kembali",
                        didOpen: () => {
                            const proofInput = document.getElementById('paymentProof');
                            proofInput.addEventListener('change', function (e) {
                                const file = e.target.files[0];
                                if (!file) return;
                                
                                // Validasi ukuran file
                                if (file.size > 2 * 1024 * 1024) {
                                    Swal.showValidationMessage('Ukuran file maksimal 2MB');
                                    return;
                                }

                                // Tampilkan preview
                                const preview = document.getElementById('proofPreview');
                                if (file.type.startsWith('image/')) {
                                    const reader = new FileReader();
                                    reader.onload = function (e) {
                                        preview.innerHTML = `
                                            <img src="${e.target.result}" style="max-width:200px; max-height:200px; border:1px solid #ddd;">
                                        `;
                                    };
                                    reader.readAsDataURL(file);
                                } else {
                                    preview.innerHTML = `<p>File: ${file.name}</p>`;
                                }
                            });
                        },
                        preConfirm: () => {
                            const email = document.getElementById('confirmEmail').value;
                            const proof = document.getElementById('paymentProof').files[0];

                            // Validasi email
                            if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
                                Swal.showValidationMessage('Email tidak valid');
                                return false;
                            }

                            // Validasi bukti transfer
                            if (!proof) {
                                Swal.showValidationMessage('Bukti transfer wajib diupload');
                                return false;
                            }

                            return {
                                email: email,
                                payment_proof: proof
                            };
                        }
                    }
                ]
            }
        };

        // Fungsi untuk generate order ID di JavaScript
        function generateOrderId(prefix = 'ORD') {
            return prefix + '-' + Math.random().toString(36).substr(2, 8).toUpperCase();
        }

        // Fungsi untuk menampilkan wizard berdasarkan paket
        async function showPackageWizard(packageType) {
            // Cek apakah user sudah login
            const isLoggedIn = <?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>;
            if (!isLoggedIn) {
                Swal.fire({
                    title: 'Login Required',
                    text: 'Anda harus login terlebih dahulu untuk memesan paket.',
                    icon: 'warning',
                    confirmButtonText: 'Login',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'login.php';
                    }
                });
                return;
            }

            const config = packageConfigs[packageType];
            const steps = ["1", "2", "3"];

            const Queue = Swal.mixin({
                progressSteps: steps, 
                allowOutsideClick: false,
                confirmButtonText: 'Selanjutnya >',
                showCancelButton: true,
                cancelButtonText: '< Kembali',
                showClass: {
                    backdrop: 'swal2-noanimation'
                },
                hideClass: {
                    backdrop: 'swal2-noanimation'
                },
                scrollbarPadding: false,
                customClass: {
                    container: 'custom-swal-container'
                }
            });

            // Step 1
            let result = await Queue.fire({
                title: config.title,
                html: config.steps[0].html,
                icon: config.steps[0].icon,
                currentProgressStep: 0
            });

            if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) 
                return;
            
            // Step 2
            result = await Queue.fire({
                title: config.title,
                html: config.steps[1].html,
                icon: config.steps[1].icon,
                currentProgressStep: 1
            });

            if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                await showPackageWizard(packageType); // Kembali ke step 1
                return;
            }

            // Step 3
            result = await Queue.fire({
                title: config.title,
                html: config.steps[2].html,
                icon: config.steps[2].icon,
                confirmButtonText: config.steps[2].confirmButtonText,
                currentProgressStep: 2
            });

            if (result.isConfirmed) {
                try {
                    // Siapkan data untuk dikirim ke server
                    const formData = new FormData();
                    formData.append('package_type', packageType);
                    formData.append('description', result.value.description);
                    formData.append('email', result.value.email);
                    formData.append('payment_proof', result.value.payment_proof);

                    // Tambahkan referensi gambar jika ada
                    if (result.value.images && result.value.images.length > 0) {
                        result.value.images.forEach((file, index) => {
                            formData.append(`references[${index}]`, file);
                        });
                    }

                    // Kirim data ke server
                    const response = await fetch('index.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        Swal.fire(
                            'Berhasil!',
                            `Pemesanan ${config.title} telah dikonfirmasi`,
                            'success'
                        ).then(() => {
                            // Redirect atau lakukan sesuatu setelah sukses
                            window.location.href = 'orders.php';
                        });
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan saat memproses pesanan');
                    }
                } catch (error) {
                    Swal.fire(
                        'Error!',
                        error.message,
                        'error'
                    );
                }
            }
        }
    </script>
    <script>
function handleFileUpload(event) {
  const files = event.target.files;
  const previewContainer = document.getElementById('preview-container');
  previewContainer.innerHTML = ''; // Kosongkan preview sebelumnya

  // Validasi jumlah file
  if (files.length > 3) {
    alert('Maksimal 3 file yang boleh diupload!');
    return;
  }

  // Proses setiap file
  Array.from(files).forEach(file => {
    // Validasi ukuran file (2MB)
    if (file.size > 2 * 1024 * 1024) {
      alert(`File ${file.name} melebihi 2MB!`);
      return;
    }

    // Validasi tipe file
    if (!['image/jpeg', 'image/png'].includes(file.type)) {
      alert(`File ${file.name} harus JPG/PNG!`);
      return;
    }

    // Buat preview
    const reader = new FileReader();
    reader.onload = function(e) {
      const preview = document.createElement('div');
      preview.className = 'preview-item';
      preview.innerHTML = `
        <img src="${e.target.result}" class="img-thumbnail" style="height:100px">
        <button onclick="removePreview(this)" class="btn btn-sm btn-danger mt-1">Hapus</button>
      `;
      previewContainer.appendChild(preview);
    };
    reader.readAsDataURL(file);
  });
}

function removePreview(button) {
  button.parentElement.remove();
}
</script>
</body>
</html>