<?php
class IndexController {
    private $twig;
    
    public function __construct(Twig\Environment $twig) {
        $this->twig = $twig;
    }
    
    public function __invoke($params) { 
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