<?php
class validateInputs {
    public array $inputs;
    private PDO $pdo;

    public function __construct(array $inputs, PDO $pdo) {
        $this->inputs = $inputs;
        $this->pdo = $pdo;
    }

    public function validate() : array {
        $data = [];
        $errors = [];

        $rules = [
            'item_name' => [
                'required' => true,
                'minLength' => 4,
                'maxLength' => 255,
                'regex' => '/^[а-яА-Яa-zA-Z0-9\s.,\-()\/]+$/'
            ],
            'item_description' => [
                'required' => false,
                'minLength' => 4,
                'maxLength' => 10000
            ],
            'item_art' => [
                'required' => false,
                'minLength' => 4,
                'maxLength' => 255,
                'regex' => '#^[а-яА-Яa-zA-Z0-9\s.,\-()&*/]+$#u'
            ],
            'item_category' => [
                'required' => false,
                'minLength' => 4,
                'maxLength' => 100,
                'regex' => '#^[а-яА-Яa-zA-Z0-9\s.,\-()&*/]+$#u'
            ],
            'item_cost' => [
                'required' => true,
                'demical' => 2,
                'filter' => FILTER_VALIDATE_FLOAT,
                'min' => 0.01,
                'max' => 999999.98
            ],
            'item_retail' => [
                'required' => true,
                'demical' => 2,
                'filter' => FILTER_VALIDATE_FLOAT,
                'min' => 0.02,
                'max' => 999999.99
            ],
            'item_manufacturer' => [
                'required' => false,
                'minLength' => 4,
                'maxLength' => 255,
                'regex' => '#^[а-яА-Яa-zA-Z0-9\s.,\-()&*/]+$#u'
            ],
            'item_remain' => [
                'required' => false,
                'minLength' => 4,
                'maxLength' => 255,
                'type' => 'int',
                'regex' => '/^[0-9]+$/'
            ],
            'item_unit' => [
                'required' => false,
                'minLength' => 4,
                'maxLength' => 255,
                'regex' => '/^[а-яА-Яa-zA-Z0-9\s.,*]+$/'
            ],
            'item_status' => [
                'required' => false,
                'minLength' => 4,
                'maxLength' => 255,
                'regex' => '/^[а-яА-Яa-zA-Z]+$/'
            ],
        ];
    }
}