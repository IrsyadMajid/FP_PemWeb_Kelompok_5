<?php 
session_start();
if(!isset($_SESSION['session_username'])){
    header('location: /login.php');
    exit();
}
// print_r($_SESSION);
// print_r($_COOKIE);

require_once '../../conn.php';
$username = $_SESSION['session_username'];
$query = $conn->query("SELECT * FROM admin WHERE username = '$username'");
$data = $query->fetch(PDO::FETCH_ASSOC);

$penghasilanQuery = $conn->query("SELECT SUM(total_penjualan) AS total_bulan_lalu FROM penjualan WHERE MONTH(tanggal_penjualan) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND YEAR(tanggal_penjualan) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)");
$penghasilan = $penghasilanQuery->fetch(PDO::FETCH_ASSOC);
$totalBulanLalu = $penghasilan['total_bulan_lalu'] ?? 0;
?>

<?php require_once '../../assets/header-admin.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body class="bg-white min-h-screen">

  <div class="lg:pl-[300px] pt-[80px] flex flex-col px-2 justify-center items-center min-h-screen lg:gap-12 gap-2">
    <div class="text-center">
      <img src="../../assets/images/<?php echo $data['foto']; ?>"  alt="Foto Profil"  class="lg:w-96 lg:h-96 w-48 h-48 rounded-full object-cover mx-auto mb-4 border-4 border-blue-600">
      <p class="text-4xl font-semibold text-gray-800"><?php echo $data['username']; ?></p>
      <p class="text-gray-600"><?php echo $_SESSION['session_role']; ?></p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md text-center mb-4">
        <i class="bi bi-cash-coin text-6xl text-yellow-500 mb-2"></i>
        <p class="text-xl font-semibold text-gray-800">Penghasilan Bulan Lalu</p>
        <p class="text-2xl text-green-600 font-bold">Rp <?php echo number_format($totalBulanLalu, 0, ',', '.'); ?></p>
    </div>
  </div>
  </div>

</body>
</html>

<?php require_once '../../assets/navbar.php'; ?>