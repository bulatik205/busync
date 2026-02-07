<?php
require_once '../../../config/config.php';
define('BASE_PATH', getBackPath(__DIR__));
header('Content-Type: application/json');

$sessionToken = null;

if (!isset($_GET['session_token'])) {
    echo json_encode([
        'success' => false,
        'error' => [
            'code' => 401,
            'message' => 'Unauthorized'
        ]
    ]);
    exit;
}

$sessionToken = $_GET['session_token'];

try {
    $stmt = $pdo->prepare('SELECT * FROM `user_sessions` WHERE `session_token` = ?');
    $stmt->execute([$sessionToken]);
    $result = $stmt->fetch();

    if ($result) {
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $result['id'],
                'user_id' => $result['user_id']
            ]
        ]);
        exit;
    } else {
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 401,
                'message' => 'Unauthorized'
            ]
        ]);
        exit;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => [
            'message' => 'Server error'
        ]
    ]);
    exit;
}