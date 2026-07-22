<?php

namespace App\Controllers;

use Twig\Environment;

class IndexController 
{
    public function __construct(
        private Environment $twig
    ) {}
    
    public function __invoke(array $params = []): void 
    { 
        echo $this->twig->render('index.twig', [
            'buttons' => [
                [
                    'name' => 'Авторизация',
                    'href' => 'auth'
                ]
            ]
        ]);
    }
}