<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../login/');
    exit;
}

function validateInputs(): bool
{
    if (!isset($_POST['csrf_token'])) {
        header('Location: ../../login?error=csrf_token_empty');
        return false;
    }

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: ../../login?error=csrf_token_invalid');
        return false;
    }

    if (!isset($_POST['password'])) {
        header('Location: ../../login?error=password_empty');
        return false;
    }

    if (!isset($_POST['username'])) {
        header('Location: ../../login?error=username_empty');
        return false;
    }

    if (strlen($_POST['password']) < 6) {
        header('Location: ../../login?error=password_short');
        return false;
    }

    if (strlen($_POST['username']) < 4) {
        header('Location: ../../login?error=username_short');
        return false;
    }

    if (strlen($_POST['password']) > 128) {
        header('Location: ../../login?error=password_long');
        return false;
    }

    if (strlen($_POST['username']) > 128) {
        header('Location: ../../login?error=username_long');
        return false;
    }
    
    return true;
}

if (!validateInputs()) {
    exit;
}

try {
    $stmtCheckUser = $pdo->prepare("SELECT * FROM `users` WHERE `login` = ?");
    $stmtCheckUser->execute([$_POST['username']]);
    $stmtCheckUser = $stmtCheckUser->fetch();

    if (empty($stmtCheckUser)) {
        header('Location: ../../login?error=user_not_found');
        exit;
    }

    if (!password_verify($_POST['password'], $stmtCheckUser['password'])) {
        header('Location: ../../login?error=wrong_password');
        exit;
    }

    $_SESSION['user_id'] = $stmtCheckUser['id'];
    $_SESSION['user_hash'] = $stmtCheckUser['hash'];
    $_SESSION['user_login'] = $stmtCheckUser['login'];

    header('Location: ../../dashboard/');
    exit;
} catch (Exception $e) {
    databaseLog($e->getMessage(), __DIR__);
    header('Location: ../../login?error=server_error');
    exit;
}