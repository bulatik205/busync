<?php
class validateApiKey {
    private string $apiKey;
    private PDO $pdo;

    function __construct(string $apiKey, PDO $pdo) {
        $this->apiKey = $apiKey;
        $this->pdo = $pdo;
    }

    function validate() : array {
        $data = [];

        try {
            $stmt = $this->pdo->prepare("SELECT user_id FROM `users_api` WHERE `session_token` = ? LIMIT 1");
            $stmt->execute([$this->apiKey]);
            $SQLdata = $stmt->fetch();

            if (empty($SQLdata)) {
                $data['success'] = false;
                $data['error']['message'] = 'invalid_api_key';
                $data['error']['code'] = 401;
                return $data;
            }

            $data['success'] = true; 
            $data['userId'] = $SQLdata['user_id'];
            return $data;
        } catch (PDOException $e) {
            exceptionLog($e->getMessage(), __DIR__);
            $data['success'] = false;
            $data['error']['message'] = 'server_error';
            $data['error']['code'] = 500;
            return $data;
        }
    }
}