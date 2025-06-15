<?php
include '../../conn.php';
session_start();

if (!isset($_SESSION['session_username']) || $_SESSION['session_role'] != 'admin') {
    header('Location: /login.php');
    exit();
}

try {
    $queryBarang = "SELECT * FROM barang";
    $resultBarang = $conn->query($queryBarang);

    $queryPegawai = "SELECT s.*, p.nama AS nama_pegawai FROM stok_pegawai s JOIN pegawai p ON s.pegawai_id = p.pegawai_id";
    $resultPegawai = $conn->query($queryPegawai);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
require_once '../../assets/header-admin.php';
require_once '../../assets/navbar-admin.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="grid grid-cols-[200px_1fr] grid-rows-[auto_1fr] h-screen">
        <header class="col-span-2 bg-blue-600 text-white p-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">Bakso Kediri</h1>
            <img src="profile.png" alt="Profile Icon" class="w-10 h-10 rounded-full bg-blue-400">
        </header>
        <aside class="bg-blue-700 text-white p-4">
            <ul class="space-y-4">
                <li><a href="#" class="flex items-center gap-2 hover:text-blue-300"><span>Dashboard</span></a></li>
                <li><a href="#" class="flex items-center gap-2 hover:text-blue-300"><span>Stok</span></a></li>
                <li><a href="#" class="flex items-center gap-2 hover:text-blue-300"><span>Hasil Penjualan</span></a></li>
                <li><a href="#" class="flex items-center gap-2 hover:text-blue-300"><span>Rekap</span></a></li>
                <li><a href="#" class="flex items-center gap-2 hover:text-blue-300"><span>Pegawai</span></a></li>
            </ul>
        </aside>
        <main class="p-6 overflow-y-auto flex flex-col items-center">
            <div class="w-full max-w-5xl space-y-8">
                <div>
                    <h2 class="text-center text-2xl font-semibold mb-4">STOK RUMAH</h2>
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full bg-white shadow-md rounded-lg border">
                            <thead>
                                <tr class="bg-blue-500 text-white">
                                    <th class="px-4 py-2">No</th>
                                    <th class="px-4 py-2">Nama Barang</th>
                                    <th class="px-4 py-2">Stok</th>
                                    <th class="px-4 py-2">Satuan</th>
                                    <th class="px-4 py-2">Harga</th>
                                    <th class="px-4 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($resultBarang->rowCount() > 0) {
                                    $no = 1;
                                    foreach ($resultBarang as $row) {
                                        echo "<tr class='text-center border-t'>";
                                        echo "<td class='px-4 py-2'>" . $no++ . "</td>";
                                        echo "<td class='px-4 py-2'>" . htmlspecialchars($row['nama_barang']) . "</td>";
                                        echo "<td class='px-4 py-2'>" . $row['stok'] . "</td>";
                                        echo "<td class='px-4 py-2'>" . $row['satuan'] . "</td>";
                                        echo "<td class='px-4 py-2'>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                                        echo "<td class='px-4 py-2 space-x-2'>
                                                <a href='update_stok_rumah.php?barang_id=" . urlencode($row['barang_id']) . "' class='bg-green-500 text-white px-3 py-1 rounded'>Update</a>
                                                <a href='delete_barang.php?barang_id=" . urlencode($row['barang_id']) . "' onclick=\"return confirm('Yakin ingin menghapus barang ini?');\" class='bg-red-500 text-white px-3 py-1 rounded'>Delete</a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center py-4'>Tidak ada data</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-4">
                        <button onclick="location.href='form_barang.php'" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded">Input Stok Rumah</button>
                    </div>
                </div>

                <div>
                    <h2 class="text-center text-2xl font-semibold mb-4">STOK PEGAWAI</h2>
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full bg-white shadow-md rounded-lg border">
                            <thead>
                                <tr class="bg-blue-500 text-white">
                                    <th class="px-4 py-2">ID Stok</th>
                                    <th class="px-4 py-2">Nama Pegawai</th>
                                    <th class="px-4 py-2">Bakso Halus</th>
                                    <th class="px-4 py-2">Bakso Kasar</th>
                                    <th class="px-4 py-2">Bakso Puyuh</th>
                                    <th class="px-4 py-2">Tahu</th>
                                    <th class="px-4 py-2">Somay</th>
                                    <th class="px-4 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($resultPegawai->rowCount() > 0) {
                                    foreach ($resultPegawai as $row) {
                                        echo "<tr class='text-center border-t'>";
                                        echo "<td class='px-4 py-2'>{$row['stok_id']}</td>";
                                        echo "<td class='px-4 py-2'>{$row['nama_pegawai']}</td>";
                                        echo "<td class='px-4 py-2'>{$row['bakso_halus']}</td>";
                                        echo "<td class='px-4 py-2'>{$row['bakso_kasar']}</td>";
                                        echo "<td class='px-4 py-2'>{$row['bakso_puyuh']}</td>";
                                        echo "<td class='px-4 py-2'>{$row['tahu']}</td>";
                                        echo "<td class='px-4 py-2'>{$row['somay']}</td>";
                                        echo "<td class='px-4 py-2 space-x-2'>
                                                <a href='update_stok_pegawai.php?stok_id={$row['stok_id']}' class='bg-green-500 text-white px-3 py-1 rounded'>Update</a>
                                                <a href='delete_stok_pegawai.php?stok_id={$row['stok_id']}' onclick=\"return confirm('Yakin ingin menghapus data stok ini?');\" class='bg-red-500 text-white px-3 py-1 rounded'>Delete</a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center py-4'>Tidak ada data</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-4">
                        <button onclick="location.href='form_stok_pegawai.php'" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded">Input Stok Pegawai</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
