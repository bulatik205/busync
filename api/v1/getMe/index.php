<?php
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        'success' => false,
        'error' => [
            'code' => 405,
            'message' => 'Method Not Allowed'
        ]
    ]);
    exit;
}

require_once '../config/config.php';
define('BASE_PATH', getBackPath(__DIR__));

require_once BASE_PATH . 'api/v1/handlers/validate/validateSessionToken.php';
require_once BASE_PATH . 'api/v1/handlers/get/getMe.php';

$headers = getallheaders();

if (!isset($headers['API-key'])) {
    echo json_encode([
        'success' => false,
        'error' => [
            'code' => 401,
            'message' => 'Unauthorized'
        ]
    ]);
    exit;
}

$sessionToken = $headers['API-key'];

$validateSessionToken = new validateSessionToken($sessionToken, $pdo);
$validateSessionTokenResult = $validateSessionToken->validate();

if (!$validateSessionTokenResult['success']) {
    echo json_encode([
        'success' => false,
        'error' => [
            'code' => $validateSessionTokenResult['error']['code'],
            'message' => $validateSessionTokenResult['error']['message']
        ]
    ]);
    exit;
}

$userId = (int)$validateSessionTokenResult['userId'] ?? null;

if (!is_null($userId) && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['userId'];
}

if (!is_numeric($userId)) {
    echo json_encode([
        'success' => false, 
        'error' => [
            'code' => 400, 
            'message' => 'invalid_user_id'
        ]
    ]);
    exit;
}

$getMe = new getMe($userId, $pdo);
$getMeResult = $getMe->get();

echo json_encode($getMeResult);
