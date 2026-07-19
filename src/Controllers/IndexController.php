<?php
class IndexController {
    public function __invoke($params, $twig) {
        echo $twig->render('index.twig', [
            'buttons' => [
                [
                    'name' => 'Авторизация',
                    'href' => 'auth'
                ]
            ]
        ]);
        exit;
    }
}