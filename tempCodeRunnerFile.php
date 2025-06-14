<?php
    $dbServer = 'localhost';
    $dbUser = 'root';
    $dbPass = '';
    $dbName = "manajemen_bakso";

    try {
        $conn = new PDO("mysql:host=$dbServer;dbname=$dbName", $dbUser, $dbPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $err)
    {
        echo "Failed Connect to Database Server : " . $err->getMessage();
    }