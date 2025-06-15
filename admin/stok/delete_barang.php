<?php
include('../../conn.php'); // Koneksi ke database

$status = '';

try {
    // Mengecek apakah method GET dan parameter barang_id tersedia
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['barang_id'])) {
        // Ambil dan sanitasi ID
        $barang_id = intval($_GET['barang_id']);

        // Siapkan query dengan placeholder
        $stmt = $conn->prepare("DELETE FROM barang WHERE barang_id = :barang_id");

        // Eksekusi query
        $stmt->execute([':barang_id' => $barang_id]);

        // Periksa apakah ada baris yang dihapus
        if ($stmt->rowCount() > 0) {
            $status = 'ok';
        } else {
            $status = 'not_found';
        }
    } else {
        $status = 'invalid';
    }
} catch (Exception $e) {
    $status = 'err';
}

// Redirect ke halaman stok barang
header("Location: index.php?status=" . $status);
exit;
?>
