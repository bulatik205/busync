<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../reg/');
    exit;
}

function validateInputs(): bool
{
    if (empty($_POST['csrf_token'])) {
        header('Location: ../../reg?error=csrf_token_empty');
        return false;
    }

    if (empty($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: ../../reg?error=csrf_token_invalid');
        return false;
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username)) {
        header('Location: ../../reg?error=username_empty');
        return false;
    }

    if (empty($password)) {
        header('Location: ../../reg?error=password_empty');
        return false;
    }

    if (strlen($username) < 4) {
        header('Location: ../../reg?error=username_short');
        return false;
    }

    if (strlen($username) > 50) { 
        header('Location: ../../reg?error=username_long');
        return false;
    }

    if (strlen($password) < 8) { 
        header('Location: ../../reg?error=password_short');
        return false;
    }

    if (strlen($password) > 72) { 
        header('Location: ../../reg?error=password_long');
        return false;
    }

    if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $username)) {
        header('Location: ../../reg?error=username_invalid');
        return false;
    }

    return true;
}

if (!validateInputs()) {
    exit;
}

try {
    $stmtCheckUsername = $pdo->prepare("SELECT id FROM `users` WHERE `login` = ?");
    $stmtCheckUsername->execute([trim($_POST['username'])]);
    $existingUser = $stmtCheckUsername->fetch();

    if ($existingUser) {
        header('Location: ../../reg?error=username_exists');
        exit;
    }

    $sessionHash = bin2hex(random_bytes(16));
    $username = trim($_POST['username']);
    $passwordHash = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    
    $stmtCreateNewUser = $pdo->prepare(
        "INSERT INTO `users` (`hash`, `login`, `password`) 
         VALUES (?, ?, ?)"
    );
    
    $stmtCreateNewUser->execute([$sessionHash, $username, $passwordHash]);
    $lastInsertId = $pdo->lastInsertId();

    if (!empty($lastInsertId)) {
        $_SESSION['user_id'] = $lastInsertId;
        $_SESSION['user_login'] = $username;
        
        unset($_SESSION['csrf_token']);
        
        header('Location: /dashboard');
        exit;
    } else {
        header('Location: ../../reg?error=registration_failed');
        exit;
    }
    
} catch (PDOException $e) {
    error_log("Registration PDO error: " . $e->getMessage());
    exit;
} catch (Exception $e) {
    databaseLog($e->getMessage(), __DIR__);
    header('Location: ../../reg?error=server_error');
    exit;
}