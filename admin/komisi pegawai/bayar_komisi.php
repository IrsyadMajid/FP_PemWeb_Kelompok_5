<?php 
session_start();
require_once '../../conn.php';

if (!isset($_SESSION['session_username'])) {
    header('location: /login.php');
    exit();
}

if (!isset($_GET['pegawai_id'])) {
    echo "Pegawai tidak ditemukan.";
    exit();
}

$pegawai_id = $_GET['pegawai_id'];

$stmt = $conn->prepare("SELECT * FROM pegawai WHERE pegawai_id = ?");
$stmt->execute([$pegawai_id]);
$pegawai = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pegawai) {
    echo "Data pegawai tidak ditemukan.";
    exit();
}

$bulan_ini = date('Y-m');

$stmt = $conn->prepare("SELECT SUM(total_penjualan) AS total FROM penjualan WHERE pegawai_id = ? AND DATE_FORMAT(tanggal_penjualan, '%Y-%m') = ?");
$stmt->execute([$pegawai_id, $bulan_ini]);
$penjualan = $stmt->fetch(PDO::FETCH_ASSOC);

$total_penjualan = $penjualan['total'] ?? 0;
$komisi = $total_penjualan * 0.2;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bayar Komisi</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow-md">
    <h2 class="text-xl font-bold mb-4 text-blue-800">Detail Komisi Bulan <?= date('F Y') ?></h2>

    <div class="space-y-4 text-gray-800">
      <div><strong>Nama Pegawai:</strong> <?= htmlspecialchars($pegawai['nama']) ?></div>
      <div><strong>Username:</strong> <?= htmlspecialchars($pegawai['username']) ?></div>
      <div><strong>Total Penjualan:</strong> Rp <?= number_format($total_penjualan, 0, ',', '.') ?></div>
      <div><strong>Komisi (20%):</strong> <span class="text-green-600 font-semibold">Rp <?= number_format($komisi, 0, ',', '.') ?></span></div>
    </div>

    <div class="mt-6">
      <a href="index.php" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400 transition">Kembali</a>
      <a href="index.php" class="bg-green-300 px-4 py-2 rounded hover:bg-green-400 transition">Bayar</a>
    </div>
  </div>
</body>
</html>
