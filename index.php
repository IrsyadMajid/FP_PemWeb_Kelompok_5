<?php
session_start();

if(isset($_SESSION['login'])){
    // Jika user sudah login, redirect berdasarkan role
    if(isset($_SESSION['session_role'])){
        // Mapping role ke halaman tujuan
        $role_pages = [
            'admin'   => 'admin/dashboard/index.php',
            'pegawai'    => 'pegawai/dashboard/index.php'
        ];
        
        // Cek apakah role ada dalam mapping
        if(array_key_exists($_SESSION['session_role'], $role_pages)){
            header('Location: ' . $role_pages[$_SESSION['session_role']]);
        } else {
            // Jika role tidak dikenali, redirect ke dashboard default
            header('Location: dashboard/index.php');
        }
    } else {
        // Jika session role tidak ada, redirect ke dashboard default
        header('Location: dashboard/index.php');
    }
} else {
    // Jika belum login, redirect ke halaman login
    header('Location: ./login.php');
}
exit();
?>