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

$data_penjualan = [];
try {
    $query = "SELECT * FROM penjualan ORDER BY tanggal_penjualan DESC";
    $stmt = $conn->query($query);
    if ($stmt) {
        $data_penjualan = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
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
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: f3f4f6;
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

        .add-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 16px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .add-btn:hover {
            background-color: #218838;
        }

        table {
            width: 750px;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 16px;
            text-align: left;
        }

        th {
            background-color: #eff6ff;
            color: #1e3a8a;
            font-weight: bold;
            border-bottom: 1px solid #cbd5e1;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .action-links a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
            font-weight: 500;
        }

        .action-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <main>
            <h1>Data Penjualan</h1>
            <a href="tambah.php" class="add-btn">Tambah Penjualan</a>
            
            <?php if (empty($data_penjualan)): ?>
                <p>Tidak ada data penjualan ditemukan.</p>
            <?php else: ?>
                <table class="table-position" border="1" cellpadding="8" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ID Pegawai</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                             <th>Aksi</th>
                        </tr>
                     </thead>
                    <tbody>
                        <?php foreach ($data_penjualan as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['penjualan_id']) ?></td>
                            <td><?= htmlspecialchars($row['pegawai_id']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_penjualan']) ?></td>
                            <td><?= number_format($row['total_penjualan'], 2, ',', '.') ?></td>
                            <td class="action-links">
                                <a href="edit.php?id=<?= $row['penjualan_id'] ?>">Edit</a>
                                <a href="hapus.php?id=<?= $row['penjualan_id'] ?>" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>