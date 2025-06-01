<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['pegawai_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$dbServer = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = "manajemen bakso";

try {
    $conn = new PDO("mysql:host=$dbServer;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pegawai_id = $_SESSION['pegawai_id'];

    $stmt = $conn->prepare("
        SELECT DATE(tanggal_laporan) AS tanggal, SUM(total_penjualan) AS total
        FROM laporan
        WHERE pegawai_id = :pegawai_id
        GROUP BY tanggal
        ORDER BY tanggal ASC
        LIMIT 30
    ");
    $stmt->bindParam(':pegawai_id', $pegawai_id, PDO::PARAM_INT);
    $stmt->execute();

    $labels = [];
    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $labels[] = $row['tanggal'];
        $data[] = (int)$row['total'];
    }

    echo json_encode([
        'labels' => $labels,
        'data' => $data
    ]);
}
catch (PDOException $e) {
    echo json_encode(['error' => "Failed to query database: " . $e->getMessage()]);
}
