<?php
// Koneksi database (sesuaikan dengan konfigurasi Anda)
$conn = new mysqli('localhost', 'username', 'password', 'nama_database');

// Fungsi untuk menambahkan karya baru
if(isset($_POST['add_portfolio'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    
    // Handle file upload
    $target_dir = "Assets/Img/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    
    $sql = "INSERT INTO portfolio (title, description, image_path) VALUES ('$title', '$description', '$target_file')";
    $conn->query($sql);
}

// Fungsi untuk menghapus karya
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM portfolio WHERE id=$id";
    $conn->query($sql);
}

// Ambil data portofolio
$sql = "SELECT * FROM portfolio";
$result = $conn->query($sql);
$portfolio_items = $result->fetch_all(MYSQLI_ASSOC);
?>