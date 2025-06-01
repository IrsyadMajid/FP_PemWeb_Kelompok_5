<?php
header('Content-Type: application/json');

$dbServer = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = "manajemen bakso";

try {
    $conn = new PDO("mysql:host=$dbServer;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pegawai_id = 1; // ganti sesuai session login

    $stmt = $conn->prepare("SELECT bulan, total FROM rekap_penjualan WHERE pegawai_id = :pegawai_id ORDER BY bulan ASC");
    $stmt->bindParam(':pegawai_id', $pegawai_id, PDO::PARAM_INT);
    $stmt->execute();

    $labels = [];
    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $labels[] = $row['bulan'];
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
