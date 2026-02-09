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
            $stmt = $this->pdo->prepare("SELECT * FROM `users_sessions` WHERE `session_token` = ? LIMIT 1");
            $stmt->execute([$this->sessionToken]);
            $data = $stmt->fetch();

            if (empty($data)) {
                $data['success'] = false;
                $data['error']['message'] = 'invalid_session_token';
                $data['error']['code'] = 401;
                return $data;
            }

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