<?php
session_start();
require_once '../../config/config.php';
define('BASE_PATH', getBackPath(__DIR__));

if (verifyAuth($pdo) !== false) {
    header('Location: ' . BASE_PATH . 'dashboard/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . 'reg/');
    exit;
}

$userInputs = [
    'username' => [
        'type' => 'username',
        'value' => trim($_POST['username'] ?? ''),
        'minLength' => 4,
        'maxLength' => 50
    ],
    'password' => [
        'type' => 'password',
        'value' => $_POST['password'] ?? '',
        'minLength' => 8,
        'maxLength' => 72
    ],
    'csrf_token' => [
        'type' => 'csrf_token',
        'value' => $_POST['csrf_token'] ?? '',
        'compare_with' => $_SESSION['csrf_token'] ?? ''
    ]
];

function validateInputs(array $userInputs): array {
    $errors = [];

    foreach ($userInputs as $key => $input) {
        if ($key === 'csrf_token') {
            if (empty($input['value'])) {
                $errors[] = 'csrf_token_empty';
            } elseif ($input['value'] !== $input['compare_with']) {
                $errors[] = 'csrf_token_invalid';
            }
            continue;
        }

        if (empty($input['value'])) {
            $errors[] = $input['type'] . '_empty';
            continue;
        }

        if (strlen($input['value']) < $input['minLength']) {
            $errors[] = $input['type'] . '_short';
        }

        if (strlen($input['value']) > $input['maxLength']) {
            $errors[] = $input['type'] . '_long';
        }
    }

    if (!empty($userInputs['username']['value']) && 
        !preg_match('/^[a-zA-Zа-яА-Я0-9_]+$/', $userInputs['username']['value'])) {
        $errors[] = 'username_invalid';
    }

    return $errors;
}

$errors = validateInputs($userInputs);

if (!empty($errors)) {
    unset($_SESSION['csrf_token']);
    header('Location: ' . BASE_PATH . 'reg?error=' . $errors[0]);
    exit;
}

try {
    $stmtCheckUsername = $pdo->prepare("SELECT id FROM `users` WHERE `username` = ?");
    $stmtCheckUsername->execute([$userInputs['username']['value']]);
    $existingUser = $stmtCheckUsername->fetch();

    if ($existingUser) {
        header('Location: ' . BASE_PATH . 'reg?error=username_exists');
        exit;
    }

    $sessionToken = bin2hex(random_bytes(16));
    $username = $userInputs['username']['value'];
    $passwordHash = password_hash($userInputs['password']['value'], PASSWORD_DEFAULT);
    
    $stmtCreateNewUser = $pdo->prepare(
        "INSERT INTO `users` (`session_token`, `username`, `password_hash`) 
         VALUES (?, ?, ?)"
    );
    
    $stmtCreateNewUser->execute([$sessionToken, $username, $passwordHash]);
    $lastInsertId = $pdo->lastInsertId();

    if (!empty($lastInsertId)) {
        $_SESSION['username'] = $userInputs['username']['value'];
        $_SESSION['session_token'] = $sessionToken;
        $_SESSION['user_id'] = $lastInsertId;
        
        unset($_SESSION['csrf_token']);
        
        header('Location: ' . BASE_PATH . 'dashboard/');
        exit;
    } else {
        header('Location: ' . BASE_PATH . 'reg?error=registration_failed');
        exit;
    }
    
} catch (Exception $e) {
    unset($_SESSION['csrf_token']);
    databaseLog(htmlspecialchars($e->getMessage()), __DIR__);
    header('Location: ' . BASE_PATH . 'reg?error=server_error');
    exit;
}