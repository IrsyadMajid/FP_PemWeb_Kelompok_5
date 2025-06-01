<?php
include 'conn.php';

$conn = connection(); // Panggil koneksi dari db.php
$status = '';

// Periksa apakah parameter stok_id tersedia
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['stok_id'])) {
    $stok_id = intval($_GET['stok_id']); // Sanitasi input

    // Siapkan query hapus menggunakan prepared statement
    $stmt = $conn->prepare("DELETE FROM stok_pegawai WHERE stok_id = ?");
    $stmt->bind_param("i", $stok_id);

    // Eksekusi query
    if ($stmt->execute()) {
        $status = 'ok';
    } else {
        $status = 'err';
    }

    $stmt->close();
    $conn->close();

    // Redirect kembali ke halaman stok
    header("Location: index.php?status=$status");
    exit;
} else {
    // Jika tidak ada stok_id, kembalikan ke halaman utama
    header("Location: index.php?status=invalid");
    exit;
}
?>
