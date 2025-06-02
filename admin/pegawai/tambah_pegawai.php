<?php
session_start();
require_once '../../conn.php';

if (!isset($_SESSION['session_username'])) {
    header("Location: /login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $username_pegawai = $_POST['username'];
    $password = $_POST['password'];
    $foto = $_POST['foto'] ?? 'default-user.jpg';
    
    try {
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM pegawai WHERE username = ?");
        $check_stmt->execute([$username_pegawai]);
        $username_exists = $check_stmt->fetchColumn();
        
        if ($username_exists > 0) {
            $_SESSION['error'] = "Username sudah digunakan!";
            header("Location: index.php");
            exit();
        }
        
        $stmt = $conn->prepare("INSERT INTO pegawai (nama, username, password, status_aktif, foto) VALUES (?, ?, ?, 1, ?)");
        $stmt->execute([$nama, $username_pegawai, $password, $foto]);
        
        $pegawai_id = $conn->lastInsertId();
        $stmt = $conn->prepare("INSERT INTO stok_pegawai (pegawai_id) VALUES (?)");
        $stmt->execute([$pegawai_id]);
        
        $stmt = $conn->prepare("INSERT INTO audit_log (user_id, role, aksi, waktu_aksi) VALUES (?, 'admin', ?, NOW())");
        $stmt->execute([1, "Menambah pegawai: $nama"]);
        
        $_SESSION['message'] = "Pegawai '$nama' berhasil ditambahkan!";
        
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    header("Location: index.php");
    exit();
}
?>