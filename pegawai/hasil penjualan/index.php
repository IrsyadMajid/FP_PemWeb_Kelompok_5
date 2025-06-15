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

$data_penjualan = [];
try {
    $query = "SELECT * FROM penjualan WHERE pegawai_id = :pegawai_id ORDER BY tanggal_penjualan DESC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':pegawai_id', $pegawai_id, PDO::PARAM_INT);
    $stmt->execute();
    $data_penjualan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}

require_once '../../assets/header.php';
require_once '../../assets/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Penjualan</title>
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

        .add-btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .add-btn:hover {
            background-color: #218838;
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
            background-color: #eff6ff;
            color: #1e3a8a;
            font-weight: bold;
            text-align: left;
            padding: 12px 16px;
            border-bottom: 1px solid #cbd5e1;
            position: sticky;
            top: 0;
        }
        
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        tr:hover {
            background-color: #f0f4f8;
        }

        .action-links a {
            margin-right: 12px;
            text-decoration: none;
            font-weight: 500;
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        
        .edit-link {
            color: #1e40af;
            background-color: #dbeafe;
        }
        
        .edit-link:hover {
            background-color: #bfdbfe;
        }
        
        .delete-link {
            color: #991b1b;
            background-color: #fee2e2;
        }
        
        .delete-link:hover {
            background-color: #fecaca;
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
                        <i class="fas fa-receipt" style="color: #1e3a8a; margin-right: 10px;"></i>
                        Data Penjualan Saya
                    </h1>
                    <a href="tambah.php" class="add-btn">
                        <i class="fas fa-plus"></i> Tambah Penjualan
                    </a>
                </div>
                
                <?php if (empty($data_penjualan)): ?>
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
                                    <th>ID Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Total Penjualan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data_penjualan as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['penjualan_id']) ?></td>
                                    <td><?= date('d M Y', strtotime($row['tanggal_penjualan'])) ?></td>
                                    <td>Rp <?= number_format($row['total_penjualan'], 2, ',', '.') ?></td>
                                    <td class="action-links">
                                        <a href="edit.php?id=<?= $row['penjualan_id'] ?>" class="edit-link">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="hapus.php?id=<?= $row['penjualan_id'] ?>" 
                                           class="delete-link"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
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