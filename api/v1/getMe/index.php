<?php
require_once '../config/config.php';
define('BASE_PATH', getBackPath(__DIR__));

require_once BASE_PATH . 'handlers/validate/validateSessionToken.php';
require_once BASE_PATH . 'handlers/get/getMe.php';

$headers = getallheaders();

if (!isset($headers['API-key'])) {
    echo json_encode([
        'success' => false,
        'error' => [
            'code' => 401,
            'message' => 'Unauthorized'
        ]
    ]);
}

$sessionToken = $headers['API-key'];

$validateSessionToken = new validateSessionToken($sessionToken, $pdo);
$validateSessionTokenResult = $validateSessionToken->validate();

if (!$validateSessionToken['success']) {
    echo json_encode([
        'success' => false,
        'error' => [
            'code' => $validateSessionTokenResult['error']['code'],
            'message' => $validateSessionTokenResult['error']['message']
        ]
    ]);
}

$userId = (int)$validateSessionTokenResult['user_id'] ?? null;

if (is_null($userId) && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
}

if (is_numeric($userId)) {
    echo json_encode([
        'success' => false, 
        'error' => [
            'code' => 400, 
            'message' => 'invalid_user_id'
        ]
    ]);
}

$getMe = new getMe($userId, $pdo);
$getMeResult = $getMe->get();

echo json_encode($getMeResult);
