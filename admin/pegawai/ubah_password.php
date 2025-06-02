<?php
session_start();
require_once '../../conn.php';

if (!isset($_SESSION['session_username'])) {
    header("Location: /login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pegawai_id = $_POST['pegawai_id'];
    $new_password = $_POST['new_password'];
    
    try {
        if (strlen($new_password) < 6) {
            $_SESSION['error'] = "Password minimal 6 karakter!";
            header("Location: index.php");
            exit();
        }
        
        $nama_stmt = $conn->prepare("SELECT nama FROM pegawai WHERE pegawai_id = ?");
        $nama_stmt->execute([$pegawai_id]);
        $nama_pegawai = $nama_stmt->fetchColumn();
        
        if (!$nama_pegawai) {
            $_SESSION['error'] = "Pegawai tidak ditemukan!";
            header("Location: index.php");
            exit();
        }
        
        $stmt = $conn->prepare("UPDATE pegawai SET password = ? WHERE pegawai_id = ?");
        $stmt->execute([$new_password, $pegawai_id]);
        
        $stmt = $conn->prepare("INSERT INTO audit_log (user_id, role, aksi, waktu_aksi) VALUES (?, 'admin', ?, NOW())");
        $stmt->execute([1, "Mengubah password pegawai: $nama_pegawai"]);
        
        $_SESSION['message'] = "Password pegawai '$nama_pegawai' berhasil diubah!";
        
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    header("Location: index.php");
    exit();
}
?>