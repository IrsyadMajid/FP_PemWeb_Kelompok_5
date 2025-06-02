<?php
session_start();
require_once '../../conn.php';

if (!isset($_SESSION['session_username'])) {
    header("Location: /login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pegawai_id = $_POST['pegawai_id'];
    $nama = $_POST['nama'];
    $username_pegawai = $_POST['username'];
    $foto = $_POST['foto'];
    
    try {
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM pegawai WHERE username = ? AND pegawai_id != ?");
        $check_stmt->execute([$username_pegawai, $pegawai_id]);
        $username_exists = $check_stmt->fetchColumn();
        
        if ($username_exists > 0) {
            $_SESSION['error'] = "Username sudah digunakan oleh pegawai lain!";
            header("Location: index.php");
            exit();
        }
        
        $old_data_stmt = $conn->prepare("SELECT nama FROM pegawai WHERE pegawai_id = ?");
        $old_data_stmt->execute([$pegawai_id]);
        $old_nama = $old_data_stmt->fetchColumn();
        
        $stmt = $conn->prepare("UPDATE pegawai SET nama = ?, username = ?, foto = ? WHERE pegawai_id = ?");
        $stmt->execute([$nama, $username_pegawai, $foto, $pegawai_id]);
        
        $stmt = $conn->prepare("INSERT INTO audit_log (user_id, role, aksi, waktu_aksi) VALUES (?, 'admin', ?, NOW())");
        $stmt->execute([1, "Mengubah data pegawai: $old_nama menjadi $nama"]);
        
        $_SESSION['message'] = "Data pegawai '$nama' berhasil diperbarui!";
        
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    header("Location: index.php");
    exit();
}
?>