<?php 
session_start();
if(!isset($_SESSION['session_username'])){
    header('location: /login.php');
    exit();
}
?>

<?php require_once '../../assets/header-admin.php'; ?>
<?php require_once '../../assets/navbar-admin.php'; ?>