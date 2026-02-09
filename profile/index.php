<?php
session_start();
require_once '../config/config.php';
define('BASE_PATH', getBackPath(__DIR__));

if (verifyAuth($pdo) === false) {
    header('Location: ' . BASE_PATH . 'login/');
    exit;
}

if (verificationBusiness($pdo, $_SESSION['user_id']) === false) {
    header('Location: ' . BASE_PATH . 'profile/reg/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuSync | Ваш профиль</title>
    <link rel="stylesheet" href="../sources/css/pages/profile/index.css">
</head>
<body>
    
</body>
</html>