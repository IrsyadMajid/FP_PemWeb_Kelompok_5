<?php 
session_start();
if(!isset($_SESSION['session_username'])){
    header('location: /login.php');
    exit();
}
?>

<?php require_once '../../assets/header-admin.php'; ?>
<?php require_once '../../assets/navbar-admin.php'; ?>

<!DOCTYPE html>
<html lang="id" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Pegawai</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">

  <div class="flex flex-col justify-center items-center min-h-screen px-4">

    <h1 class="text-2xl font-semibold mb-4 text-center text-gray-700">
      Selamat datang, 
      <span class="text-blue-600">
        <?php echo htmlspecialchars($_SESSION['session_username']); ?>
      </span>
    </h1>

    <h2 class="text-gray-700 text-xl font-semibold mb-6 text-center">Dashboard Admin</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 max-w-6xl w-full pl-16">

      <div class="bg-white rounded-lg shadow p-6 h-80 flex flex-col">
        <span class="text-gray-700 mb-4 font-semibold text-lg">Rekap Penjualan Harian</span>
        <canvas id="penjualanChart" class="w-full flex-grow"></canvas>
      </div>

      <div class="bg-white rounded-lg shadow p-6 h-80 flex flex-col">
        <span class="text-gray-700 mb-4 font-semibold text-lg">Stok Barang</span>
        <canvas id="stokChart" class="w-full flex-grow"></canvas>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    async function loadPenjualan() {
      const res = await fetch('./api/get_rekap_penjualan.php');
      const json = await res.json();
      console.log('Penjualan data:', json);
      if(json.error){
        alert(json.error);
        return null;
      }
      return {
        labels: json.labels,
        datasets: [{
          data: json.data,
          borderColor: 'rgba(37, 99, 235, 0.8)',
          backgroundColor: 'rgba(37, 99, 235, 0.3)',
          fill: true,
          tension: 0.3,
          pointRadius: 4,
          pointHoverRadius: 6,
          borderWidth: 2
        }]
      };
    }

    async function loadStok() {
      const res = await fetch('./api/get_stok_pegawai.php');
      const json = await res.json();
      console.log('Stok data:', json);
      if(json.error){
        alert(json.error);
        return null;
      }
      return {
        labels: json.labels,
        datasets: [{
          data: json.data,
          backgroundColor: [
            'rgba(249, 115, 22, 0.7)',
            'rgba(234, 179, 8, 0.7)',
            'rgba(59, 130, 246, 0.7)',
            'rgba(139, 92, 246, 0.7)',
            'rgba(156, 163, 175, 0.7)'
          ],
          borderWidth: 0
        }]
      };
    }

    async function renderCharts() {
      const penjualanData = await loadPenjualan();
      const stokData = await loadStok();

      if(penjualanData){
        new Chart(document.getElementById('penjualanChart'), {
          type: 'line',
          data: penjualanData,
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              tooltip: { enabled: true }
            },
            scales: {
              x: {
                display: true,
                grid: { display: false },
                ticks: { color: '#555', maxRotation: 0, minRotation: 0 }
              },
              y: {
                display: true,
                grid: { color: '#eee' },
                ticks: { color: '#555', beginAtZero: true }
              }
            },
            interaction: { intersect: false, mode: 'nearest' }
          }
        });
      }

      if(stokData){
        new Chart(document.getElementById('stokChart'), {
          type: 'bar',
          data: stokData,
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              tooltip: { enabled: true }
            },
            scales: {
              x: {
                display: true,
                grid: { display: false },
                ticks: { color: '#555' }
              },
              y: {
                display: true,
                grid: { color: '#eee' },
                ticks: { color: '#555', beginAtZero: true }
              }
            },
            barPercentage: 0.6,
            categoryPercentage: 0.6,
          }
        });
      }
    }

    renderCharts();
  </script>

</body>
</html>
