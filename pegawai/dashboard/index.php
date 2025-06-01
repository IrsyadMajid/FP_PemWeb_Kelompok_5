<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Pegawai</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6 font-sans">

  <h2 class="text-gray-700 text-xl font-semibold mb-6">Dashboard Pegawai</h2>

  <div class="flex gap-6 justify-center">

    <div class="bg-white rounded-lg shadow-sm p-4 w-72 h-32 flex flex-col justify-center">
      <span class="text-gray-600 text-sm mb-2">Rekap Penjualan Bulanan</span>
      <canvas id="penjualanChart" class="w-full h-20"></canvas>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 w-72 h-32 flex flex-col justify-center">
      <span class="text-gray-600 text-sm mb-2">Stok Barang Terbaru</span>
      <canvas id="stokChart" class="w-full h-20"></canvas>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    async function loadPenjualan() {
      // Path API relatif dari index.php
      const res = await fetch('./api/get_rekap_penjualan.php');
      const json = await res.json();
      if(json.error){
        alert(json.error);
        return null;
      }

      return {
        labels: json.labels,
        datasets: [{
          data: json.data,
          borderColor: 'rgba(37, 99, 235, 0.8)',
          backgroundColor: 'rgba(37, 99, 235, 0.2)',
          fill: true,
          tension: 0.3,
          pointRadius: 0,
        }]
      };
    }

    async function loadStok() {
      const res = await fetch('./api/get_stok_barang.php');
      const json = await res.json();
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
            plugins: { legend: { display: false } },
            scales: { x: { display: false }, y: { display: false } },
            interaction: { intersect: false, mode: 'index' }
          }
        });
      }

      if(stokData){
        new Chart(document.getElementById('stokChart'), {
          type: 'bar',
          data: stokData,
          options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { x: { display: false }, y: { display: false } },
            barPercentage: 0.5,
            categoryPercentage: 0.5,
          }
        });
      }
    }

    renderCharts();
  </script>
</body>
</html>
