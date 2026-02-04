<?php
class validateUserExist {
    public $username;
    public $pdo;

    function __construct(string $username, $pdo) {
        $this->username = $username;
        $this->pdo = $pdo;
    }

    function validate() : bool {
        $stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $stmt->execute([$this->username]);
        $userExist = $stmt->fetch();

        if ($userExist) {
            return true;
        } 

        return false;
    }
}