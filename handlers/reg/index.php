<?php
session_start();
require_once '../../config/config.php';
define('BASE_PATH', getBackPath(__DIR__));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . 'reg/');
    exit;
}

function validateInputs(): bool
{
    if (empty($_POST['csrf_token'])) {
        header('Location: ' . BASE_PATH . 'reg?error=csrf_token_empty');
        return false;
    }

    if (empty($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: ' . BASE_PATH . 'reg?error=csrf_token_invalid');
        return false;
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username)) {
        header('Location: ' . BASE_PATH . 'reg?error=username_empty');
        return false;
    }

    if (empty($password)) {
        header('Location: ' . BASE_PATH . 'reg?error=password_empty');
        return false;
    }

    if (strlen($username) < 4) {
        header('Location: ' . BASE_PATH . 'reg?error=username_short');
        return false;
    }

    if (strlen($username) > 50) { 
        header('Location: ' . BASE_PATH . 'reg?error=username_long');
        return false;
    }

    if (strlen($password) < 8) { 
        header('Location: ' . BASE_PATH . 'reg?error=password_short');
        return false;
    }

    if (strlen($password) > 72) { 
        header('Location: ' . BASE_PATH . 'reg?error=password_long');
        return false;
    }

    if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $username)) {
        header('Location: ' . BASE_PATH . 'reg?error=username_invalid');
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
        header('Location: ' . BASE_PATH . 'reg?error=username_exists');
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
        header('Location: ' . BASE_PATH . 'reg?error=registration_failed');
        exit;
    }
    
} catch (PDOException $e) {
    error_log("Registration PDO error: " . $e->getMessage());
    exit;
} catch (Exception $e) {
    databaseLog($e->getMessage(), __DIR__);
    header('Location: ' . BASE_PATH . 'reg?error=server_error');
    exit;
}