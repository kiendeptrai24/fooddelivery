<?php
session_start();
require_once 'config/database.php';

// Get featured restaurants
$featured_restaurants = [];
$sql = "SELECT * FROM restaurants WHERE featured = 1 LIMIT 6";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $featured_restaurants[] = $row;
    }
}

// Get popular categories
$categories = [
    ['name' => 'Món Việt', 'icon' => 'fas fa-flag', 'color' => 'primary'],
    ['name' => 'Món Á', 'icon' => 'fas fa-utensils', 'color' => 'success'],
    ['name' => 'Món Âu', 'icon' => 'fas fa-cheese', 'color' => 'warning'],
    ['name' => 'Đồ uống', 'icon' => 'fas fa-coffee', 'color' => 'info'],
    ['name' => 'Tráng miệng', 'icon' => 'fas fa-ice-cream', 'color' => 'danger'],
    ['name' => 'Đồ chay', 'icon' => 'fas fa-leaf', 'color' => 'success']
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Delivery - Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .restaurant-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .restaurant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .category-card {
            text-align: center;
            padding: 30px 20px;
            border-radius: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-section {
            background: #f8f9fa;
            padding: 80px 0;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-utensils me-2"></i>Food Delivery
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="restaurants.php">Nhà hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Đơn hàng</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">
                                <i class="fas fa-shopping-cart me-1"></i>Giỏ hàng
                                <span class="badge bg-warning text-dark ms-1" id="cart-count">0</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php">Hồ sơ</a></li>
                                <li><a class="dropdown-item" href="orders.php">Đơn hàng</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Đăng ký</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        Đặt đồ ăn ngon tại nhà
                    </h1>
                    <p class="lead mb-4">
                        Khám phá hàng trăm món ăn ngon từ các nhà hàng uy tín.
                        Đặt hàng dễ dàng, giao hàng nhanh chóng!
                    </p>
                    <div class="d-flex gap-3">
                        <a href="restaurants.php" class="btn btn-warning btn-lg">
                            <i class="fas fa-utensils me-2"></i>Đặt hàng ngay
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-info-circle me-2"></i>Tìm hiểu thêm
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="https://via.placeholder.com/500x400/FF6B6B/FFFFFF?text=Food+Delivery" 
                         alt="Food Delivery" class="img-fluid" style="max-width: 500px; border-radius: 20px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Tại sao chọn chúng tôi?</h2>
                <p class="lead text-muted">Dịch vụ giao đồ ăn tốt nhất với nhiều ưu đãi</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-clock fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Giao hàng nhanh</h5>
                            <p class="card-text">Giao hàng trong vòng 30-45 phút, đảm bảo đồ ăn còn nóng hổi</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-utensils fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title">Đa dạng món ăn</h5>
                            <p class="card-text">Hàng trăm món ăn từ các nhà hàng uy tín, đáp ứng mọi khẩu vị</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-mobile-alt fa-3x text-warning"></i>
                            </div>
                            <h5 class="card-title">Đặt hàng dễ dàng</h5>
                            <p class="card-text">Giao diện thân thiện, dễ sử dụng trên mọi thiết bị</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Danh mục món ăn</h2>
                <p class="lead text-muted">Khám phá các loại món ăn đa dạng</p>
            </div>
            <div class="row">
                <?php foreach ($categories as $category): ?>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="category-card bg-white shadow-sm" onclick="location.href='restaurants.php?category=<?php echo urlencode($category['name']); ?>'">
                        <i class="fas <?php echo $category['icon']; ?> fa-2x text-<?php echo $category['color']; ?> mb-3"></i>
                        <h6 class="fw-bold"><?php echo $category['name']; ?></h6>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Restaurants -->
    <?php if (!empty($featured_restaurants)): ?>
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Nhà hàng nổi bật</h2>
                <p class="lead text-muted">Những nhà hàng được yêu thích nhất</p>
            </div>
            <div class="row">
                <?php foreach ($featured_restaurants as $restaurant): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card restaurant-card h-100">
                        <img src="<?php echo !empty($restaurant['image']) ? $restaurant['image'] : 'https://via.placeholder.com/300x200/FF6B6B/FFFFFF?text=Restaurant'; ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($restaurant['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($restaurant['name']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($restaurant['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary"><?php echo htmlspecialchars($restaurant['cuisine_type']); ?></span>
                                <a href="restaurants.php?id=<?php echo $restaurant['id']; ?>" class="btn btn-outline-primary btn-sm">Xem menu</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="restaurants.php" class="btn btn-primary btn-lg">Xem tất cả nhà hàng</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <p class="text-muted">Món ăn</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">50+</div>
                        <p class="text-muted">Nhà hàng</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">1000+</div>
                        <p class="text-muted">Khách hàng</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">30</div>
                        <p class="text-muted">Phút giao hàng</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-utensils me-2"></i>Food Delivery</h5>
                    <p class="text-muted">Dịch vụ giao đồ ăn tốt nhất với nhiều ưu đãi</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted">&copy; 2024 Food Delivery. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Load cart count if user is logged in
        <?php if (isset($_SESSION['user_id'])): ?>
        fetch('get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('cart-count').textContent = data.count;
            })
            .catch(error => console.error('Error:', error));
        <?php endif; ?>
    </script>
</body>
</html>
