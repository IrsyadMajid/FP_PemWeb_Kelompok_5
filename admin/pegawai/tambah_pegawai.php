<?php
session_start();
require_once '../../conn.php';

if (!isset($_SESSION['session_username'])) {
    header("Location: /login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $username_pegawai = $_POST['username'];
    $password = $_POST['password'];
    
    $foto = $_FILES['foto']['name'] ?? null;
    $tmp = $_FILES['foto']['tmp_name'] ?? null;
    $timestamp = time();
    $namaFile = 'default-user.jpg'; // default

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            $namaFile = $timestamp . '-' . basename($foto);
            $upload_dir = __DIR__ . '/../../assets/images/';
            $location = $upload_dir . $namaFile;

            if (!move_uploaded_file($tmp, $location)) {
                $_SESSION['error'] = "Gagal menyimpan foto ke server.";
                header("Location: index.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Format file tidak didukung! (Gunakan jpg, jpeg, png, gif)";
            header("Location: index.php");
            exit();
        }
    }

    try {
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM pegawai WHERE username = ?");
        $check_stmt->execute([$username_pegawai]);
        $username_exists = $check_stmt->fetchColumn();

        if ($username_exists > 0) {
            $_SESSION['error'] = "Username sudah digunakan!";
            header("Location: index.php");
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO pegawai (nama, username, password, status_aktif, foto) VALUES (?, ?, ?, 1, ?)");
        $stmt->execute([$nama, $username_pegawai, $password, $namaFile]);

        $pegawai_id = $conn->lastInsertId();

        $stmt = $conn->prepare("INSERT INTO stok_pegawai (pegawai_id) VALUES (?)");
        $stmt->execute([$pegawai_id]);

        $stmt = $conn->prepare("INSERT INTO audit_log (user_id, role, aksi, waktu_aksi) VALUES (?, 'admin', ?, NOW())");
        $stmt->execute([1, "Menambah pegawai: $nama"]);

        $_SESSION['message'] = "Pegawai '$nama' berhasil ditambahkan!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    header("Location: index.php");
    exit();
}
?>