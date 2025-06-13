<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Manajemen Pegawai - Manajemen Bakso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        .file-upload-area {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.2s, border-color 0.2s;
        }
        .file-upload-area.drag-over {
            background-color: #e0e7ff;
            border-color: #6366f1;
        }
        .file-upload-area .file-preview {
            max-width: 100px;
            max-height: 100px;
            margin: 1rem auto 0;
            border-radius: 8px;
            object-cover;
        }
    </style>
</head>
<body class="bg-gray-100">

    <main class="pt-20 px-6 py-8">
        </main>

    <div id="addModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Pegawai Baru</h3>
                <form action="tambah_pegawai.php" method="POST" enctype="multipart/form-data">
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                        <div id="addFileUploadArea" class="file-upload-area">
                            <input type="file" name="foto" id="add_foto_input" class="hidden" accept="image/*">
                            <div id="addFileText">
                                <i class="bi bi-upload text-3xl text-gray-400"></i>
                                <p class="text-gray-500 mt-2">Seret & lepas file atau klik untuk memilih</p>
                            </div>
                            <img id="addFilePreview" class="hidden file-preview" src="#" alt="Pratinjau Foto"/>
                        </div>
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
        <div class="relative top-10 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Pegawai</h3>
                <form action="edit_pegawai.php" method="POST" id="editForm" enctype="multipart/form-data">
                    <input type="hidden" name="pegawai_id" id="edit_pegawai_id">
                    <input type="hidden" name="foto_lama" id="edit_foto_lama">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                        <input type="text" name="nama" id="edit_nama" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="username" id="edit_username" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Baru (Opsional)</label>
                        <div id="editFileUploadArea" class="file-upload-area">
                            <input type="file" name="foto_baru" id="edit_foto_input" class="hidden" accept="image/*">
                            <div id="editFileText">
                                <i class="bi bi-upload text-3xl text-gray-400"></i>
                                <p class="text-gray-500 mt-2">Seret & lepas atau klik untuk ganti foto</p>
                            </div>
                            <img id="editFilePreview" class="file-preview" src="#" alt="Pratinjau Foto"/>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>

        function setupFileUpload(areaId, inputId, textId, previewId) {
            const area = document.getElementById(areaId);
            const input = document.getElementById(inputId);
            const text = document.getElementById(textId);
            const preview = document.getElementById(previewId);

            area.addEventListener('click', () => input.click());

            area.addEventListener('dragover', (e) => {
                e.preventDefault();
                area.classList.add('drag-over');
            });

            area.addEventListener('dragleave', () => {
                area.classList.remove('drag-over');
            });

            area.addEventListener('drop', (e) => {
                e.preventDefault();
                area.classList.remove('drag-over');
                if (e.dataTransfer.files.length) {
                    input.files = e.dataTransfer.files;
                    updatePreview(input, text, preview);
                }
            });

            input.addEventListener('change', () => {
                updatePreview(input, text, preview);
            });
        }

        function updatePreview(input, text, preview) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    text.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        setupFileUpload('addFileUploadArea', 'add_foto_input', 'addFileText', 'addFilePreview');
        setupFileUpload('editFileUploadArea', 'edit_foto_input', 'editFileText', 'editFilePreview');

        function editPegawai(pegawai) {
            document.getElementById('edit_pegawai_id').value = pegawai.pegawai_id;
            document.getElementById('edit_nama').value = pegawai.nama;
            document.getElementById('edit_username').value = pegawai.username;
            document.getElementById('edit_foto_lama').value = pegawai.foto;

            const preview = document.getElementById('editFilePreview');
            preview.src = '/assets/images/' + pegawai.foto;
            preview.classList.remove('hidden');
            document.getElementById('editFileText').classList.add('hidden');

            document.getElementById('edit_foto_input').value = '';

            openModal('editModal');
        }

    </script>
</body>
</html>