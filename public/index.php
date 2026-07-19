<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../src/Views');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', 'IndexController');
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
        $controller = $routeInfo[1];
        $params = $routeInfo[2];
        
        require_once __DIR__ . "/../src/Controllers/{$controller}.php";
        
        $instance = new $controller();
        $instance($params, $twig);
        break;
}