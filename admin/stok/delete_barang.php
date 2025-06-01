<?php
include('conn.php');

// Ambil koneksi
$conn = connection();

$status = '';

// Mengecek apakah method GET dan parameter barang_id tersedia
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['barang_id'])) {
    // Ambil dan sanitasi ID
    $barang_id = intval($_GET['barang_id']);

    // Siapkan query dengan placeholder
    $stmt = $conn->prepare("DELETE FROM barang WHERE barang_id = ?");
    
    if ($stmt) {
        // Binding parameter ke statement
        $stmt->bind_param("i", $barang_id);

        // Eksekusi query
        if ($stmt->execute()) {
            $status = 'ok';
        } else {
            $status = 'err';
        }

        $stmt->close();
    } else {
        $status = 'err';
    }

    // Redirect ke halaman stok barang
    header("Location: index.php?status=" . $status);
    exit;
} else {
    // Redirect jika tidak ada barang_id
    header("Location:index.php?status=invalid");
    exit;
}
?>
