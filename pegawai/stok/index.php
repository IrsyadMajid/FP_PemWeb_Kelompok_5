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
$queryPegawai = $conn->query("SELECT * FROM pegawai WHERE username = '$username'");
$dataPegawai = $queryPegawai->fetch(PDO::FETCH_ASSOC);
$idPenjual = $dataPegawai['pegawai_id'];
$queryStok = $conn->query("SELECT * FROM stok_pegawai WHERE pegawai_id = '$idPenjual'");
$dataStok = $queryStok->fetch(PDO::FETCH_ASSOC);
?>

<?php require_once '../../assets/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body class="bg-white min-h-screen">

  <div class="lg:pl-[300px] pt-[80px] flex flex-col px-2 justify-center items-center min-h-screen lg:gap-12 gap-2 mb-8">

    <div class="flex gap-4">
        <button onclick="filterStok('bakso_halus', this)" class="bg-blue-400 p-2 rounded-lg shadow-md text-center mb-1 border-2 border-blue-800  hover:bg-blue-600 w-2xl">
            <p>Bakso Halus</p>
        </button>
        <button onclick="filterStok('bakso_kasar', this)" class="bg-blue-400 p-2 rounded-lg shadow-md text-center mb-1 border-2 border-blue-800  hover:bg-blue-600 w-2xl">
            <p>Bakso Kasar</p>
        </button>
        <button onclick="filterStok('bakso_puyuh', this)" class="bg-blue-400 p-2 rounded-lg shadow-md text-center mb-1 border-2 border-blue-800  hover:bg-blue-600 w-2xl">
            <p>Bakso Puyuh</p>
        </button>
        <button onclick="filterStok('tahu', this)" class="bg-blue-400 p-2 rounded-lg shadow-md text-center mb-1 border-2 border-blue-800  hover:bg-blue-600 w-2xl">
            <p>Tahu</p>
        </button>
        <button onclick="filterStok('somay', this)" class="bg-blue-400 p-2 rounded-lg shadow-md text-center mb-1 border-2 border-blue-800 hover:bg-blue-600 w-2xl">
            <p>Somay</p>
        </button>
    </div>

    <div class="flex gap-4">
        <div class="bg-green-400 p-2 rounded-lg shadow-md text-center mb-1 border-2 border-green-800  hover:bg-blue-600">
            <p>Bakso Halus : <?php echo $dataStok['bakso_halus']?></p>
        </div>
        <div class="bg-green-400 p-2 rounded-lg shadow-md text-center mb-1 border-2 border-green-800  hover:bg-blue-600">
            <p>Bakso Kasar : <?php echo $dataStok['bakso_kasar']?></p>
        </div>
        <div class="bg-green-400 p-2 rounded-lg shadow-md text-center mb-1 border-2 border-green-800  hover:bg-blue-600">
            <p>Bakso Puyuh : <?php echo $dataStok['bakso_puyuh']?></p>
        </div>
        <div class="bg-green-400 p-2 rounded-lg shadow-md text-center mb-1 border-2 border-green-800  hover:bg-blue-600">
            <p>Tahu : <?php echo $dataStok['tahu']?></p>
        </div>
        <div class="bg-green-400 p-2 rounded-lg shadow-md text-center mb-1 border-2 border-green-800 hover:bg-blue-600">
            <p>Somay : <?php echo $dataStok['somay']?></p>
        </div>
    </div>

    <form action="update.php" method="post" class="">
        <div class="bg-white p-6 rounded-lg shadow-md text-center mb-4 stok-card" data-produk="bakso_halus">
            <img src="../../assets/images/stok/bakso halus.png" alt="" class="w-48">
            <p class="text-xl font-semibold text-gray-800">Bakso Halus</p>
            <input type="number" name="baksohalus" value="0" id="stok-bakso_halus" class="text-2xl text-green-600 font-bold text-center w-20 mx-auto mb-3 border border-gray-300 rounded-lg p-1"oninput="cekPerubahan()" min="0">
            <div class="flex justify-between">
                <button type="button" onclick="ubahStok('bakso_halus', 1)" class="bg-blue-600 text-white py-2 px-4 rounded-2xl hover:bg-blue-700 w-20">+</button>
                <button type="button"    onclick="ubahStok('bakso_halus', -1)" class="bg-red-600 text-white py-2 px-4 rounded-2xl hover:bg-red-700 w-20">-</button>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md text-center mb-4 stok-card" data-produk="bakso_kasar">
            <img src="../../assets/images/stok/bakso kasar.png" alt="" class="w-48">
            <p class="text-xl font-semibold text-gray-800">Bakso Kasar</p>
            <input type="number" name="baksokasar" value="0" id="stok-bakso_kasar" class="text-2xl text-green-600 font-bold text-center w-20 mx-auto mb-3 border border-gray-300 rounded-lg p-1"oninput="cekPerubahan()" min="0">
            <div class="flex justify-between">
                <button type="button" onclick="ubahStok('bakso_kasar', 1)" class="bg-blue-600 text-white py-2 px-4 rounded-2xl hover:bg-blue-700 w-20">+</button>
                <button type="button" onclick="ubahStok('bakso_kasar', -1)" class="bg-red-600 text-white py-2 px-4 rounded-2xl hover:bg-red-700 w-20">-</button>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md text-center mb-4 stok-card" data-produk="bakso_puyuh">
            <img src="../../assets/images/stok/bakso halus.png" alt="" class="w-48">
            <p class="text-xl font-semibold text-gray-800">Bakso Puyuh</p>
            <input type="number" name="baksopuyuh" value="0" id="stok-bakso_puyuh" class="text-2xl text-green-600 font-bold text-center w-20 mx-auto mb-3 border border-gray-300 rounded-lg p-1"oninput="cekPerubahan()" min="0">
            <div class="flex justify-between">
                <button type="button" onclick="ubahStok('bakso_puyuh', 1)" class="bg-blue-600 text-white py-2 px-4 rounded-2xl hover:bg-blue-700 w-20">+</button>
                <button type="button" onclick="ubahStok('bakso_puyuh', -1)" class="bg-red-600 text-white py-2 px-4 rounded-2xl hover:bg-red-700 w-20">-</button>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md text-center mb-4 stok-card" data-produk="tahu">
            <img src="../../assets/images/stok/tahu.png" alt="" class="w-48">
            <p class="text-xl font-semibold text-gray-800">Tahu</p>
            <input type="number" name="tahu" value="0" id="stok-tahu" class="text-2xl text-green-600 font-bold text-center w-20 mx-auto mb-3 border border-gray-300 rounded-lg p-1"oninput="cekPerubahan()" min="0">
            <div class="flex justify-between">
                <button type="button" onclick="ubahStok('tahu', 1)" class="bg-blue-600 text-white py-2 px-4 rounded-2xl hover:bg-blue-700 w-20">+</button>
                <button type="button" onclick="ubahStok('tahu', -1)" class="bg-red-600 text-white py-2 px-4 rounded-2xl hover:bg-red-700 w-20">-</button>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md text-center mb-4 stok-card" data-produk="somay">
            <img src="../../assets/images/stok/somay.png" alt="" class="w-48">
            <p class="text-xl font-semibold text-gray-800">Somay</p>
            <input type="number" name="somay" value="0" id="stok-somay" class="text-2xl text-green-600 font-bold text-center w-20 mx-auto mb-3 border border-gray-300 rounded-lg p-1"oninput="cekPerubahan()" min="0">
            <div class="flex justify-between">
                <button type="button" onclick="ubahStok('somay', 1)" class="bg-blue-600 text-white py-2 px-4 rounded-2xl hover:bg-blue-700 w-20">+</button>
                <button type="button" onclick="ubahStok('somay', -1)" class="bg-red-600 text-white py-2 px-4 rounded-2xl hover:bg-red-700 w-20">-</button>
            </div>
        </div>

        <div id="simpan-wrapper" class="hidden lg:fixed lg:bottom-4 lg:right-4 z-50 w-full lg:w-auto px-4 lg:px-0 mt-6 mb-12">
            <button type="submit" onclick="simpanPerubahan()" class="bg-green-600 text-white px-6 py-2 rounded-lg shadow-lg hover:bg-green-700">Simpan Perubahan</button>
        </div>
    </form>
    </div>

<script>
let stokAwal = {
    bakso_halus: 0,
    bakso_kasar: 0,
    bakso_puyuh: 0,
    tahu: 0,
    somay: 0
};

let stokData = { ...stokAwal };

function ubahStok(item, jumlah) {
    let input = document.getElementById(`stok-${item}`);
    let nilaiLama = parseInt(input.value) || 0;
    let nilaiBaru = Math.max(0, nilaiLama + jumlah);
    input.value = nilaiBaru;

    stokData[item] = nilaiBaru;
    cekPerubahan();
}

function cekPerubahan() {
    let berubah = Object.keys(stokData).some(key => {
        let inputVal = parseInt(document.getElementById(`stok-${key}`).value) || 0;
        return inputVal !== stokAwal[key];
    });

    document.getElementById('simpan-wrapper').classList.toggle('hidden', !berubah);
}

  let filterAktif = new Set();

  function filterStok(filterName, button) {
    if (filterAktif.has(filterName)) {
      filterAktif.delete(filterName);
      button.classList.remove('bg-blue-700');
      button.classList.add('bg-blue-400');
    } else {
      filterAktif.add(filterName);
      button.classList.remove('bg-blue-400');
      button.classList.add('bg-blue-700');
    }
    updateFilter();
  }

  function updateFilter() {
    const cards = document.querySelectorAll('.stok-card');

    if (filterAktif.size === 0) {
      cards.forEach(card => card.classList.remove('hidden'));
      return;
    }

    cards.forEach(card => {
      const produk = card.getAttribute('data-produk');
      if (filterAktif.has(produk)) {
        card.classList.remove('hidden');
      } else {
        card.classList.add('hidden');
      }
    });
  }
</script>

</body>
</html>
<?php require_once '../../assets/navbar.php'; ?>