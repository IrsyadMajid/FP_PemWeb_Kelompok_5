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
            header('Location: ./login.php');
        }
    } else {
        header('Location: ./login.php');
    }
} else {
    header('Location: ./login.php');
}
exit();
?>