<?php
session_start();
require_once '../../conn.php';

if (!isset($_SESSION['session_username'])) {
    header("Location: /login.php");
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Manajemen Pegawai - Manajemen Bakso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-100">

    <header class="bg-blue-600 text-white flex items-center justify-between px-6 py-4 fixed w-full z-20">
        <h1 class="text-2xl font-semibold">Manajemen Bakso - Pegawai</h1>

        <div class="relative">
            <button id="profileButton" class="flex items-center gap-2 focus:outline-none">
                <img src="../../assets/images/<?php echo $admin_data['foto'] ?? 'default-admin.jpg'; ?>" alt="Profile" class="w-8 h-8 rounded-full object-cover">
                <span class="font-medium"><?php echo $_SESSION['session_username']; ?></span>
                <i class="bi bi-caret-down-fill text-sm ml-1"></i>
            </button>

            <div id="dropdownMenu" class="absolute right-0 mt-2 w-40 bg-white text-black rounded-md shadow-lg hidden z-50">
                <a href="../../admin/profile/index.php" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
                <a href="../../logout.php" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
            </div>
        </div>
    </header>

    <main class="pt-20 px-6 py-8">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="mb-6 flex justify-between items-center">
            <a href="../../admin/dashboard/index.php" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="bi bi-arrow-left"></i>
                Kembali ke Dashboard
            </a>
            <button onclick="openModal('addModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="bi bi-person-plus"></i>
                Tambah Pegawai
            </button>
        </div>

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
                            <button onclick="editPegawai(<?php echo htmlspecialchars(json_encode($pegawai)); ?>)" class="text-blue-600 hover:text-blue-900" title="Edit Data">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button onclick="changePassword(<?php echo $pegawai['pegawai_id']; ?>, '<?php echo htmlspecialchars($pegawai['nama']); ?>')" class="text-yellow-600 hover:text-yellow-900" title="Ubah Password">
                                <i class="bi bi-key"></i>
                            </button>
                            <button onclick="confirmDelete(<?php echo $pegawai['pegawai_id']; ?>, '<?php echo htmlspecialchars($pegawai['nama']); ?>')" class="text-red-600 hover:text-red-900" title="Hapus Pegawai">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="addModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Pegawai Baru</h3>
                <form action="tambah_pegawai.php" method="POST">
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
                        <input type="password" name="password" required minlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <small class="text-gray-500">Minimal 6 karakter</small>
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

    <div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Pegawai</h3>
                <form action="edit_pegawai.php" method="POST" id="editForm">
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

    <div id="passwordModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ubah Password</h3>
                <form action="ubah_password.php" method="POST" id="passwordForm">
                    <input type="hidden" name="pegawai_id" id="password_pegawai_id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pegawai</label>
                        <input type="text" id="password_nama" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                        <input type="password" name="new_password" required minlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <small class="text-gray-500">Minimal 6 karakter</small>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('passwordModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">Ubah Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="bi bi-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4 mb-2">Konfirmasi Hapus</h3>
                <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus pegawai <span id="delete_nama" class="font-semibold"></span>?</p>
                <form action="hapus_pegawai.php" method="POST" id="deleteForm">
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

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modals = ['addModal', 'editModal', 'passwordModal', 'deleteModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (!modal.classList.contains('hidden')) {
                        closeModal(modalId);
                    }
                });
            }
        });

        window.addEventListener('click', function(e) {
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