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

$pegawai_id = $_SESSION['pegawai_id'] ?? null;

$error = '';
$success = '';

$menu_items = [
    'bakso_halus' => 'Bakso Halus',
    'bakso_kasar' => 'Bakso Kasar',
    'bakso_puyuh' => 'Bakso Puyuh',
    'tahu' => 'Tahu',
    'siomay' => 'Siomay'
];

$harga_produk = [
    'bakso_halus' => 15000,
    'bakso_kasar' => 12000,
    'bakso_puyuh' => 10000,
    'tahu' => 8000,
    'siomay' => 9000
];

$penjualan_id = $_GET['id'] ?? null;
$penjualan_data = null;
$detail_penjualan = [];

if ($penjualan_id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM penjualan WHERE penjualan_id = :penjualan_id AND pegawai_id = :pegawai_id");
        $stmt->bindParam(':penjualan_id', $penjualan_id);
        $stmt->bindParam(':pegawai_id', $pegawai_id);
        $stmt->execute();
        $penjualan_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$penjualan_data) {
            die("Data penjualan tidak ditemukan atau tidak memiliki akses");
        }

        $stmt_stok = $conn->prepare("SELECT * FROM stok_pegawai WHERE pegawai_id = :pegawai_id");
        $stmt_stok->bindParam(':pegawai_id', $pegawai_id);
        $stmt_stok->execute();
        $stok_pegawai = $stmt_stok->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
} else {
    die("ID penjualan tidak valid");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $tanggal = $_POST['tanggal_penjualan'];

        $jumlah_penjualan = [];
        $total = 0;
        
        foreach ($menu_items as $key => $nama) {
            $jumlah = $_POST[$key] ?? 0;
            $jumlah_penjualan[$key] = $jumlah;
            $total += $jumlah * $harga_produk[$key];
        }

        if (empty($tanggal)) {
            throw new Exception("Tanggal penjualan harus diisi");
        }

        if ($total <= 0) {
            throw new Exception("Minimal harus ada 1 menu yang terjual");
        }

        $conn->beginTransaction();

        $stmt = $conn->prepare("UPDATE penjualan 
                               SET tanggal_penjualan = :tanggal, 
                                   total_penjualan = :total
                               WHERE penjualan_id = :penjualan_id AND pegawai_id = :pegawai_id");
        $stmt->bindParam(':tanggal', $tanggal);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':penjualan_id', $penjualan_id);
        $stmt->bindParam(':pegawai_id', $pegawai_id);
        $stmt->execute();

        $selisih = [];
        foreach ($menu_items as $key => $nama) {
            $selisih[$key] = $jumlah_penjualan[$key] - ($_POST['old_'.$key] ?? 0);
        }

        $stmt_stok = $conn->prepare("UPDATE stok_pegawai 
                                    SET bakso_halus = bakso_halus - :bakso_halus,
                                        bakso_kasar = bakso_kasar - :bakso_kasar,
                                        bakso_puyuh = bakso_puyuh - :bakso_puyuh,
                                        tahu = tahu - :tahu,
                                        somay = somay - :siomay
                                    WHERE pegawai_id = :pegawai_id");
        $stmt_stok->bindParam(':bakso_halus', $selisih['bakso_halus']);
        $stmt_stok->bindParam(':bakso_kasar', $selisih['bakso_kasar']);
        $stmt_stok->bindParam(':bakso_puyuh', $selisih['bakso_puyuh']);
        $stmt_stok->bindParam(':tahu', $selisih['tahu']);
        $stmt_stok->bindParam(':siomay', $selisih['siomay']);
        $stmt_stok->bindParam(':pegawai_id', $pegawai_id);
        $stmt_stok->execute();

        $conn->commit();

        $success = "Data penjualan berhasil diperbarui!";

        header("Location: edit.php?id=" . $penjualan_id);
        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
        $error = "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        $conn->rollBack();
        $error = $e->getMessage();
    }
}

require_once '../../assets/header.php';
require_once '../../assets/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Penjualan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        .main-container {
            display: flex;
            min-height: 100vh;
        }
        
        .content-wrapper {
            flex: 1;
            padding: 20px;
            margin-left: 250px;
            padding-top: 80px;
        }

        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            margin-left: 50px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #2d3748;
        }

        .form-container {
            max-width: 750px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
        }
        
        .form-control-static {
            display: block;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .menu-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .menu-name {
            font-weight: 500;
        }

        .menu-qty {
            width: 60px;
            padding: 5px;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .btn-primary {
            background-color: #4299e1;
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            background-color: #3182ce;
        }
        
        .btn-secondary {
            background-color: #e2e8f0;
            color: #4a5568;
            border: none;
            margin-left: 10px;
        }
        
        .btn-secondary:hover {
            background-color: #cbd5e0;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            color: #9b2c2c;
            background-color: #fed7d7;
            border: 1px solid #feb2b2;
        }
        
        .alert-success {
            color: #276749;
            background-color: #c6f6d5;
            border: 1px solid #9ae6b4;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="content-wrapper">
            <div class="card">
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-edit" style="color: #4299e1; margin-right: 10px;"></i>
                        Edit Data Penjualan
                    </h1>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-container">
                    <form method="POST">
                        <div class="form-group">
                            <label for="pegawai_id">ID Pegawai</label>
                            <div class="form-control-static">
                                <?= htmlspecialchars($pegawai_id) ?>
                            </div>
                            <input type="hidden" name="pegawai_id" value="<?= htmlspecialchars($pegawai_id) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="penjualan_id">ID Penjualan</label>
                            <div class="form-control-static">
                                <?= htmlspecialchars($penjualan_id) ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="tanggal_penjualan">Tanggal Penjualan</label>
                            <input type="date" id="tanggal_penjualan" name="tanggal_penjualan" 
                                   class="form-control" required
                                   value="<?= htmlspecialchars($penjualan_data['tanggal_penjualan'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label>Menu yang Terjual</label>
                            <div class="menu-list">
                                <?php foreach ($menu_items as $key => $nama): ?>
                                    <div class="menu-item">
                                        <span class="menu-name"><?= htmlspecialchars($nama) ?></span>
                                        <input type="number" name="<?= htmlspecialchars($key) ?>" min="0" 
                                               value="<?= htmlspecialchars($stok_pegawai[$key] ?? 0) ?>" 
                                               class="menu-qty" placeholder="0">
                                        <input type="hidden" name="old_<?= htmlspecialchars($key) ?>" 
                                               value="<?= htmlspecialchars($stok_pegawai[$key] ?? 0) ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="total_penjualan">Total Penjualan</label>
                            <div class="form-control-static">
                                Rp <?= number_format($penjualan_data['total_penjualan'] ?? 0, 0, ',', '.') ?>
                            </div>
                        </div>
                        
                        <div style="margin-top: 20px;">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="index.php" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const inputs = document.querySelectorAll('.menu-qty');
            let totalItems = 0;
            
            inputs.forEach(input => {
                totalItems += parseInt(input.value) || 0;
            });
            
            if (totalItems === 0) {
                e.preventDefault();
                alert('Minimal harus ada 1 menu yang terjual');
            }
        });
    </script>
</body>
</html>