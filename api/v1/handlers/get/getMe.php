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
            $stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE `id` = ? LIMIT 1");
            $stmt->execute([$this->userId]);
            $data = $stmt->fetch();

            $data['success'] = true;
            return $data;
        } catch (PDOException $e) {
            $data['success'] = false;
            $data['error']['message'] = 'server_error';
            $data['error']['code'] = '500';
            return $data;
        }
    }
}