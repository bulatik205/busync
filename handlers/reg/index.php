<?php
session_start();

require_once '../../config/config.php';

define('BASE_PATH', getBackPath(__DIR__));

require_once BASE_PATH . 'modules/user/createUser.php';
require_once BASE_PATH . 'modules/user/validateUserExist.php';

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

$validateUserClass = new validateUserExist($userInputs['username']['value'], $pdo);
$validateResult = $validateUserClass->validate();

if ($validateResult) {
    header('Location: ' . BASE_PATH . 'reg?error=username_exists');
    exit;
}

$passwordHash = password_hash($userInputs['password']['value'], PASSWORD_DEFAULT);

$createUserClass = new createUser($userInputs['username']['value'], $passwordHash, $pdo);
$createResult = $createUserClass->create();

if ($createResult) {
    header('Location: ' . BASE_PATH . 'dashboard/');
    exit;
} else {
    header('Location: ' . BASE_PATH . 'reg?error=registration_failed');
    exit;
}

