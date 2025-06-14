<?php
include 'conn.php';

$conn = connection(); // Panggil koneksi dari conn.php
$status = '';

try {
    // Periksa apakah parameter stok_id tersedia
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['stok_id'])) {
        $stok_id = intval($_GET['stok_id']); // Sanitasi input

        // Siapkan query hapus dengan prepared statement
        $stmt = $conn->prepare("DELETE FROM stok_pegawai WHERE stok_id = :stok_id");

        // Eksekusi query dengan parameter
        $stmt->execute([':stok_id' => $stok_id]);

        // Periksa apakah baris terhapus
        if ($stmt->rowCount() > 0) {
            $status = 'ok';
        } else {
            $status = 'not_found';
        }
    } else {
        $status = 'invalid';
    }
} catch (Exception $e) {
    // Tangani kesalahan dengan mencatat log (opsional)
    error_log($e->getMessage());
    $status = 'err';
}

// Tutup koneksi dan redirect
$conn = null; // PDO otomatis menutup koneksi
header("Location: index.php?status=$status");
exit;
?>
