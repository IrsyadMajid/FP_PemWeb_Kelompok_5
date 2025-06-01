<?php
include('conn.php'); // koneksi ke database

$status = '';
$result = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['stok_id'])) {
        $stok_id = $_GET['stok_id'];
        $query = "SELECT * FROM stok_pegawai WHERE stok_id = '$stok_id'";
        $result = mysqli_query(connection(), $query);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stok_id = $_POST['stok_id'];
    $pegawai_id = $_POST['pegawai_id'];
    $bakso_halus = $_POST['bakso_halus'];
    $bakso_kasar = $_POST['bakso_kasar'];
    $bakso_puyuh = $_POST['bakso_puyuh'];
    $tahu = $_POST['tahu'];
    $somay = $_POST['somay'];

    $sql = "UPDATE stok_pegawai 
            SET pegawai_id='$pegawai_id', 
                bakso_halus='$bakso_halus', 
                bakso_kasar='$bakso_kasar', 
                bakso_puyuh='$bakso_puyuh', 
                tahu='$tahu', 
                somay='$somay' 
            WHERE stok_id='$stok_id'";

    $result = mysqli_query(connection(), $sql);
    $status = $result ? 'ok' : 'err';
    header('Location: index.php?status=' . $status);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Stok Pegawai</title>
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
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-left: 70px;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
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
    <div class="navbar">Update Stok Pegawai</div>
    <div class="container">
        <h2 style="text-align: center;">Form Update Stok</h2>
        <form action="update_stok_pegawai.php" method="POST">
            <?php while ($data = mysqli_fetch_array($result)) : ?>
                <div class="form-group">
                    <label>ID Stok</label>
                    <input type="number" name="stok_id" value="<?= $data['stok_id']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label>ID Pegawai</label>
                    <input type="number" name="pegawai_id" value="<?= $data['pegawai_id']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Bakso Halus</label>
                    <input type="number" name="bakso_halus" value="<?= $data['bakso_halus']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Bakso Kasar</label>
                    <input type="number" name="bakso_kasar" value="<?= $data['bakso_kasar']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Bakso Puyuh</label>
                    <input type="number" name="bakso_puyuh" value="<?= $data['bakso_puyuh']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Tahu</label>
                    <input type="number" name="tahu" value="<?= $data['tahu']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Somay</label>
                    <input type="number" name="somay" value="<?= $data['somay']; ?>" required>
                </div>
            <?php endwhile; ?>
            <button type="submit">Simpan</button>
            <a href="index.php" class="view">Kembali</a>
        </form>
    </div>
</body>
</html>
