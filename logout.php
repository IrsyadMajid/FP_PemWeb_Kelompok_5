<?php
session_start();
$remembered_username = isset($_COOKIE['remember_username']) ? $_COOKIE['remember_username'] : null;
$_SESSION['session_username'] = "";
$_SESSION['session_password'] = "";
session_unset();
session_destroy();
setcookie('username', $username, time() - (86400 * 30));
setcookie('password', $password, time() - (86400 * 30));
header("location: login.php");