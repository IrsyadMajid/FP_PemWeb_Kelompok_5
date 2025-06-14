<?php
session_start();
require_once '../../conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pegawai_id = $_POST['pegawai_id'];
    $nama = $_POST['nama'];
    $username_pegawai = $_POST['username'];
    $foto_lama = $_POST['foto_lama'];
    $foto_nama = $foto_lama; 

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

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_file)) {
                if ($foto_lama != 'default-user.jpg' && file_exists($upload_dir . $foto_lama)) {
                    unlink($upload_dir . $foto_lama);
                }
            } else {
                $_SESSION['error'] = "Gagal mengunggah foto baru!";
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
        $stmt = $conn->prepare("UPDATE pegawai SET nama = ?, username = ?, foto = ? WHERE pegawai_id = ?");
        $stmt->execute([$nama, $username_pegawai, $foto_nama, $pegawai_id]);
        
        $_SESSION['message'] = "Data pegawai '$nama' berhasil diperbarui!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    header("Location: index.php");
    exit();
}
