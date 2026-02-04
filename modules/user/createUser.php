<?php
class createUser
{
    public $sessionToken;
    public $username;
    public $passwordHash;
    public $pdo;

    function __construct(string $username, string $passwordHash, $pdo)
    {
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->pdo = $pdo;
    }

    public function create()
    {
        try {
            $this->sessionToken = bin2hex(random_bytes(16));
            $stmtCreateNewUser = $this->pdo->prepare(
                "INSERT INTO `users` (`session_token`, `username`, `password_hash`) 
                VALUES (?, ?, ?)"
            );
            $stmtCreateNewUser->execute([
                $this->sessionToken,
                $this->username,
                $this->passwordHash
            ]);
            unset($_SESSION['csrf_token']);

            $lastInsertId = $this->pdo->lastInsertId();

            if (!empty($lastInsertId)) {
                $_SESSION['username'] = $this->username;
                $_SESSION['session_token'] = $this->sessionToken;
                $_SESSION['user_id'] = $lastInsertId;

                return true;
            }

            return false;
        } catch (Exception $e) {
            unset($_SESSION['csrf_token']);
            databaseLog(htmlspecialchars($e->getMessage()), __DIR__);
            header('Location: ' . BASE_PATH . 'reg?error=server_error');
            exit;
        }
    }
}