<?php
// memanggil file koneksi ke database
include('conn.php');

$status = '';
$result = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['barang_id'])) {
        $barang_id = $_GET['barang_id'];
        $query = "SELECT * FROM barang WHERE barang_id = '$barang_id'";
        $result = mysqli_query(connection(), $query);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barang_id = $_POST['barang_id'];
    $nama_barang = $_POST['nama_barang'];
    $stok = $_POST['stok'];
    $satuan = $_POST['satuan'];
    $harga = $_POST['harga'];

    $sql = "UPDATE barang 
            SET nama_barang='$nama_barang', stok='$stok', satuan='$satuan', harga='$harga' 
            WHERE barang_id='$barang_id'";
    $result = mysqli_query(connection(), $sql);
    $status = $result ? 'ok' : 'err';
    header('Location: index.php?status=' . $status);
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
            margin: 20px auto;
            max-width: 600px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .container h2 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            margin-left: 70px;
        }
        .form-group input,
        .form-group select {
            width: 75%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-left: 70px;
        }
        button {
            background-color: rgb(53, 142, 243);
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            margin-left: 275px;
        }
        button:hover {
            background-color: rgb(9, 98, 233);
        }
        .view {
        display: inline-block;
        margin-top: 15px;
        margin-left: 275px;
        text-decoration: none;
        background-color: #888;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
      }
    </style>
</head>
<body>
<div class="navbar">Update Barang</div>

<div class="container">
    <h2>Form Update Barang</h2>
    <form action="update_stok_rumah.php" method="POST">
        <?php while ($data = mysqli_fetch_array($result)): ?>
            <div class="form-group">
                <label>ID Barang</label>
                <input type="text" name="barang_id" value="<?php echo $data['barang_id']; ?>" readonly>
            </div>
            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" value="<?php echo $data['nama_barang']; ?>" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" value="<?php echo $data['stok']; ?>" required>
            </div>
            <div class="form-group">
                <label>Satuan</label>
                <input type="text" name="satuan" value="<?php echo $data['satuan']; ?>" required>
            </div>
            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" value="<?php echo $data['harga']; ?>" required>
            </div>
        <?php endwhile; ?>
        <button type="submit">Simpan</button>
        <a href="index.php" class="view">Kembali</a>
    </form>
</div>
</body>
</html>
