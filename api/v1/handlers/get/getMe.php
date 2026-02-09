<?php
class getMe {
    private int $userId;
    private PDO $pdo;

    function __construct(int $userId, PDO $pdo) {
        $this->userId = $userId;
        $this->pdo = $pdo;
    }

    function get() : array {
        $data = [];

        try {
            $stmt = $this->pdo->prepare("SELECT id, username, data FROM `users` WHERE `id` = ? LIMIT 1");
            $stmt->execute([$this->userId]);
            $SQLdata = $stmt->fetch();

            $data['success'] = true;
            $data['user']['user_id'] = $SQLdata['user_id'] ?? null;
            $data['user']['username'] = $SQLdata['username'] ?? null;
            $data['user']['registration_date'] = $SQLdata['data'] ?? null;
            return $data;
        } catch (PDOException $e) {
            $data['success'] = false;
            $data['error']['message'] = 'server_error';
            $data['error']['code'] = '500';
            return $data;
        }
    }
}