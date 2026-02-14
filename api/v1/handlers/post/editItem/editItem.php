<?php
class editItem {
    public array $inputs;
    private PDO $pdo;
    
    public function __construct(array $inputs, PDO $pdo) {
        $this->inputs = $inputs;
        $this->pdo = $pdo;
    }

    public function edit() {
        // todo
    }
}