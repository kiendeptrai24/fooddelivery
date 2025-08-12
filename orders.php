<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user orders
$user_id = $_SESSION['user_id'];
$sql = "SELECT o.*, r.name as restaurant_name, r.image as restaurant_image 
        FROM orders o 
        JOIN restaurants r ON o.restaurant_id = r.id 
        WHERE o.user_id = ? 
        ORDER BY o.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$orders = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của tôi - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
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
                        <a class="nav-link active" href="orders.php">Đơn hàng</a>
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
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge bg-danger" id="cart-count">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-list-alt me-2"></i>Đơn hàng của tôi
                </h1>
                
                <?php if (mysqli_num_rows($orders) == 0): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Bạn chưa có đơn hàng nào</h4>
                        <p class="text-muted">Hãy đặt món ăn ngon từ các nhà hàng của chúng tôi!</p>
                        <a href="restaurants.php" class="btn btn-primary">
                            <i class="fas fa-utensils me-2"></i>Xem nhà hàng
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 order-card">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-primary">#<?php echo $order['id']; ?></span>
                                            <span class="badge bg-<?php echo getStatusColor($order['status']); ?>">
                                                <?php echo getStatusText($order['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="<?php echo $order['restaurant_image']; ?>" 
                                                 alt="<?php echo $order['restaurant_name']; ?>" 
                                                 class="rounded me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0"><?php echo $order['restaurant_name']; ?></h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <strong>Tổng tiền:</strong> 
                                            <span class="text-primary"><?php echo number_format($order['total_amount']); ?> VNĐ</span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <strong>Địa chỉ giao hàng:</strong><br>
                                            <small class="text-muted"><?php echo $order['delivery_address']; ?></small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <strong>Phương thức thanh toán:</strong><br>
                                            <span class="badge bg-secondary">
                                                <?php echo getPaymentMethodText($order['payment_method']); ?>
                                            </span>
                                        </div>
                                        
                                        <?php if ($order['notes']): ?>
                                            <div class="mb-3">
                                                <strong>Ghi chú:</strong><br>
                                                <small class="text-muted"><?php echo $order['notes']; ?></small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer">
                                        <a href="order_details.php?id=<?php echo $order['id']; ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
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

<?php
function getStatusColor($status) {
    switch ($status) {
        case 'pending': return 'warning';
        case 'confirmed': return 'info';
        case 'preparing': return 'primary';
        case 'out_for_delivery': return 'info';
        case 'delivered': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

function getStatusText($status) {
    switch ($status) {
        case 'pending': return 'Chờ xác nhận';
        case 'confirmed': return 'Đã xác nhận';
        case 'preparing': return 'Đang chuẩn bị';
        case 'out_for_delivery': return 'Đang giao hàng';
        case 'delivered': return 'Đã giao hàng';
        case 'cancelled': return 'Đã hủy';
        default: return 'Không xác định';
    }
}

function getPaymentMethodText($method) {
    switch ($method) {
        case 'cash': return 'Tiền mặt';
        case 'bank_transfer': return 'Chuyển khoản';
        default: return 'Không xác định';
    }
}
?>
