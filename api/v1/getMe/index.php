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

$getMe = new getMe($validateSessionTokenResult['user_id'], $pdo);
$getMeResult = $getMe->get();

echo json_encode($getMeResult);