<?php
require_once "logger.php";
require_once "pathGet.php";
require_once "verificationUser.php";

# base configuration on MAMP (https://www.mamp.info/)
$main_host = "localhost"; 
$main_user = "root"; 
$main_pass = "root"; 
$main_db = "busync"; 

$pdo = new PDO(
    "mysql:host=$main_host;dbname=$main_db;charset=utf8mb4",
    $main_user,
    $main_pass, 
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);
?>