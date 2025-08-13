<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch orders from DB
$orders_sql = "SELECT o.*, r.name AS restaurant_name 
               FROM orders o 
               JOIN restaurants r ON o.restaurant_id = r.id 
               WHERE o.user_id = ? 
               ORDER BY o.created_at DESC";
$orders_stmt = mysqli_prepare($conn, $orders_sql);
mysqli_stmt_bind_param($orders_stmt, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($orders_stmt);
$orders_result = mysqli_stmt_get_result($orders_stmt);

// Preload order items grouped by order
$order_id_to_items = [];
if ($orders_result) {
    $order_ids = [];
    while ($o = mysqli_fetch_assoc($orders_result)) {
        $order_ids[] = (int)$o['id'];
    }
    // Rewind result set
    mysqli_data_seek($orders_result, 0);
    if (!empty($order_ids)) {
        $in_clause = implode(',', array_fill(0, count($order_ids), '?'));
        $types = str_repeat('i', count($order_ids));
        $items_sql = "SELECT oi.*, mi.name, mi.image 
                      FROM order_items oi 
                      JOIN menu_items mi ON oi.menu_item_id = mi.id 
                      WHERE oi.order_id IN ($in_clause)";
        $items_stmt = mysqli_prepare($conn, $items_sql);
        mysqli_stmt_bind_param($items_stmt, $types, ...$order_ids);
        mysqli_stmt_execute($items_stmt);
        $items_res = mysqli_stmt_get_result($items_stmt);
        while ($it = mysqli_fetch_assoc($items_res)) {
            $oid = (int)$it['order_id'];
            if (!isset($order_id_to_items[$oid])) $order_id_to_items[$oid] = [];
            $order_id_to_items[$oid][] = $it;
        }
    }
}
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
    <style>
        .order-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .order-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .order-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 1rem;
            border-radius: 10px 10px 0 0;
        }
        
        .order-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-preparing {
            background: #d4edda;
            color: #155724;
        }
        
        .status-delivering {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-delivered {
            background: #d1e7dd;
            color: #0f5132;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .order-items {
            padding: 1rem;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-item-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 1rem;
        }
        
        .order-summary {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0 0 10px 10px;
        }
        
        .empty-orders {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .empty-orders i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .loading {
            text-align: center;
            padding: 2rem;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        
        .order-timeline {
            padding: 1rem;
            border-left: 2px solid #dee2e6;
            margin-left: 1rem;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -0.5rem;
            top: 0.25rem;
            width: 0.75rem;
            height: 0.75rem;
            background: #007bff;
            border-radius: 50%;
        }
        
        .timeline-item.active::before {
            background: #28a745;
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

    <!-- Page Header -->
    <div class="bg-light py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-list-alt me-2 text-primary"></i>Đơn hàng của tôi
                    </h1>
                    <p class="text-muted mb-0">Theo dõi trạng thái đơn hàng</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="restaurants.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Đặt món mới
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Content -->
    <div class="container mt-4">
        <?php if (mysqli_num_rows($orders_result) === 0): ?>
            <div class="empty-orders">
                <i class="fas fa-clipboard-list"></i>
                <h4 class="text-muted">Chưa có đơn hàng nào</h4>
                <p class="text-muted">Bạn chưa đặt món ăn nào</p>
                <a href="restaurants.php" class="btn btn-primary">
                    <i class="fas fa-utensils me-2"></i>Khám phá nhà hàng
                </a>
            </div>
        <?php else: ?>
            <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="mb-1">
                                    <i class="fas fa-receipt me-2"></i>Đơn hàng #<?php echo (int)$order['id']; ?>
                                </h6>
                                <small>Đặt lúc: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></small>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <?php
                                $status_labels = [
                                    'pending' => 'status-pending',
                                    'confirmed' => 'status-confirmed',
                                    'preparing' => 'status-preparing',
                                    'delivering' => 'status-delivering',
                                    'delivered' => 'status-delivered',
                                    'cancelled' => 'status-cancelled'
                                ];
                                $label_class = $status_labels[$order['status']] ?? 'status-pending';
                                $status_text = [
                                    'pending' => 'Chờ xác nhận',
                                    'confirmed' => 'Đã xác nhận',
                                    'preparing' => 'Đang chuẩn bị',
                                    'delivering' => 'Đang giao hàng',
                                    'delivered' => 'Đã giao hàng',
                                    'cancelled' => 'Đã hủy'
                                ][$order['status']] ?? 'Chờ xác nhận';
                                ?>
                                <span class="order-status <?php echo $label_class; ?>"><?php echo $status_text; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="order-items">
                        <h6 class="mb-3">
                            <i class="fas fa-store me-2 text-primary"></i><?php echo htmlspecialchars($order['restaurant_name']); ?>
                        </h6>
                        <?php foreach ($order_id_to_items[(int)$order['id']] ?? [] as $item): ?>
                            <div class="order-item">
                                <img src="<?php echo htmlspecialchars($item['image'] ?: 'assets/images/default-food.jpg'); ?>" 
                                     class="order-item-image" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     onerror="this.src='assets/images/default-food.jpg'">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted">Số lượng: <?php echo (int)$item['quantity']; ?></small>
                                </div>
                                <div class="text-end">
                                    <strong><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> ₫</strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="order-summary">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="order-timeline">
                                    <div class="timeline-item<?php if(in_array($order['status'], ['confirmed','preparing','delivering','delivered'])) echo ' active'; ?>">
                                        <strong>Đơn hàng đã được xác nhận</strong>
                                        <br><small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></small>
                                    </div>
                                    <div class="timeline-item<?php if(in_array($order['status'], ['preparing','delivering','delivered'])) echo ' active'; ?>">
                                        <strong>Nhà hàng đang chuẩn bị</strong>
                                        <br><small class="text-muted">Dự kiến: 30-45 phút</small>
                                    </div>
                                    <div class="timeline-item<?php if(in_array($order['status'], ['delivering','delivered'])) echo ' active'; ?>">
                                        <strong>Đang giao hàng</strong>
                                        <br><small class="text-muted">Shipper đang đến</small>
                                    </div>
                                    <div class="timeline-item<?php if($order['status'] == 'delivered') echo ' active'; ?>">
                                        <strong>Giao hàng thành công</strong>
                                        <br><small class="text-muted">Đã hoàn thành</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-end">
                                    <div class="mb-2">
                                        <small class="text-muted">Tổng cộng:</small>
                                        <br><strong class="text-primary fs-5"><?php echo number_format($order['total'], 0, ',', '.'); ?> ₫</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
