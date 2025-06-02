<?php
session_start();
require_once '../../conn.php';

if (!isset($_SESSION['session_username'])) {
    header("Location: /login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pegawai_id = $_POST['pegawai_id'];
    
    try {
        $nama_stmt = $conn->prepare("SELECT nama FROM pegawai WHERE pegawai_id = ?");
        $nama_stmt->execute([$pegawai_id]);
        $nama_pegawai = $nama_stmt->fetchColumn();
        
        if (!$nama_pegawai) {
            $_SESSION['error'] = "Pegawai tidak ditemukan!";
            header("Location: index.php");
            exit();
        }
        
        $conn->beginTransaction();
        
        $penjualan_stmt = $conn->prepare("SELECT COUNT(*) FROM penjualan WHERE pegawai_id = ?");
        $penjualan_stmt->execute([$pegawai_id]);
        $penjualan_count = $penjualan_stmt->fetchColumn();
        
        $laporan_stmt = $conn->prepare("SELECT COUNT(*) FROM laporan WHERE pegawai_id = ?");
        $laporan_stmt->execute([$pegawai_id]);
        $laporan_count = $laporan_stmt->fetchColumn();
        
        if ($penjualan_count > 0 || $laporan_count > 0) {
            $stmt = $conn->prepare("UPDATE pegawai SET status_aktif = 0 WHERE pegawai_id = ?");
            $stmt->execute([$pegawai_id]);
            
            $_SESSION['message'] = "Pegawai '$nama_pegawai' dinonaktifkan karena memiliki data transaksi!";
        } else {
            $stmt = $conn->prepare("DELETE FROM stok_pegawai WHERE pegawai_id = ?");
            $stmt->execute([$pegawai_id]);
            
            $stmt = $conn->prepare("DELETE FROM pegawai WHERE pegawai_id = ?");
            $stmt->execute([$pegawai_id]);
            
            $_SESSION['message'] = "Pegawai '$nama_pegawai' berhasil dihapus!";
        }
        
        $stmt = $conn->prepare("INSERT INTO audit_log (user_id, role, aksi, waktu_aksi) VALUES (?, 'admin', ?, NOW())");
        $stmt->execute([1, "Menghapus/menonaktifkan pegawai: $nama_pegawai"]);
        
        $conn->commit();
        
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    header("Location: index.php");
    exit();
}
?>