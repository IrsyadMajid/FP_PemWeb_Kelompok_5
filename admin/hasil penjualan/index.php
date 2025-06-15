<?php
session_start();

if (!isset($_SESSION['session_username']) || $_SESSION['session_role'] != 'admin') {
    header('Location: /login.php');
    exit();
}

$path_conn = realpath(__DIR__ . '/../../conn.php');
if (!$path_conn || !file_exists($path_conn)) {
    die("Database configuration file not found");
}
require_once $path_conn;

$data_rekap = [];
$total_keseluruhan = 0;

try {
    $query = "SELECT 
                p.pegawai_id, 
                pg.nama as nama_pegawai,
                COUNT(p.penjualan_id) as jumlah_transaksi,
                COALESCE(SUM(p.total_penjualan), 0) as total_penjualan,
                MAX(p.tanggal_penjualan) as terakhir_transaksi
              FROM penjualan p
              JOIN pegawai pg ON p.pegawai_id = pg.pegawai_id
              WHERE pg.status_aktif = TRUE
              GROUP BY p.pegawai_id, pg.nama
              ORDER BY total_penjualan DESC";
    
    $stmt = $conn->query($query);
    if ($stmt) {
        $data_rekap = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    $stmt = $conn->query("SELECT COALESCE(SUM(total_penjualan), 0) FROM penjualan");
    $total_keseluruhan = $stmt->fetchColumn();
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
    <title>Rekap Penjualan Pegawai</title>
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

        .table-container {
            max-height: calc(100vh - 250px);
            overflow-y: auto;
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }
        
        th {
            background-color: #edf2f7;
            color: #4a5568;
            font-weight: 600;
            text-align: left;
            padding: 12px 16px;
            position: sticky;
            top: 0;
        }
        
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        tr:hover {
            background-color: #f7fafc;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-success {
            background-color: #f0fff4;
            color: #38a169;
        }
        
        .badge-warning {
            background-color: #fffaf0;
            color: #dd6b20;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        
        .btn-detail {
            background-color: #ebf8ff;
            color: #3182ce;
            border: 1px solid #bee3f8;
        }
        
        .btn-detail:hover {
            background-color: #bee3f8;
            transform: translateY(-1px);
        }
        
        .btn-action i {
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .btn-action span {
                display: none;
            }
            
            .btn-action i {
                margin-right: 0;
            }
            
            .btn-action {
                padding: 8px;
                width: 32px;
                height: 32px;
                border-radius: 50%;
            }
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
    </style>
</head>
<body>
    <div class="main-container">     
        <div class="content-wrapper">
            <div class="card">
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-chart-line" style="color: #4299e1; margin-right: 10px;"></i>
                        Rekap Penjualan Pegawai
                    </h1>
                    <div class="total-badge">
                        Total Keseluruhan: Rp <?= number_format((float)$total_keseluruhan, 0, ',', '.') ?>
                    </div>
                </div>
                
                <?php if (empty($data_rekap)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <p>Belum ada data penjualan</p>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID Pegawai</th>
                                    <th>Nama Pegawai</th>
                                    <th>Jumlah Transaksi</th>
                                    <th>Total Penjualan</th>
                                    <th>Terakhir Transaksi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data_rekap as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['pegawai_id']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_pegawai']) ?></td>
                                    <td>
                                        <span class="badge <?= $row['jumlah_transaksi'] > 10 ? 'badge-success' : 'badge-warning' ?>">
                                            <?= htmlspecialchars($row['jumlah_transaksi']) ?> transaksi
                                        </span>
                                    </td>
                                    <td>Rp <?= number_format((float)$row['total_penjualan'], 0, ',', '.') ?></td>
                                    <td><?= date('d M Y', strtotime($row['terakhir_transaksi'])) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="detail.php?id=<?= $row['pegawai_id'] ?>" class="btn-action btn-detail" title="Detail Penjualan">
                                                <i class="fas fa-eye"></i>
                                                <span>Detail</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>