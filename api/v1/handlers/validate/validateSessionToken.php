<?php
class validateSessionToken {
    private string $sessionToken;
    private PDO $pdo;

    function __construct(string $sessionToken, PDO $pdo) {
        $this->sessionToken = $sessionToken;
        $this->pdo = $pdo;
    }

    function validate() : array {
        $data = [];

        try {
            $stmt = $this->pdo->prepare("SELECT user_id FROM `users_sessions` WHERE `session_token` = ? LIMIT 1");
            $stmt->execute([$this->sessionToken]);
            $SQLdata = $stmt->fetch();

            if (empty($SQLdata)) {
                $data['success'] = false;
                $data['error']['message'] = 'invalid_session_token';
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