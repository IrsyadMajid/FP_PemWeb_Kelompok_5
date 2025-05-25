<?php
session_start();
include("conn.php");

$err = '';
$username = '';
$password = '';
$rememberme = '';

// Ambil username dari cookie jika ada
if (isset($_COOKIE['remember_username'])) {
    $username = $_COOKIE['remember_username'];
}

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $rememberme = isset($_POST['rememberme']) ? '1' : '';

    if ($username === '' || $password === '') {
        $err .= "<li>Username atau password masih kosong.</li>";
    } else {
        // Gunakan prepared statement untuk keamanan
        $stmt = $conn->prepare("SELECT * FROM login WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data || $data['password'] !== $password) {
            $err .= "<li><b>Username atau password yang anda masukkan salah</b></li>";
        }

        if (empty($err)) {
            $_SESSION['session_username'] = $username;
            $_SESSION['session_role'] = $data['role'];
            $_SESSION['login'] = true;

            if ($rememberme === '1') {
                setcookie('remember_username', $username, time() + (86400 * 30), "/");
            } else {
                setcookie('remember_username', '', time() - 3600, "/"); // Hapus cookie jika tidak dicentang
            }

            switch ($data['role']) {
                case 'admin':
                    header("Location: admin/dashboard/index.php");
                    exit;
                case 'pegawai':
                    header("Location: pegawai/dashboard/index.php");
                    exit;
                default:
                    header("Location: pegawai/dashboard/index.php");
                    exit;
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="src/output.css" rel="stylesheet">
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-b from-blue-500 to-white">
    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
        <?php if ($err) { ?>
            <div class="mb-10 p-4 bg-red-500/90 text-white rounded">
                <ul><?php echo $err ?></ul>
            </div>
        <?php } ?>

        <form action="" method="post" role="form" class="flex flex-col space-y-6">
            <div class="relative">
                <input type="text" id="username"
                    class="border-b w-full focus:outline-none mt-1 py-1 focus:border-blue-800 focus:border-b-2 transition-colors duration-200  peer"
                    autocomplete="off" placeholder=" " name="username" value="<?php echo $username ?>">
                <label for="username" class="absolute left-0 top-1 text-gray-600 cursor-text transition-allpeer-placeholder-shown:top-1 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-600 peer-focus:text-xs peer-focus:-top-4 peer-focus:text-purple-600 peer-not-placeholder-shown:text-xs peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-purple-600 transition-all">
                    Username
                </label>
            </div>

            <div class="relative">
                <input type="password"
                    class="border-b w-full focus:outline-none mt-1 py-1 focus:border-blue-800 focus:border-b-2 transition-colors duration-200 peer"
                    placeholder=" " name="password">
                <label for="password"
                    class="absolute left-0 top-1 text-gray-600 cursor-text transition-allpeer-placeholder-shown:top-1 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-600 peer-focus:text-xs peer-focus:-top-4 peer-focus:text-purple-600 peer-not-placeholder-shown:text-xs peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-purple-600 transition-all">
                    Password
                </label>
            </div>

            <label class="flex items-center space-x-2 text-sm text-gray-700">
                <input type="checkbox" name="rememberme" value="1" <?php if ($rememberme == '1') echo "checked" ?>>
                <span>Remember Me</span>
            </label>

            <input type="submit" name="login"
                class="bg-blue-600 text-white py-2 px-4 rounded-2xl hover:bg-blue-700 transition-colors duration-200"
                value="Login" />
        </form>
    </div>
</body>
</html>