<?php 
session_start();
if (!isset($_SESSION['session_username']) || $_SESSION['session_role'] !== 'pegawai') {
    header('location: /login.php');
    exit();
}

require_once '../../conn.php';

// Ambil data pegawai
$username = $_SESSION['session_username'];
$stmt = $conn->prepare("SELECT * FROM pegawai WHERE username = :username");
$stmt->execute(['username' => $username]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    header('location: /logout.php');
    exit();
}

$idPegawai = $data['pegawai_id'];

// Hitung total penghasilan bulan lalu
$stmtPenghasilan = $conn->prepare("
    SELECT SUM(total_penjualan) AS total_bulan_lalu 
    FROM penjualan 
    WHERE 
        MONTH(tanggal_penjualan) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) 
        AND YEAR(tanggal_penjualan) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) 
        AND pegawai_id = :pegawai_id
");
$stmtPenghasilan->execute(['pegawai_id' => $idPegawai]);
$penghasilan = $stmtPenghasilan->fetch(PDO::FETCH_ASSOC);
$totalBulanLalu = $penghasilan['total_bulan_lalu'] ?? 0;

require_once '../../assets/header.php';
require_once '../../assets/navbar.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <img src="../../assets/images/<?php echo htmlspecialchars($data['foto']); ?>" alt="Foto Profil" class="mx-auto rounded-full w-48 h-48 object-cover border-4 border-blue-600 mb-4" />
        <h1 class="text-4xl font-semibold text-gray-800"><?php echo htmlspecialchars($data['nama']); ?></h1>
        <p class="text-gray-600 capitalize"><?php echo htmlspecialchars($_SESSION['session_role']); ?></p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 max-w-md mx-auto text-center">
        <i class="bi bi-cash-coin text-yellow-500 text-6xl mb-4"></i>
        <h2 class="text-xl font-semibold mb-2">Penghasilan Bulan Lalu</h2>
        <p class="text-3xl font-bold text-green-600">Rp <?php echo number_format($totalBulanLalu, 0, ',', '.'); ?></p>
    </div>
</div>
