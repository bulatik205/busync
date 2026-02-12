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

require_once BASE_PATH . 'api/v1/handlers/validate/validateApiKey.php';
require_once BASE_PATH . 'api/v1/handlers/get/getItems/getItems.php';
require_once BASE_PATH . 'api/v1/handlers/get/getItems/validateLimits.php';

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

$apiKey = $headers['API-key'];

$validateApiKey = new validateApiKey($apiKey, $pdo);
$validateApiKeyResult = $validateApiKey->validate();

if (!$validateApiKeyResult['success']) {
    echo json_encode([
        'success' => false,
        'error' => [
            'code' => $validateApiKeyResult['error']['code'],
            'message' => $validateApiKeyResult['error']['message']
        ]
    ]);
    exit;
}

$update = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'success' => false,
        'error' => [
            'code' => 400,
            'message' => 'Invalid JSON format',
            'details' => json_last_error_msg()
        ]
    ]);
    exit;
}

$userId = (int)$validateApiKeyResult['userId'] ?? null;

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

