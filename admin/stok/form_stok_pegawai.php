<?php 
include('conn.php'); 

$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pegawai_id = $_POST['pegawai_id'];
    $bakso_halus = $_POST['bakso_halus'];
    $bakso_kasar = $_POST['bakso_kasar'];
    $bakso_puyuh = $_POST['bakso_puyuh'];
    $tahu = $_POST['tahu'];
    $somay = $_POST['somay'];

    try {
        // Query SQL dengan prepared statement
        $query = "INSERT INTO stok_pegawai (pegawai_id, bakso_halus, bakso_kasar, bakso_puyuh, tahu, somay)
                  VALUES (:pegawai_id, :bakso_halus, :bakso_kasar, :bakso_puyuh, :tahu, :somay)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':pegawai_id', $pegawai_id, PDO::PARAM_INT);
        $stmt->bindParam(':bakso_halus', $bakso_halus, PDO::PARAM_INT);
        $stmt->bindParam(':bakso_kasar', $bakso_kasar, PDO::PARAM_INT);
        $stmt->bindParam(':bakso_puyuh', $bakso_puyuh, PDO::PARAM_INT);
        $stmt->bindParam(':tahu', $tahu, PDO::PARAM_INT);
        $stmt->bindParam(':somay', $somay, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $status = 'ok';
        } else {
            $status = 'err';
        }
    } catch (PDOException $e) {
        $status = 'err';
    }
}

// Ambil daftar pegawai untuk dropdown
$pegawaiList = $conn->query("SELECT pegawai_id, nama FROM pegawai");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Form Stok Pegawai</title>
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
      .form-group input, .form-group select {
          width: 75%;
          padding: 10px;
          border: 1px solid #ccc;
          border-radius: 4px;
          box-sizing: border-box;
          margin-left: 70px;
      }
      button {
          background-color: rgb(65, 134, 238);
          color: white;
          border: none;
          padding: 10px 20px;
          cursor: pointer;
          border-radius: 4px;
          margin-left: 275px;
      }
      button:hover {
          background-color: rgb(13, 102, 236);
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
          margin-left: 273px;
          text-decoration: none;
          background-color: #888;
          color: white;
          padding: 8px 16px;
          border-radius: 4px;
      }
  </style>
</head>
<body>
  <div class="navbar">Input Stok Pegawai</div>

  <div class="container">
    <?php 
        if ($status == 'ok') {
            echo '<div class="alert alert-success">Data stok berhasil disimpan</div>';
        } elseif ($status == 'err') {
            echo '<div class="alert alert-danger">Data stok gagal disimpan</div>';
        }
    ?>

    <h2>Form Stok Pegawai</h2>
    <form action="form_stok_pegawai.php" method="POST">
      <div class="form-group">
        <label>Nama Pegawai</label>
        <select name="pegawai_id" required>
          <option value="">-- Pilih Pegawai --</option>
          <?php while($row = $pegawaiList->fetch(PDO::FETCH_ASSOC)): ?>
            <option value="<?= $row['pegawai_id'] ?>"><?= htmlspecialchars($row['nama']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Bakso Halus</label>
        <input type="number" name="bakso_halus" required>
      </div>
      <div class="form-group">
        <label>Bakso Kasar</label>
        <input type="number" name="bakso_kasar" required>
      </div>
      <div class="form-group">
        <label>Bakso Puyuh</label>
        <input type="number" name="bakso_puyuh" required>
      </div>
      <div class="form-group">
        <label>Tahu</label>
        <input type="number" name="tahu" required>
      </div>
      <div class="form-group">
        <label>Somay</label>
        <input type="number" name="somay" required>
      </div>
      <button type="submit">Simpan</button>
      <a href="index.php" class="view">Kembali</a>
    </form>
  </div>
</body>
</html>
