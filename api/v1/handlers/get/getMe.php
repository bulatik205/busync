<?php
class getMe {
    private string $user_id;
    private PDO $pdo;

    function __construct(string $user_id, PDO $pdo) {
        $this->user_id = $user_id;
        $this->pdo = $pdo;
    }

    function get() : array {
        $data = [];

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE `id` = ? LIMIT 1");
            $stmt->execute([$this->user_id]);
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