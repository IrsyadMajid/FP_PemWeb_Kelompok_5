<?php 
session_start();
require_once '../../assets/header-admin.php';
if(!isset($_SESSION['session_username'])){
    header('location: /login.php');
    exit();
}

$username = $_SESSION['session_username'];
$query = $conn->query("SELECT * FROM admin WHERE username = '$username'");
$admin_data = $query->fetch(PDO::FETCH_ASSOC);

$pegawai_query = $conn->query("SELECT * FROM pegawai ORDER BY nama ASC");
$pegawai_list = $pegawai_query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="lg:ml-[300px] pt-[100px] px-4">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($pegawai_list as $pegawai): ?>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $pegawai['pegawai_id']; ?></td>
              <td class="px-6 py-4 whitespace-nowrap">
                <img src="../../assets/images/<?php echo $pegawai['foto']; ?>" alt="Foto" class="w-10 h-10 rounded-full object-cover">
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($pegawai['nama']); ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($pegawai['username']); ?></td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $pegawai['status_aktif'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                  <?php echo $pegawai['status_aktif'] ? 'Aktif' : 'Nonaktif'; ?>
                </span>
              </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <?php if (date('d') == '25'): ?>
                    <a href="bayar_komisi.php?pegawai_id=<?= $pegawai['pegawai_id'] ?>">
                    <input type="submit" name="login" value="Bayar Komisi" class="bg-green-600 text-white py-2 px-4 rounded-2xl hover:bg-blue-700 transition-colors duration-200 cursor-pointer"/>
                    </a>
                <?php else: ?>
                    <span class="text-sm text-gray-400 italic">Pemberian Komisi Setiap Tanggal 25</span>
                <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>

<?php require_once '../../assets/navbar-admin.php'; ?>