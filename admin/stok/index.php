<?php
include 'conn.php'; // Menghubungkan ke file koneksi

try {
    // Query untuk tabel barang
    $queryBarang = "SELECT * FROM barang";
    $resultBarang = $conn->query($queryBarang);
    
    // Query untuk tabel stok pegawai
    $queryPegawai = "SELECT s.*, p.nama AS nama_pegawai 
                     FROM stok_pegawai s 
                     JOIN pegawai p ON s.pegawai_id = p.pegawai_id";
    $resultPegawai = $conn->query($queryPegawai);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="header" style="background-color: #414eff; color: white;">
            <h1>Bakso Kediri</h1>
            <img src="profile.png" alt="Profile Icon" class="profile-icon">
        </header>
        <aside class="sidebar" style="background-color: rgb(85, 96, 250); color: white;">
            <ul>
                <li><a href="">Dashboard</a></li>
                <li><a href="">Stok</a></li>
                <li><a href="">Hasil Penjualan</a></li>
                <li><a href="">Rekap</a></li>
                <li><a href="">Pegawai</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h2>STOK RUMAH</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultBarang->rowCount() > 0) {
                        $no = 1;
                        foreach ($resultBarang as $row) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_barang']) . "</td>";
                            echo "<td>" . $row['stok'] . "</td>";
                            echo "<td>" . $row['satuan'] . "</td>";
                            echo "<td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                            echo "<td>
                                    <a href='update_stok_rumah.php?barang_id=" . urlencode($row['barang_id']) . "' 
                                       style='padding:6px 12px; background-color:#4CAF50; color:white; border-radius:4px; text-decoration:none;'>Update</a>
                                    <a href='delete_barang.php?barang_id=" . urlencode($row['barang_id']) . "' 
                                       onclick=\"return confirm('Yakin ingin menghapus barang ini?');\" 
                                       style='padding:6px 12px; background-color:#f44336; color:white; border-radius:4px; text-decoration:none; margin-left:5px;'>Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>Tidak ada data</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <button onclick="location.href='form_barang.php'">Input Stok Rumah</button>
            <h2>STOK PEGAWAI</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Stok</th>
                        <th>Nama Pegawai</th>
                        <th>Bakso Halus</th>
                        <th>Bakso Kasar</th>
                        <th>Bakso Puyuh</th>
                        <th>Tahu</th>
                        <th>Somay</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultPegawai->rowCount() > 0) {
                        foreach ($resultPegawai as $row) {
                            echo "<tr>
                                    <td>{$row['stok_id']}</td>
                                    <td>{$row['nama_pegawai']}</td>
                                    <td>{$row['bakso_halus']}</td>
                                    <td>{$row['bakso_kasar']}</td>
                                    <td>{$row['bakso_puyuh']}</td>
                                    <td>{$row['tahu']}</td>
                                    <td>{$row['somay']}</td>
                                    <td>
                                        <a href='update_stok_pegawai.php?stok_id={$row['stok_id']}' 
                                           style='padding:6px 12px; background-color:#4CAF50; color:white; border-radius:4px; text-decoration:none;'>Update</a>
                                        <a href='delete_stok_pegawai.php?stok_id={$row['stok_id']}' 
                                           onclick=\"return confirm('Yakin ingin menghapus data stok ini?');\" 
                                           style='padding:6px 12px; background-color:#f44336; color:white; border-radius:4px; text-decoration:none; margin-left:5px;'>Delete</a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>Tidak ada data</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <button onclick="location.href='form_stok_pegawai.php'">Input Stok Pegawai</button>
        </main>
    </div>
</body>
</html>
