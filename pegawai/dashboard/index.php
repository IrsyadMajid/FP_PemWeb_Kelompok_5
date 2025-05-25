<?php 
session_start();
if(!isset($_SESSION['session_username'])){
    header('location: /login.php');
    exit();
}
$username = $_SESSION['session_username'];
// print_r($_SESSION);
// print_r($_COOKIE);
?>

<?php require_once '../../assets/header.php'; ?>
<?php require_once '../../assets/navbar.php'; ?>