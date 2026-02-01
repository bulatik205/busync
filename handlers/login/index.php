<?php
session_start();
require_once '../../config/config.php';
define('BASE_PATH', getBackPath(__DIR__));

if (verifyAuth($pdo) !== false) {
    header('Location: ' . BASE_PATH . 'dashboard/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . 'login/');
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
        'minLength' => 6,
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

        if (strlen($input['value']) > $input['maxLength']) {
            $errors[] = $input['type'] . '_long';
        }

        if (strlen($input['value']) < $input['minLength']) {
            $errors[] = $input['type'] . '_short';
        }
    }

    return $errors;
}

$errors = validateInputs($userInputs);

if (!empty($errors)) {
    header('Location: ' . BASE_PATH . 'login?error=' . $errors[0]);
    exit;
}

try {
    $stmtCheckUser = $pdo->prepare("SELECT * FROM `users` WHERE `username` = ?");
    $stmtCheckUser->execute([$userInputs['username']['value']]);
    $user = $stmtCheckUser->fetch();

    if (empty($user)) {
        header('Location: ' . BASE_PATH . 'login?error=incurrect_input');
        exit;
    }

    if (!password_verify($userInputs['password']['value'], $user['password_hash'])) {
        header('Location: ' . BASE_PATH . 'login?error=incurrect_input');
        exit;
    }

    $sessionToken = bin2hex(random_bytes(16));
    
    $stmtRefreshToken = $pdo->prepare("UPDATE `users` SET `session_token` = ? WHERE `id` = ?");
    $stmtRefreshToken->execute([$sessionToken, $user['id']]);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['session_token'] = $sessionToken;
    $_SESSION['username'] = $user['username'];

    unset($userInputs['password']['value']);

    header('Location: ' . BASE_PATH . 'dashboard/');
    exit;
} catch (Exception $e) {
    databaseLog($e->getMessage(), __DIR__);
    header('Location: ' . BASE_PATH . 'login?error=server_error');
    exit;
}