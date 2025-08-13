<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if order ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: orders.php');
    exit();
}

$order_id = (int)$_GET['id'];

// Get order details
$sql = "SELECT o.*, r.name as restaurant_name, r.phone as restaurant_phone 
        FROM orders o 
        JOIN restaurants r ON o.restaurant_id = r.id 
        WHERE o.id = ? AND o.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$order) {
    header('Location: orders.php');
    exit();
}

// Get order items
$items_sql = "SELECT oi.*, mi.name, mi.description, mi.image 
              FROM order_items oi 
              JOIN menu_items mi ON oi.menu_item_id = mi.id 
              WHERE oi.order_id = ?";
$items_stmt = mysqli_prepare($conn, $items_sql);
mysqli_stmt_bind_param($items_stmt, "i", $order_id);
mysqli_stmt_execute($items_stmt);
$order_items = mysqli_stmt_get_result($items_stmt);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
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
                </ul>
            </div>
        </div>
    </nav>

    <!-- Success Message -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-success">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle fa-5x text-success"></i>
                        </div>
                        <h2 class="text-success mb-3">Đặt hàng thành công!</h2>
                        <p class="lead text-muted mb-4">
                            Cảm ơn bạn đã đặt hàng. Đơn hàng của bạn đã được xác nhận và đang được xử lý.
                        </p>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <strong>Mã đơn hàng:</strong> #<?php echo $order_id; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Chi tiết đơn hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Nhà hàng:</strong>
                                <p class="mb-0 text-muted"><?php echo htmlspecialchars($order['restaurant_name']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <strong>Ngày đặt:</strong>
                                <p class="mb-0 text-muted"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Địa chỉ giao hàng:</strong>
                                <p class="mb-0 text-muted"><?php echo htmlspecialchars($order['delivery_address']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <strong>Số điện thoại:</strong>
                                <p class="mb-0 text-muted"><?php echo htmlspecialchars($order['delivery_phone']); ?></p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Phương thức thanh toán:</strong>
                                <p class="mb-0 text-muted">
                                    <?php if ($order['payment_method'] == 'cash'): ?>
                                        <i class="fas fa-money-bill me-1"></i>Tiền mặt khi nhận hàng
                                    <?php else: ?>
                                        <i class="fas fa-university me-1"></i>Chuyển khoản ngân hàng
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong>Trạng thái:</strong>
                                <p class="mb-0">
                                    <span class="badge status-<?php echo $order['status']; ?>">
                                        <?php
                                        $status_labels = [
                                            'pending' => 'Chờ xác nhận',
                                            'confirmed' => 'Đã xác nhận',
                                            'preparing' => 'Đang chuẩn bị',
                                            'out_for_delivery' => 'Đang giao hàng',
                                            'delivered' => 'Đã giao hàng',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                        echo $status_labels[$order['status']] ?? $order['status'];
                                        ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                        
                        <?php if (!empty($order['notes'])): ?>
                            <div class="mb-3">
                                <strong>Ghi chú:</strong>
                                <p class="mb-0 text-muted"><?php echo htmlspecialchars($order['notes']); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <h6 class="mb-3">Các món đã đặt:</h6>
                        <?php while ($item = mysqli_fetch_assoc($order_items)): ?>
                            <div class="d-flex align-items-center mb-2">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                     class="rounded me-3" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     style="width: 50px; height: 50px; object-fit: cover;"
                                     onerror="this.src='assets/images/default-food.jpg'">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted"><?php echo htmlspecialchars($item['description']); ?></small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold"><?php echo number_format($item['price'], 0, ',', '.'); ?> ₫</div>
                                    <small class="text-muted">Số lượng: <?php echo $item['quantity']; ?></small>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Tổng cộng:</h5>
                            <h4 class="text-primary mb-0"><?php echo number_format($order['total'], 0, ',', '.'); ?> ₫</h4>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Thông tin giao hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Thời gian giao hàng:</strong>
                            <p class="mb-0 text-muted">30-45 phút</p>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Liên hệ nhà hàng:</strong>
                            <p class="mb-0 text-muted">
                                <i class="fas fa-phone me-1"></i>
                                <a href="tel:<?php echo htmlspecialchars($order['restaurant_phone']); ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($order['restaurant_phone']); ?>
                                </a>
                            </p>
                        </div>
                        
                        <hr>
                        
                        <div class="text-center">
                            <p class="text-muted small mb-2">
                                <i class="fas fa-clock me-1"></i>
                                Đơn hàng sẽ được xử lý trong thời gian sớm nhất
                            </p>
                            <div class="d-grid gap-2">
                                <a href="orders.php" class="btn btn-primary">
                                    <i class="fas fa-list me-2"></i>Xem tất cả đơn hàng
                                </a>
                                <a href="restaurants.php" class="btn btn-outline-primary">
                                    <i class="fas fa-utensils me-2"></i>Đặt thêm món
                                </a>
                            </div>
                        </div>
                    </div>
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
</body>
</html>
