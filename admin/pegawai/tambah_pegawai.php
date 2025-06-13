<?php
session_start();
require_once '../../conn.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $username_pegawai = $_POST['username'];
    $password = $_POST['password'];
    $foto_nama = 'default-user.jpg'; // Default foto

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $upload_dir = '../../assets/images/';
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5 MB

        $file_type = $_FILES['foto']['type'];
        $file_size = $_FILES['foto']['size'];

        if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
            $file_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto_nama = uniqid('pegawai_', true) . '.' . $file_extension;
            $upload_file = $upload_dir . $foto_nama;

            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $upload_file)) {
                $_SESSION['error'] = "Gagal mengunggah foto!";
                header("Location: index.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Tipe file tidak valid atau ukuran terlalu besar!";
            header("Location: index.php");
            exit();
        }
    }

    try {
        
        $stmt = $conn->prepare("INSERT INTO pegawai (nama, username, password, status_aktif, foto) VALUES (?, ?, ?, 1, ?)");
        $stmt->execute([$nama, $username_pegawai, password_hash($password, PASSWORD_DEFAULT), $foto_nama]); // Hashing password
                
        $_SESSION['message'] = "Pegawai '$nama' berhasil ditambahkan!";
        
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    header("Location: index.php");
    exit();
}
?>