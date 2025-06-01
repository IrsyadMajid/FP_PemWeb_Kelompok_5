<?php
session_start();

if (!isset($_SESSION['session_username'])) {
    header('Location: /login.php');
    exit();
}

$path_conn = realpath(__DIR__ . '/../../conn.php');
if (!$path_conn || !file_exists($path_conn)) {
    die("Database configuration file not found");
}
require_once $path_conn;

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit();
}

try {
    $stmt = $conn->prepare("SELECT * FROM penjualan WHERE penjualan_id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        $_SESSION['error'] = "Data penjualan tidak ditemukan";
        header('Location: index.php');
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM penjualan WHERE penjualan_id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $_SESSION['success'] = "Data penjualan berhasil dihapus";
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

header('Location: index.php');
exit();
?>