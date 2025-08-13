<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch cart items from database
$sql = "SELECT c.id as cart_id, c.quantity, mi.id as menu_item_id, mi.name, mi.description, mi.price, mi.image, r.name as restaurant_name, r.id as restaurant_id 
        FROM cart c 
        JOIN menu_items mi ON c.menu_item_id = mi.id 
        JOIN restaurants r ON c.restaurant_id = r.id 
        WHERE c.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$cart_items_result = mysqli_stmt_get_result($stmt);

$cart_items = [];
$total = 0;
$cart_count = 0;
$restaurant_name = '';
$restaurant_id = 0;
$restaurant_ids = [];
while ($row = mysqli_fetch_assoc($cart_items_result)) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
    $cart_count += $row['quantity'];
    $restaurant_name = $row['restaurant_name'];
    $restaurant_id = $row['restaurant_id'];
    $restaurant_ids[$row['restaurant_id']] = true;
}

$multiple_restaurants = count($restaurant_ids) > 1;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
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
            width: 60px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 0.25rem;
        }
        .remove-btn {
            color: #dc3545;
            border: 1px solid #dc3545;
            background: white;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .remove-btn:hover {
            background: #dc3545;
            color: white;
        }
        .restaurant-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
        }
        .empty-cart i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        .sticky-summary {
            position: sticky;
            top: 100px;
        }
        .loading {
            text-align: center;
            padding: 2rem;
        }
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        
        /* Order Confirmation Modal Styles */
        .modal-lg {
            max-width: 800px;
        }
        
        .restaurant-info {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        
        .order-items .row {
            margin: 0;
        }
        
        .order-items .row:last-child {
            border-bottom: none !important;
        }
        
        .order-summary {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
        }
        
        .delivery-info {
            border-left: 4px solid #28a745;
        }
        
        .modal-footer .btn {
            min-width: 120px;
        }
        
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        /* Loading animation for confirm button */
        .btn:disabled {
            cursor: not-allowed;
        }
        
        /* Success animation */
        @keyframes successPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .success-animation {
            animation: successPulse 0.5s ease-in-out;
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
                        <a class="nav-link active" href="cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge bg-danger" id="cart-count">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="bg-light py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-shopping-cart me-2 text-primary"></i>Giỏ hàng
                    </h1>
                    <p class="text-muted mb-0">Quản lý các món ăn đã chọn</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="restaurants.php" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>Thêm món
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Content -->
    <div class="container mt-4">
        <?php if ($cart_count === 0): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h4 class="text-muted">Giỏ hàng trống</h4>
                <p class="text-muted">Bạn chưa có món ăn nào trong giỏ hàng</p>
                <a href="restaurants.php" class="btn btn-primary">
                    <i class="fas fa-utensils me-2"></i>Khám phá nhà hàng
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-store me-2"></i>
                                <?php echo htmlspecialchars($restaurant_name); ?>
                            </h5>
                        </div>
                        <div class="card-body" id="cartItems">
                            <?php if ($multiple_restaurants): ?>
                                <div class="restaurant-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Cảnh báo:</strong> Giỏ hàng của bạn có món ăn từ nhà hàng khác. Vui lòng xóa giỏ hàng cũ trước khi thêm món mới.
                                </div>
                            <?php endif; ?>
                            <?php foreach ($cart_items as $item): ?>
                                <div class="cart-item" data-cart-item-id="<?php echo $item['cart_id']; ?>">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <img src="<?php echo htmlspecialchars($item['image'] ?: 'assets/images/default-food.jpg'); ?>" 
                                                 class="cart-item-image" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                 onerror="this.src='assets/images/default-food.jpg'">
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            <small class="text-muted"><?php echo number_format($item['price'], 0, ',', '.'); ?> ₫</small>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="quantity-controls">
                                                <button class="quantity-btn" onclick="updateCartQuantity(<?php echo $item['cart_id']; ?>, Math.max(1, parseInt(this.nextElementSibling.value)-1))">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="quantity-input" value="<?php echo (int)$item['quantity']; ?>" 
                                                       min="1" max="99" onchange="updateCartQuantity(<?php echo $item['cart_id']; ?>, this.value)">
                                                <button class="quantity-btn" onclick="updateCartQuantity(<?php echo $item['cart_id']; ?>, Math.min(99, parseInt(this.previousElementSibling.value)+1))">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <strong class="text-primary item-price"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> ₫</strong>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button class="remove-btn" onclick="removeFromCart(<?php echo $item['cart_id']; ?>)" title="Xóa món">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card sticky-summary">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-receipt me-2 text-primary"></i>Tổng đơn hàng
                            </h5>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính:</span>
                                <span id="subtotal"><?php echo number_format($total, 0, ',', '.'); ?> ₫</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí giao hàng:</span>
                                <span>Miễn phí</span>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Tổng cộng:</strong>
                                <strong class="text-primary fs-5" id="cart-total"><?php echo number_format($total, 0, ',', '.'); ?> ₫</strong>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="checkout.php" class="btn btn-primary btn-lg" id="checkout-btn" <?php echo $total <= 0 ? 'disabled' : ''; ?>>
                                    <i class="fas fa-receipt me-2"></i>Tiến hành đặt hàng
                                </a>
                                <a href="restaurants.php" class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-2"></i>Thêm món
                                </a>
                            </div>
                            
                            <hr>
                            
                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Thời gian giao hàng: 30-45 phút
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Success Toast -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="successToast" class="toast" role="alert">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">Thành công!</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="body" id="toastMessage">
                Đã cập nhật giỏ hàng!
            </div>
        </div>
    </div>

    <!-- Order Confirmation Modal -->
    <div class="modal fade" id="orderConfirmationModal" tabindex="-1" aria-labelledby="orderConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="orderConfirmationModalLabel">
                        <i class="fas fa-receipt me-2"></i>Xác nhận đơn hàng
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Restaurant Info -->
                    <div class="restaurant-info mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-1">
                                    <i class="fas fa-store me-2 text-primary"></i>
                                    <span id="modalRestaurantName">Nhà hàng</span>
                                </h6>
                                <p class="text-muted mb-0" id="modalRestaurantAddress">Địa chỉ nhà hàng</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-clock me-1"></i>30-45 phút
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="order-items mb-4">
                        <h6 class="mb-3">
                            <i class="fas fa-utensils me-2 text-primary"></i>Chi tiết đơn hàng
                        </h6>
                        <div id="modalOrderItems">
                            <!-- Order items will be populated here -->
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="order-summary">
                        <h6 class="mb-3">
                            <i class="fas fa-calculator me-2 text-primary"></i>Tổng cộng
                        </h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tạm tính:</span>
                                    <span id="modalSubtotal">0 ₫</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Phí giao hàng:</span>
                                    <span class="text-success">Miễn phí</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Thuế VAT:</span>
                                    <span id="modalVAT">0 ₫</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong class="fs-5">Tổng cộng:</strong>
                                    <strong class="text-primary fs-5" id="modalTotal">0 ₫</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <i class="fas fa-credit-card fa-2x text-primary"></i>
                                    </div>
                                    <small class="text-muted">Thanh toán khi nhận hàng</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Info -->
                    <div class="delivery-info mt-4 p-3 bg-light rounded">
                        <h6 class="mb-2">
                            <i class="fas fa-truck me-2 text-primary"></i>Thông tin giao hàng
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Người nhận:</small>
                                <p class="mb-1 fw-bold"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Phương thức:</small>
                                <p class="mb-1 fw-bold">Giao hàng tận nơi</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Hủy bỏ
                    </button>
                    <button type="button" class="btn btn-primary" onclick="confirmOrder()">
                        <i class="fas fa-check me-2"></i>Xác nhận đặt hàng
                    </button>
                </div>
            </div>
        </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
