<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Header Dropdown</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-100">

  <!-- Header -->
  <header class="bg-blue-600 text-white flex items-center justify-between px-6 py-4 relative z-20">
    <!-- Kiri: Judul -->
    <h1 class="text-2xl font-semibold">Manajemen Bakso</h1>

    <!-- Kanan: Profile Dropdown -->
    <div class="relative">
      <button id="profileButton" class="flex items-center gap-2 focus:outline-none">
        <i class="bi bi-person-circle"></i>
        <span class="font-medium"><?php echo $_SESSION['session_username']; ?></span>
        <i class="bi bi-caret-down-fill text-sm ml-1"></i>
      </button>

      <!-- Dropdown -->
      <div id="dropdownMenu" class="absolute right-0 mt-2 w-40 bg-white text-black rounded-md shadow-lg hidden z-50">
        <a href="/pegawai/profile" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
        <a href="/logout.php" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
      </div>
    </div>
  </header>

  <script>
    const button = document.getElementById('profileButton');
    const dropdown = document.getElementById('dropdownMenu');

    button.addEventListener('click', (e) => {
      e.stopPropagation(); // Mencegah klik bubble
      dropdown.classList.toggle('hidden');
    });

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', function (e) {
      if (!dropdown.contains(e.target) && !button.contains(e.target)) {
        dropdown.classList.add('hidden');
      }
    });
  </script>

</body>
</html>
