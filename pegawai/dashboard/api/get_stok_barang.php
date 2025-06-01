<?php
header('Content-Type: application/json');

$dbServer = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = "manajemen bakso";

try {
    $conn = new PDO("mysql:host=$dbServer;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT nama_barang, stok FROM stok_barang ORDER BY nama_barang ASC");
    $stmt->execute();

    $labels = [];
    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $labels[] = $row['nama_barang'];
        $data[] = (int)$row['stok'];
    }

    echo json_encode([
        'labels' => $labels,
        'data' => $data
    ]);
}
catch (PDOException $e) {
    echo json_encode(['error' => "Failed to query database: " . $e->getMessage()]);
}
