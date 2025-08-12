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
$user_id = $_SESSION['user_id'];

// Get order details
$sql = "SELECT o.*, r.name as restaurant_name, r.image as restaurant_image, r.address as restaurant_address, r.phone as restaurant_phone
        FROM orders o 
        JOIN restaurants r ON o.restaurant_id = r.id 
        WHERE o.id = ? AND o.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$order = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($order) == 0) {
    header('Location: orders.php');
    exit();
}

$order = mysqli_fetch_assoc($order);

// Get order items
$sql = "SELECT oi.*, mi.name, mi.description, mi.image, mi.price
        FROM order_items oi 
        JOIN menu_items mi ON oi.menu_item_id = mi.id 
        WHERE oi.order_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$order_items = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?php echo $order_id; ?> - Food Delivery</title>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>
                        <i class="fas fa-receipt me-2"></i>Chi tiết đơn hàng #<?php echo $order_id; ?>
                    </h1>
                    <a href="orders.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>
                
                <div class="row">
                    <!-- Order Information -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Thông tin đơn hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Trạng thái:</strong> 
                                            <span class="badge bg-<?php echo getStatusColor($order['status']); ?>">
                                                <?php echo getStatusText($order['status']); ?>
                                            </span>
                                        </p>
                                        <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                                        <p><strong>Phương thức thanh toán:</strong> <?php echo getPaymentMethodText($order['payment_method']); ?></p>
                                        <p><strong>Trạng thái thanh toán:</strong> 
                                            <span class="badge bg-<?php echo $order['payment_status'] == 'paid' ? 'success' : 'warning'; ?>">
                                                <?php echo getPaymentStatusText($order['payment_status']); ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Địa chỉ giao hàng:</strong><br><?php echo $order['delivery_address']; ?></p>
                                        <p><strong>Số điện thoại:</strong> <?php echo $order['delivery_phone']; ?></p>
                                        <p><strong>Tổng tiền:</strong> <span class="text-primary fw-bold"><?php echo number_format($order['total_amount']); ?> VNĐ</span></p>
                                    </div>
                                </div>
                                
                                <?php if ($order['notes']): ?>
                                    <hr>
                                    <p><strong>Ghi chú:</strong><br><?php echo $order['notes']; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Order Items -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>Chi tiết món ăn
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php while ($item = mysqli_fetch_assoc($order_items)): ?>
                                    <div class="d-flex align-items-center mb-3 p-3 border rounded">
                                        <img src="<?php echo $item['image']; ?>" 
                                             alt="<?php echo $item['name']; ?>" 
                                             class="rounded me-3" 
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo $item['name']; ?></h6>
                                            <p class="text-muted mb-1"><?php echo $item['description']; ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted">Số lượng: <?php echo $item['quantity']; ?></span>
                                                <span class="fw-bold"><?php echo number_format($item['price'] * $item['quantity']); ?> VNĐ</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Restaurant Information -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-store me-2"></i>Thông tin nhà hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <img src="<?php echo $order['restaurant_image']; ?>" 
                                         alt="<?php echo $order['restaurant_name']; ?>" 
                                         class="rounded mb-3" 
                                         style="width: 100%; max-width: 200px; height: 150px; object-fit: cover;">
                                    <h6><?php echo $order['restaurant_name']; ?></h6>
                                </div>
                                
                                <p><strong>Địa chỉ:</strong><br><?php echo $order['restaurant_address']; ?></p>
                                <p><strong>Điện thoại:</strong> <?php echo $order['restaurant_phone']; ?></p>
                                
                                <a href="restaurant.php?id=<?php echo $order['restaurant_id']; ?>" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-utensils me-2"></i>Xem menu
                                </a>
                            </div>
                        </div>
                        
                        <!-- Order Timeline -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-clock me-2"></i>Tiến trình đơn hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <?php
                                    $statuses = [
                                        'pending' => ['icon' => 'clock', 'text' => 'Chờ xác nhận', 'time' => $order['created_at']],
                                        'confirmed' => ['icon' => 'check-circle', 'text' => 'Đã xác nhận', 'time' => ''],
                                        'preparing' => ['icon' => 'utensils', 'text' => 'Đang chuẩn bị', 'time' => ''],
                                        'out_for_delivery' => ['icon' => 'truck', 'text' => 'Đang giao hàng', 'time' => ''],
                                        'delivered' => ['icon' => 'home', 'text' => 'Đã giao hàng', 'time' => '']
                                    ];
                                    
                                    $current_status = $order['status'];
                                    $found_current = false;
                                    
                                    foreach ($statuses as $status => $info):
                                        $is_active = $status === $current_status;
                                        $is_completed = array_search($status, array_keys($statuses)) < array_search($current_status, array_keys($statuses));
                                        
                                        if ($is_active) $found_current = true;
                                    ?>
                                        <div class="timeline-item <?php echo $is_completed ? 'completed' : ($is_active ? 'active' : ''); ?>">
                                            <div class="timeline-icon">
                                                <i class="fas fa-<?php echo $info['icon']; ?>"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1"><?php echo $info['text']; ?></h6>
                                                <?php if ($info['time']): ?>
                                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($info['time'])); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
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

function getPaymentStatusText($status) {
    switch ($status) {
        case 'pending': return 'Chờ thanh toán';
        case 'paid': return 'Đã thanh toán';
        case 'failed': return 'Thanh toán thất bại';
        default: return 'Không xác định';
    }
}
?>
