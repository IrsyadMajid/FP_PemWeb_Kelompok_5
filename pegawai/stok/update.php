<?php
session_start();
if (!isset($_SESSION['session_username'])) {
    header('Location: /login.php');
    exit();
}

require_once '../../conn.php';

$username = $_SESSION['session_username'];
$queryPegawai = $conn->query("SELECT * FROM pegawai WHERE username = '$username'");
$dataPegawai = $queryPegawai->fetch(PDO::FETCH_ASSOC);
$pegawaiId = $dataPegawai['pegawai_id'];

$queryStok = $conn->prepare("SELECT * FROM stok_pegawai WHERE pegawai_id = ?");
$queryStok->execute([$pegawaiId]);
$dataStok = $queryStok->fetch(PDO::FETCH_ASSOC);

$baksoHalus = $_POST['baksohalus'] ?? 0;
$baksoKasar = $_POST['baksokasar'] ?? 0;
$baksoPuyuh = $_POST['baksopuyuh'] ?? 0;
$tahu       = $_POST['tahu'] ?? 0;
$somay      = $_POST['somay'] ?? 0;

$newBaksoHalus = max(0, $dataStok['bakso_halus'] - $baksoHalus);
$newBaksoKasar = max(0, $dataStok['bakso_kasar'] - $baksoKasar);
$newBaksoPuyuh = max(0, $dataStok['bakso_puyuh'] - $baksoPuyuh);
$newTahu       = max(0, $dataStok['tahu'] - $tahu);
$newSomay      = max(0, $dataStok['somay'] - $somay);

$update = $conn->prepare("UPDATE stok_pegawai SET 
    bakso_halus = :halus, 
    bakso_kasar = :kasar, 
    bakso_puyuh = :puyuh, 
    tahu = :tahu, 
    somay = :somay 
    WHERE pegawai_id = :id");

$update->execute([
    ':halus' => $newBaksoHalus,
    ':kasar' => $newBaksoKasar,
    ':puyuh' => $newBaksoPuyuh,
    ':tahu' => $newTahu,
    ':somay' => $newSomay,
    ':id' => $pegawaiId
]);

header("Location: index.php?update=success");
exit();
