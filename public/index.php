<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use DI\ContainerBuilder;
use App\Controllers\IndexController;
use App\Controllers\AuthController;

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);

$containerBuilder->addDefinitions([
    Twig\Environment::class => function() {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../src/Views');
        return new \Twig\Environment($loader, ['cache' => false]);
    },
    
    IndexController::class => DI\autowire(),
    AuthController::class => DI\autowire(),
]);

$container = $containerBuilder->build();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', IndexController::class);
    $r->addRoute('GET', '/auth', AuthController::class);    
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Not Found']);
        break;

    case FastRoute\Dispatcher::FOUND:
        $controllerClass = $routeInfo[1];
        $params = $routeInfo[2];
        
        try {
            $controller = $container->get($controllerClass);
            $controller($params);
        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;
}