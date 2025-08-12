<?php

// Simple routing for development without Composer
require_once dirname(__DIR__).'/src/Controller/HomeController.php';
require_once dirname(__DIR__).'/src/Controller/SecurityController.php';
require_once dirname(__DIR__).'/src/Controller/RestaurantController.php';

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Simple routing
switch ($request_uri) {
    case '/':
        $controller = new HomeController();
        $response = $controller->index();
        break;
        
    case '/restaurants':
        $controller = new RestaurantController();
        $response = $controller->index();
        break;
        
    case '/login':
        $controller = new SecurityController();
        $response = $controller->login();
        break;
        
    case '/register':
        $controller = new SecurityController();
        $response = $controller->register();
        break;
        
    case '/logout':
        $controller = new SecurityController();
        $response = $controller->logout();
        break;
        
    default:
        // Check if it's a restaurant detail page
        if (preg_match('/^\/restaurants\/(\d+)$/', $request_uri, $matches)) {
            $restaurant_id = $matches[1];
            $controller = new RestaurantController();
            $response = $controller->show($restaurant_id);
        } else {
            http_response_code(404);
            $response = '
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .error-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            padding: 40px;
        }
        
        .error-icon {
            font-size: 6rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="error-card">
                    <div class="error-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h1 class="display-1 fw-bold text-danger mb-3">404</h1>
                    <h3 class="mb-3">Trang không tìm thấy!</h3>
                    <p class="text-muted mb-4">Trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển.</p>
                    <div class="d-grid gap-2">
                        <a href="/" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Về trang chủ
                        </a>
                        <a href="/restaurants" class="btn btn-outline-secondary">
                            <i class="fas fa-utensils me-2"></i>Xem nhà hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
        }
        break;
}

// Output response
if (isset($response)) {
    echo $response;
}
