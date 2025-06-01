<?php 
  include('db.php'); 

  $status = '';
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $barang_id = $_POST['barang_id'];
      $nama_barang = $_POST['nama_barang'];
      $stok = $_POST['stok'];
      $satuan = $_POST['satuan'];
      $harga = $_POST['harga'];

      $query = "INSERT INTO barang (barang_id, nama_barang, stok, satuan, harga)
                VALUES ('$barang_id', '$nama_barang', '$stok', '$satuan', '$harga')";

      $result = mysqli_query(connection(), $query);

      if ($result) {
        $status = 'ok';
      } else {
        $status = 'err';
      }
  }
?>

<!DOCTYPE html>
<html>
<head>
  <title>Form Tambah Barang</title>
  <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
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
      .container h2{
        text-align:center;
      }
      .form-group {
        margin-bottom: 15px;
      }
      .form-group label {
        display: block;
        margin-bottom: 5px;
        margin-left:70px;
      }
      .form-group input {
        width: 75%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        margin-left: 70px;
      }
      button {
        background-color:rgb(65, 134, 238);
        color: white;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 4px;
        margin-left:275px;
      }
      button:hover {
        background-color:rgb(13, 102, 236);
      }
      .alert {
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid transparent;
        border-radius: 4px;
      }
      .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
      }
      .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
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
  <div class="navbar">Form Tambah Barang</div>

  <div class="container">
    <?php 
        if ($status == 'ok') {
          echo '<div class="alert alert-success">Data barang berhasil disimpan</div>';
        } elseif ($status == 'err') {
          echo '<div class="alert alert-danger">Data barang gagal disimpan</div>';
        }
     ?>

    <h2>Input Data Barang</h2>
    <form action="form_barang.php" method="POST">
      <div class="form-group">
        <label>ID Barang</label>
        <input type="number" name="barang_id" required placeholder="Masukkan ID Barang">
      </div>
      <div class="form-group">
        <label>Nama Barang</label>
        <input type="text" name="nama_barang" required placeholder="Masukkan Nama Barang">
      </div>
      <div class="form-group">
        <label>Stok</label>
        <input type="number" name="stok" required placeholder="Jumlah Stok">
      </div>
      <div class="form-group">
        <label>Satuan</label>
        <input type="text" name="satuan" required placeholder="Contoh: pcs, bungkus">
      </div>
      <div class="form-group">
        <label>Harga</label>
        <input type="number" name="harga" required placeholder="Harga per satuan">
      </div>
      <button type="submit">Simpan</button>
      <a href="index.php" class="view">Kembali</a>
    </form>
  </div>
</body>
</html>
