<?php
session_start();

if (!isset($_SESSION['session_username']) || $_SESSION['session_role'] != 'admin') {
    header('Location: /login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$pegawai_id = (int)$_GET['id'];

$path_conn = realpath(__DIR__ . '/../../conn.php');
if (!$path_conn || !file_exists($path_conn)) {
    die("Database configuration file not found");
}
require_once $path_conn;

$pegawai = [];
$total_penjualan = 0;
$transaksi = [];

try {
    $stmt = $conn->prepare("SELECT pegawai_id, nama FROM pegawai WHERE pegawai_id = ? AND status_aktif = TRUE");
    $stmt->execute([$pegawai_id]);
    $pegawai = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pegawai) {
        header('Location: index.php');
        exit();
    }

    $stmt = $conn->prepare("SELECT 
                            penjualan_id, 
                            tanggal_penjualan, 
                            total_penjualan
                          FROM penjualan 
                          WHERE pegawai_id = ?
                          ORDER BY tanggal_penjualan DESC");
    $stmt->execute([$pegawai_id]);
    $transaksi = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_penjualan = array_sum(array_column($transaksi, 'total_penjualan'));
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}

require_once '../../assets/header-admin.php';
require_once '../../assets/navbar-admin.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Penjualan Pegawai</title>
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
        
        .total-badge {
            background-color: #ebf8ff;
            color: #3182ce;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }

        .pegawai-info {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .pegawai-name {
            font-size: 18px;
            font-weight: 600;
        }
        
        .pegawai-id {
            color: #718096;
            font-size: 14px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #718096;
        }
        
        .empty-icon {
            font-size: 48px;
            color: #cbd5e0;
            margin-bottom: 16px;
        }
        
        .table-container {
            overflow-y: auto;
            margin-top: 20px;
        }
        
        .transaction-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .transaction-table th {
            background-color: #edf2f7;
            padding: 12px 15px;
            text-align: left;
        }
        
        .transaction-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .transaction-table tr:hover {
            background-color: #f8fafc;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            padding: 10px 16px;
            background-color: #4299e1;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(66, 153, 225, 0.2);
            border: 1px solid #3182ce;
        }

        .back-btn:hover {
            background-color: #3182ce;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(66, 153, 225, 0.3);
        }

        .back-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 3px rgba(66, 153, 225, 0.2);
        }

        .back-btn i {
            font-size: 14px;
        }
        
        .subtotal-row {
            background-color: #f0fff4;
            font-weight: 600;
        }
        
        .subtotal-row td {
            padding: 15px;
            border-top: 2px solid #48bb78;
            border-bottom: none;
        }
        
        .subtotal-label {
            color: #2f855a;
        }
        
        .subtotal-value {
            color: #2f855a;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="main-container">     
        <div class="content-wrapper">
            <div class="card">
                <a href="index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Kembali ke Rekap
                </a>
                
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-user-tie" style="color: #4299e1; margin-right: 10px;"></i>
                        Detail Penjualan: <?= htmlspecialchars($pegawai['nama']) ?>
                    </h1>
                    <div class="total-badge">
                        Total: Rp <?= number_format((float)$total_penjualan, 0, ',', '.') ?>
                    </div>
                </div>
                
                <div class="pegawai-info">
                    <div class="pegawai-name"><?= htmlspecialchars($pegawai['nama']) ?></div>
                    <div class="pegawai-id">ID Pegawai: <?= htmlspecialchars($pegawai['pegawai_id']) ?></div>
                </div>
                
                <?php if (empty($transaksi)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <p>Pegawai ini belum memiliki transaksi penjualan</p>
                    </div>
                <?php else: ?>
                    <h3 style="margin: 25px 0 15px; color: #2d3748;">
                        <i class="fas fa-history" style="color: #4299e1; margin-right: 8px;"></i>
                        Daftar Transaksi
                    </h3>
                    <div class="table-container">
                        <table class="transaction-table">
                            <thead>
                                <tr>
                                    <th>ID Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transaksi as $trx): ?>
                                <tr>
                                    <td><?= htmlspecialchars($trx['penjualan_id']) ?></td>
                                    <td><?= date('d M Y', strtotime($trx['tanggal_penjualan'])) ?></td>
                                    <td>Rp <?= number_format((float)$trx['total_penjualan'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="subtotal-row">
                                    <td colspan="2" class="subtotal-label">Subtotal Penjualan</td>
                                    <td class="subtotal-value">Rp <?= number_format((float)$total_penjualan, 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>