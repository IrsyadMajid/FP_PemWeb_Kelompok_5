<?php
session_start();
require_once '../../conn.php';

// Cek apakah user sudah login
if (!isset($_SESSION['session_username'])) {
    header("Location: /login.php");
    exit();
}

$username = $_SESSION['session_username'];
$query = $conn->query("SELECT * FROM admin WHERE username = '$username'");
$admin_data = $query->fetch(PDO::FETCH_ASSOC);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $nama = $_POST['nama'];
                $username_pegawai = $_POST['username'];
                $password = $_POST['password'];
                $foto = $_POST['foto'] ?? 'default-user.jpg';
                
                $stmt = $conn->prepare("INSERT INTO pegawai (nama, username, password, status_aktif, foto) VALUES (?, ?, ?, 1, ?)");
                $stmt->execute([$nama, $username_pegawai, $password, $foto]);
                
                // Tambahkan stok awal untuk pegawai baru
                $pegawai_id = $conn->lastInsertId();
                $stmt = $conn->prepare("INSERT INTO stok_pegawai (pegawai_id) VALUES (?)");
                $stmt->execute([$pegawai_id]);
                
                $_SESSION['message'] = "Pegawai berhasil ditambahkan!";
                break;
                
            case 'edit':
                $pegawai_id = $_POST['pegawai_id'];
                $nama = $_POST['nama'];
                $username_pegawai = $_POST['username'];
                $foto = $_POST['foto'];
                
                $stmt = $conn->prepare("UPDATE pegawai SET nama = ?, username = ?, foto = ? WHERE pegawai_id = ?");
                $stmt->execute([$nama, $username_pegawai, $foto, $pegawai_id]);
                
                $_SESSION['message'] = "Data pegawai berhasil diperbarui!";
                break;
                
            case 'change_password':
                $pegawai_id = $_POST['pegawai_id'];
                $new_password = $_POST['new_password'];
                
                $stmt = $conn->prepare("UPDATE pegawai SET password = ? WHERE pegawai_id = ?");
                $stmt->execute([$new_password, $pegawai_id]);
                
                $_SESSION['message'] = "Password berhasil diubah!";
                break;
                
            case 'delete':
                $pegawai_id = $_POST['pegawai_id'];
                
                // Hapus stok pegawai terlebih dahulu
                $stmt = $conn->prepare("DELETE FROM stok_pegawai WHERE pegawai_id = ?");
                $stmt->execute([$pegawai_id]);
                
                // Hapus pegawai
                $stmt = $conn->prepare("DELETE FROM pegawai WHERE pegawai_id = ?");
                $stmt->execute([$pegawai_id]);
                
                $_SESSION['message'] = "Pegawai berhasil dihapus!";
                break;
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Ambil data pegawai
$pegawai_query = $conn->query("SELECT * FROM pegawai ORDER BY nama ASC");
$pegawai_list = $pegawai_query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Manajemen Pegawai - Manajemen Bakso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-100">

    <!-- Header -->
    <header class="bg-blue-600 text-white flex items-center justify-between px-6 py-4 fixed w-full z-20">
        <!-- Kiri: Judul -->
        <h1 class="text-2xl font-semibold">Manajemen Bakso - Pegawai</h1>

        <!-- Kanan: Profile Dropdown -->
        <div class="relative">
            <button id="profileButton" class="flex items-center gap-2 focus:outline-none">
                <img src="/assets/images/<?php echo $admin_data['foto'] ?? 'default-admin.jpg'; ?>" alt="Profile" class="w-8 h-8 rounded-full object-cover">
                <span class="font-medium"><?php echo $_SESSION['session_username']; ?></span>
                <i class="bi bi-caret-down-fill text-sm ml-1"></i>
            </button>

            <!-- Dropdown -->
            <div id="dropdownMenu" class="absolute right-0 mt-2 w-40 bg-white text-black rounded-md shadow-lg hidden z-50">
                <a href="/admin/profile/index.php" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
                <a href="/logout.php" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-20 px-6 py-8">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Add Employee Button -->
        <div class="mb-6">
            <button onclick="openModal('addModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="bi bi-person-plus"></i>
                Tambah Pegawai
            </button>
        </div>

        <!-- Employee Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
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
                            <img src="/assets/images/<?php echo $pegawai['foto']; ?>" alt="Foto" class="w-10 h-10 rounded-full object-cover">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($pegawai['nama']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($pegawai['username']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $pegawai['status_aktif'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $pegawai['status_aktif'] ? 'Aktif' : 'Nonaktif'; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <button onclick="editPegawai(<?php echo htmlspecialchars(json_encode($pegawai)); ?>)" class="text-blue-600 hover:text-blue-900">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button onclick="changePassword(<?php echo $pegawai['pegawai_id']; ?>, '<?php echo htmlspecialchars($pegawai['nama']); ?>')" class="text-yellow-600 hover:text-yellow-900">
                                <i class="bi bi-key"></i>
                            </button>
                            <button onclick="confirmDelete(<?php echo $pegawai['pegawai_id']; ?>, '<?php echo htmlspecialchars($pegawai['nama']); ?>')" class="text-red-600 hover:text-red-900">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Add Modal -->
    <div id="addModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Pegawai Baru</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                        <input type="text" name="nama" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="username" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto (nama file)</label>
                        <input type="text" name="foto" placeholder="default-user.jpg" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('addModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Pegawai</h3>
                <form method="POST" id="editForm">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="pegawai_id" id="edit_pegawai_id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                        <input type="text" name="nama" id="edit_nama" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="username" id="edit_username" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto (nama file)</label>
                        <input type="text" name="foto" id="edit_foto" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="passwordModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ubah Password</h3>
                <form method="POST" id="passwordForm">
                    <input type="hidden" name="action" value="change_password">
                    <input type="hidden" name="pegawai_id" id="password_pegawai_id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pegawai</label>
                        <input type="text" id="password_nama" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                        <input type="password" name="new_password" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('passwordModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">Ubah Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="bi bi-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4 mb-2">Konfirmasi Hapus</h3>
                <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus pegawai <span id="delete_nama" class="font-semibold"></span>?</p>
                <form method="POST" id="deleteForm">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="pegawai_id" id="delete_pegawai_id">
                    <div class="flex justify-center space-x-3">
                        <button type="button" onclick="closeModal('deleteModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Profile dropdown functionality
        const button = document.getElementById('profileButton');
        const dropdown = document.getElementById('dropdownMenu');

        button.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target) && !button.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function editPegawai(pegawai) {
            document.getElementById('edit_pegawai_id').value = pegawai.pegawai_id;
            document.getElementById('edit_nama').value = pegawai.nama;
            document.getElementById('edit_username').value = pegawai.username;
            document.getElementById('edit_foto').value = pegawai.foto;
            openModal('editModal');
        }

        function changePassword(pegawaiId, nama) {
            document.getElementById('password_pegawai_id').value = pegawaiId;
            document.getElementById('password_nama').value = nama;
            openModal('passwordModal');
        }

        function confirmDelete(pegawaiId, nama) {
            document.getElementById('delete_pegawai_id').value = pegawaiId;
            document.getElementById('delete_nama').textContent = nama;
            openModal('deleteModal');
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            const modals = ['addModal', 'editModal', 'passwordModal', 'deleteModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (e.target === modal) {
                    closeModal(modalId);
                }
            });
        });
    </script>

</body>
</html>