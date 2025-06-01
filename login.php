<?php
session_start();
include("conn.php");

$err = '';
$username = '';
$password = '';
$rememberme = '';

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
        $user_found = false;
        $role = '';
        $data = null;

        $stmt_admin = $conn->prepare("SELECT * FROM admin WHERE username = :username LIMIT 1");
        $stmt_admin->execute(['username' => $username]);
        $data_admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);

        if ($data_admin) {
            if ($data_admin['PASSWORD'] === $password) {
                $user_found = true;
                $role = 'admin';
                $data = $data_admin;
                // Jika perlu, simpan admin_id juga di session
                $_SESSION['admin_id'] = $data_admin['admin_id'];
            }
        }

        if (!$user_found) {
            $stmt_pegawai = $conn->prepare("SELECT * FROM pegawai WHERE username = :username AND status_aktif = TRUE LIMIT 1");
            $stmt_pegawai->execute(['username' => $username]);
            $data_pegawai = $stmt_pegawai->fetch(PDO::FETCH_ASSOC);

            if ($data_pegawai) {
                if ($data_pegawai['PASSWORD'] === $password) {
                    $user_found = true;
                    $role = 'pegawai';
                    $data = $data_pegawai;

                    // Tambahkan penyimpanan pegawai_id di session
                    $_SESSION['pegawai_id'] = $data_pegawai['pegawai_id'];
                }
            }
        }

        if (!$user_found) {
            $err .= "<li><b>Username atau password yang Anda masukkan salah</b></li>";
        }

        if (empty($err) && $user_found) {
            $_SESSION['session_username'] = $username;
            $_SESSION['session_role'] = $role;
            $_SESSION['login'] = true;

            if ($rememberme === '1') {
                setcookie('remember_username', $username, time() + (86400 * 30), "/");
            } else {
                setcookie('remember_username', '', time() - 3600, "/");
            }

            switch ($role) {
                case 'admin':
                    header("Location: admin/dashboard/index.php");
                    exit;
                case 'pegawai':
                    header("Location: pegawai/dashboard/index.php");
                    exit;
                default:
                    header("Location: index.php");
                    exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Pegawai</title>
    <link href="src/output.css" rel="stylesheet" />
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
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="<?php echo htmlspecialchars($username) ?>"
                    autocomplete="off"
                    placeholder=" "
                    class="border-b w-full focus:outline-none mt-1 py-1 focus:border-blue-800 focus:border-b-2 transition-colors duration-200 peer"
                />
                <label
                    for="username"
                    class="absolute left-0 top-1 text-gray-600 cursor-text transition-all peer-placeholder-shown:top-1 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-600 peer-focus:text-xs peer-focus:-top-4 peer-focus:text-purple-600 peer-not-placeholder-shown:text-xs peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-purple-600 transition-all"
                >
                    Username
                </label>
            </div>

            <div class="relative">
                <input
                    type="password"
                    name="password"
                    placeholder=" "
                    class="border-b w-full focus:outline-none mt-1 py-1 focus:border-blue-800 focus:border-b-2 transition-colors duration-200 peer"
                />
                <label
                    for="password"
                    class="absolute left-0 top-1 text-gray-600 cursor-text transition-all peer-placeholder-shown:top-1 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-600 peer-focus:text-xs peer-focus:-top-4 peer-focus:text-purple-600 peer-not-placeholder-shown:text-xs peer-not-placeholder-shown:-top-4 peer-not-placeholder-shown:text-purple-600 transition-all"
                >
                    Password
                </label>
            </div>

            <label class="flex items-center space-x-2 text-sm text-gray-700">
                <input
                    type="checkbox"
                    name="rememberme"
                    value="1"
                    <?php if ($rememberme == '1') echo "checked" ?>
                />
                <span>Remember Me</span>
            </label>

            <input
                type="submit"
                name="login"
                value="Login"
                class="bg-blue-600 text-white py-2 px-4 rounded-2xl hover:bg-blue-700 transition-colors duration-200 cursor-pointer"
            />
        </form>
    </div>
</body>
</html>
