<?php

// Simple routing for development without Composer
require_once dirname(__DIR__).'/src/Controller/HomeController.php';

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Simple routing
switch ($request_uri) {
    case '/':
        $controller = new HomeController();
        $response = $controller->index();
        break;
    case '/restaurants':
        // TODO: Implement RestaurantController
        echo "Restaurants page - Coming soon!";
        exit;
    case '/login':
        // TODO: Implement SecurityController
        echo "Login page - Coming soon!";
        exit;
    case '/register':
        // TODO: Implement SecurityController
        echo "Register page - Coming soon!";
        exit;
    default:
        http_response_code(404);
        echo "404 - Page not found";
        exit;
}

// Output response
if (isset($response)) {
    echo $response;
}
