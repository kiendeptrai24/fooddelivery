<?php
session_start();
require_once 'config/database.php';

// Get restaurant ID from URL
$restaurant_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$restaurant_id) {
    header('Location: restaurants.php');
    exit();
}

// Get restaurant information
$restaurant_sql = "SELECT * FROM restaurants WHERE id = ? AND status = 'active'";
$restaurant_stmt = mysqli_prepare($conn, $restaurant_sql);
mysqli_stmt_bind_param($restaurant_stmt, 'i', $restaurant_id);
mysqli_stmt_execute($restaurant_stmt);
$restaurant_result = mysqli_stmt_get_result($restaurant_stmt);

if (mysqli_num_rows($restaurant_result) == 0) {
    header('Location: restaurants.php');
    exit();
}

$restaurant = mysqli_fetch_assoc($restaurant_result);

// Get menu items for this restaurant
$menu_sql = "SELECT * FROM menu_items WHERE restaurant_id = ? ORDER BY name";
$menu_stmt = mysqli_prepare($conn, $menu_sql);
mysqli_stmt_bind_param($menu_stmt, 'i', $restaurant_id);
mysqli_stmt_execute($menu_stmt);
$menu_result = mysqli_stmt_get_result($menu_stmt);

// Group menu items by category (if category exists) or just show all items
$menu_categories = [];
$categories = [];

// Check if category column exists
$check_category_sql = "SHOW COLUMNS FROM menu_items LIKE 'category'";
$check_category_result = mysqli_query($conn, $check_category_sql);

if (mysqli_num_rows($check_category_result) > 0) {
    // Category column exists, group by category
    while ($item = mysqli_fetch_assoc($menu_result)) {
        $category = $item['category'] ?: 'Khác';
        if (!isset($menu_categories[$category])) {
            $menu_categories[$category] = [];
        }
        $menu_categories[$category][] = $item;
    }
    
    // Get unique categories for filter
    $categories_sql = "SELECT DISTINCT category FROM menu_items WHERE restaurant_id = ? AND category IS NOT NULL ORDER BY category";
    $categories_stmt = mysqli_prepare($conn, $categories_sql);
    mysqli_stmt_bind_param($categories_stmt, 'i', $restaurant_id);
    mysqli_stmt_execute($categories_stmt);
    $categories_result = mysqli_stmt_get_result($categories_stmt);
    while ($row = mysqli_fetch_assoc($categories_result)) {
        $categories[] = $row['category'];
    }
} else {
    // Category column doesn't exist, show all items in one group
    $menu_categories['Tất cả món'] = [];
    while ($item = mysqli_fetch_assoc($menu_result)) {
        $menu_categories['Tất cả món'][] = $item;
    }
    $categories = ['Tất cả món'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurant['name']); ?> - Menu - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .menu-item-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .menu-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .menu-item-image {
            height: 200px;
            object-fit: cover;
        }
        .category-section {
            margin-bottom: 3rem;
        }
        .category-title {
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .quantity-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid #dee2e6;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .quantity-btn:hover {
            background: #f8f9fa;
            border-color: #007bff;
        }
        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 0.25rem;
        }
        .add-to-cart-btn {
            min-width: 120px;
        }
        .restaurant-hero {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('<?php echo htmlspecialchars($restaurant['image']); ?>');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 4rem 0;
        }
        .filter-buttons {
            margin-bottom: 2rem;
        }
        .filter-btn {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .filter-btn.active {
            background-color: #007bff;
            border-color: #007bff;
        }
        
        /* Floating Cart Styles */
        .floating-cart {
            position: fixed;
            top: 100px;
            right: 20px;
            width: 350px;
            max-height: 500px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
            z-index: 1000;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .floating-cart.collapsed {
            width: 60px;
            height: 60px;
        }
        
        .cart-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 15px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .cart-header.collapsed {
            justify-content: center;
        }
        
        .cart-title {
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .cart-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
        }
        
        .cart-body {
            padding: 15px;
            max-height: 350px;
            overflow-y: auto;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-item-info {
            flex: 1;
            margin-left: 10px;
        }
        
        .cart-item-name {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .cart-item-price {
            color: #28a745;
            font-weight: bold;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .cart-quantity-btn {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 1px solid #dee2e6;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
        }
        
        .cart-quantity-input {
            width: 40px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 2px;
            font-size: 12px;
        }
        
        .cart-footer {
            padding: 15px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        
        .cart-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .cart-actions {
            display: flex;
            gap: 10px;
        }
        
        .cart-actions button {
            flex: 1;
        }
        
        .cart-empty {
            text-align: center;
            padding: 30px 15px;
            color: #6c757d;
        }
        
        .cart-empty i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .restaurant-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .floating-cart {
                position: fixed;
                bottom: 20px;
                right: 20px;
                top: auto;
                width: 300px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
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
                        <a class="nav-link" href="index.php">Trang chủ</a>
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
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo $_SESSION['username']; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php">Hồ sơ</a></li>
                                <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="badge bg-danger" id="cart-count">0</span>
                            </a>
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

    <!-- Restaurant Hero Section -->
    <div class="restaurant-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4 mb-3"><?php echo htmlspecialchars($restaurant['name']); ?></h1>
                    <p class="lead mb-3"><?php echo htmlspecialchars($restaurant['description']); ?></p>
                    <div class="d-flex flex-wrap gap-3 mb-3">
                        <span class="badge bg-success fs-6">
                            <i class="fas fa-utensils me-2"></i>
                            <?php echo htmlspecialchars($restaurant['cuisine']); ?>
                        </span>
                        <span class="badge bg-info fs-6">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <?php echo htmlspecialchars($restaurant['address']); ?>
                        </span>
                        <span class="badge bg-warning fs-6">
                            <i class="fas fa-phone me-2"></i>
                            <?php echo htmlspecialchars($restaurant['phone']); ?>
                        </span>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="restaurants.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Filter Buttons -->
    <div class="container mt-4">
        <div class="filter-buttons">
            <button class="btn btn-outline-primary filter-btn active" data-category="all">
                <i class="fas fa-list me-2"></i>Tất cả
            </button>
            <?php foreach ($categories as $category): ?>
                <button class="btn btn-outline-primary filter-btn" data-category="<?php echo htmlspecialchars($category); ?>">
                    <?php echo htmlspecialchars($category); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Menu Items -->
    <div class="container">
        <?php if (!empty($menu_categories)): ?>
            <?php foreach ($menu_categories as $category => $items): ?>
                <div class="category-section" data-category="<?php echo htmlspecialchars($category); ?>">
                    <h2 class="category-title">
                        <i class="fas fa-utensils me-2 text-primary"></i>
                        <?php echo htmlspecialchars($category); ?>
                    </h2>
                    <div class="row">
                        <?php foreach ($items as $item): ?>
                            <div class="col-md-6 col-lg-4 mb-4 menu-item" data-category="<?php echo htmlspecialchars($category); ?>">
                                <div class="card h-100 menu-item-card">
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                         class="card-img-top menu-item-image" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         onerror="this.src='assets/images/default-food.jpg'">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                        <p class="card-text text-muted flex-grow-1">
                                            <?php echo htmlspecialchars($item['description']); ?>
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="price"><?php echo number_format($item['price']); ?>đ</span>
                                            <?php if (isset($item['featured']) && $item['featured']): ?>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-star me-1"></i>Nổi bật
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <div class="quantity-controls mb-3">
                                                <button class="quantity-btn" onclick="changeQuantity(<?php echo $item['id']; ?>, -1)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="quantity-input" 
                                                       id="quantity-<?php echo $item['id']; ?>" 
                                                       value="1" min="1" max="99">
                                                <button class="quantity-btn" onclick="changeQuantity(<?php echo $item['id']; ?>, 1)">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            
                                            <button class="btn btn-primary add-to-cart-btn w-100" 
                                                    onclick="addToCart(<?php echo $item['id']; ?>, <?php echo $restaurant_id; ?>)">
                                                <i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ
                                            </button>
                                        <?php else: ?>
                                            <div class="text-center">
                                                <a href="login.php" class="btn btn-outline-primary">
                                                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập để đặt hàng
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Chưa có món ăn nào</h4>
                <p class="text-muted">Nhà hàng này chưa cập nhật menu</p>
                <a href="restaurants.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Xem nhà hàng khác
                </a>
            </div>
        <?php endif; ?>
    </div>

    

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Food Delivery</h5>
                    <p>Đặt đồ ăn ngon tại nhà</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2024 Food Delivery. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Success Toast -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="successToast" class="toast" role="alert">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">Thành công!</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                Đã thêm món ăn vào giỏ hàng!
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Category filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const category = this.dataset.category;
                document.querySelectorAll('.category-section').forEach(section => {
                    section.style.display = (category === 'all' || section.dataset.category === category) ? 'block' : 'none';
                });
            });
        });
        // Quantity control for menu cards
        function changeQuantity(itemId, change) {
            const input = document.getElementById(`quantity-${itemId}`);
            let newValue = parseInt(input.value || '1') + change;
            if (newValue < 1) newValue = 1;
            if (newValue > 99) newValue = 99;
            input.value = newValue;
        }
    </script>
</body>
</html>
