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

    $stmt = $conn->prepare("SELECT bakso_halus, bakso_kasar, bakso_puyuh, tahu, somay FROM stok_pegawai WHERE pegawai_id = :pegawai_id LIMIT 1");
    $stmt->bindParam(':pegawai_id', $pegawai_id, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['labels' => [], 'data' => []]);
        exit();
    }

    $labels = ['Bakso Halus', 'Bakso Kasar', 'Bakso Puyuh', 'Tahu', 'Somay'];
    $data = [
        (int)$row['bakso_halus'],
        (int)$row['bakso_kasar'],
        (int)$row['bakso_puyuh'],
        (int)$row['tahu'],
        (int)$row['somay']
    ];

    echo json_encode([
        'labels' => $labels,
        'data' => $data
    ]);
}
catch (PDOException $e) {
    echo json_encode(['error' => "Failed to query database: " . $e->getMessage()]);
}
