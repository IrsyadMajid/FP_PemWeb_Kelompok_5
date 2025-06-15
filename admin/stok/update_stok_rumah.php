<?php
include('../../conn.php'); // koneksi ke database

$status = '';
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['barang_id'])) {
    $barang_id = $_GET['barang_id'];
    $query = "SELECT * FROM barang WHERE barang_id = :barang_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':barang_id', $barang_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barang_id = $_POST['barang_id'];
    $nama_barang = $_POST['nama_barang']; 
    $stok = $_POST['stok'];
    $satuan = $_POST['satuan'];
    $harga = $_POST['harga'];

    $query = "UPDATE barang 
              SET nama_barang = :nama_barang, 
                  stok = :stok, 
                  satuan = :satuan, 
                  harga = :harga 
              WHERE barang_id = :barang_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':barang_id', $barang_id, PDO::PARAM_INT);
    $stmt->bindParam(':nama_barang', $nama_barang, PDO::PARAM_STR);
    $stmt->bindParam(':stok', $stok, PDO::PARAM_INT);
    $stmt->bindParam(':satuan', $satuan, PDO::PARAM_STR);
    $stmt->bindParam(':harga', $harga, PDO::PARAM_INT);

    try {
        $stmt->execute();
        $status = 'ok';
    } catch (PDOException $e) {
        $status = 'err';
    }
    header('Location: index.php?status=' . $status);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Data Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0; padding: 0;
            background-color: #f4f4f4;
        }
        .navbar {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-left: 70px;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 75%;
            padding: 10px;
            margin-left: 70px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            display: block;
            margin: 0 auto;
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .view {
            display: inline-block;
            margin-top: 15px;
            margin-left: 255px;
            text-decoration: none;
            background-color: #888;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<div class="navbar">Update Data Barang</div>
<div class="container">
    <h2 style="text-align: center;">Form Update Barang</h2>
    <?php if ($data): ?>
    <form action="update_stok_rumah.php" method="POST">
        <div class="form-group">
            <label>ID Barang</label>
            <input type="number" name="barang_id" value="<?= htmlspecialchars($data['barang_id']); ?>" readonly>
        </div>
        <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" value="<?= htmlspecialchars($data['nama_barang']); ?>" required>
        </div>
        <div class="form-group">
            <label>Stok</label>
            <input type="number" name="stok" value="<?= htmlspecialchars($data['stok']); ?>" required>
        </div>
        <div class="form-group">
            <label>Satuan</label>
            <input type="text" name="satuan" value="<?= htmlspecialchars($data['satuan']); ?>" required>
        </div>
        <div class="form-group">
            <label>Harga</label>
            <input type="number" name="harga" value="<?= htmlspecialchars($data['harga']); ?>" required>
        </div>
        <button type="submit">Simpan</button>
        <a href="index.php" class="view">Kembali</a>
    </form>
    <?php else: ?>
        <p>Data barang tidak ditemukan.</p>
    <?php endif; ?>
</div>
</body>
</html>
