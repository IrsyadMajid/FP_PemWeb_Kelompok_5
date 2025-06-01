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

$error = '';
$success = '';
$penjualan = null;

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit();
}

try {
    $stmt = $conn->prepare("SELECT * FROM penjualan WHERE penjualan_id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $penjualan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$penjualan) {
        throw new Exception("Data penjualan tidak ditemukan");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pegawai_id = $_POST['pegawai_id'];
        $tanggal = $_POST['tanggal_penjualan'];
        $total = $_POST['total_penjualan'];

        if (empty($pegawai_id) || empty($tanggal) || empty($total)) {
            throw new Exception("Semua field harus diisi");
        }

        if (!is_numeric($total)) {
            throw new Exception("Total harus berupa angka");
        }

        $stmt = $conn->prepare("UPDATE penjualan SET 
                               pegawai_id = :pegawai_id, 
                               tanggal_penjualan = :tanggal, 
                               total_penjualan = :total 
                               WHERE penjualan_id = :id");
        $stmt->bindParam(':pegawai_id', $pegawai_id);
        $stmt->bindParam(':tanggal', $tanggal);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $success = "Data penjualan berhasil diperbarui!";
        $penjualan = $conn->query("SELECT * FROM penjualan WHERE penjualan_id = $id")->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $error = $e->getMessage();
}

require_once '../../assets/header-admin.php';
require_once '../../assets/navbar-admin.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f3f4f6;
        }

        .container {
            padding-top: 120px;
            padding-left: 120px;
            display: grid;
            place-items: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #1e3a8a;
        }

        .form-container {
            width: 750px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #1e3a8a;
            font-weight: 500;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 16px;
        }

        input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        button[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        .back-btn {
            display: inline-block;
            margin-left: 10px;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 16px;
        }

        .back-btn:hover {
            background-color: #5a6268;
        }

        .error {
            color: #dc3545;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .success {
            color: #28a745;
            background-color: #d4edda;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <main>
            <h1>Edit Data Penjualan</h1>
            
            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <?php if ($penjualan): ?>
            <div class="form-container">
                <form method="POST">
                    <div class="form-group">
                        <label for="pegawai_id">ID Pegawai</label>
                        <input type="text" id="pegawai_id" name="pegawai_id" 
                            value="<?= htmlspecialchars($penjualan['pegawai_id']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="tanggal_penjualan">Tanggal Penjualan</label>
                        <input type="date" id="tanggal_penjualan" name="tanggal_penjualan" 
                            value="<?= htmlspecialchars($penjualan['tanggal_penjualan']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="total_penjualan">Total Penjualan</label>
                        <input type="number" id="total_penjualan" name="total_penjualan" step="0.01" 
                            value="<?= htmlspecialchars($penjualan['total_penjualan']) ?>" required>
                    </div>
                    
                    <button type="submit">Update</button>
                    <a href="index.php" style="margin-left: 10px;">Kembali</a>
                </form>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>