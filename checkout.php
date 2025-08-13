<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get cart items
$sql = "SELECT c.*, mi.name, mi.description, mi.price, mi.image, r.name as restaurant_name, r.id as restaurant_id 
        FROM cart c 
        JOIN menu_items mi ON c.menu_item_id = mi.id 
        JOIN restaurants r ON c.restaurant_id = r.id 
        WHERE c.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$cart_items = mysqli_stmt_get_result($stmt);

// Calculate total
$total = 0;
$cart_count = 0;
$restaurant_name = '';
$restaurant_id = 0;
while ($item = mysqli_fetch_assoc($cart_items)) {
    $total += $item['price'] * $item['quantity'];
    $cart_count += $item['quantity'];
    $restaurant_name = $item['restaurant_name'];
    $restaurant_id = $item['restaurant_id'];
}
mysqli_data_seek($cart_items, 0); // Reset pointer

// Check if cart is empty
if ($cart_count == 0) {
    header('Location: cart.php');
    exit();
}

// Get user information
$user_sql = "SELECT * FROM users WHERE id = ?";
$user_stmt = mysqli_prepare($conn, $user_sql);
mysqli_stmt_bind_param($user_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($user_stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($user_stmt));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $delivery_address = trim($_POST['delivery_address']);
    $delivery_phone = trim($_POST['delivery_phone']);
    $payment_method = $_POST['payment_method'];
    $notes = trim($_POST['notes']);
    
    // Validation
    if (empty($delivery_address) || empty($delivery_phone)) {
        $error = 'Vui lòng nhập đầy đủ thông tin giao hàng';
    } else {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Create order
            $order_sql = "INSERT INTO orders (user_id, restaurant_id, total, delivery_address, delivery_phone, payment_method, notes) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $order_stmt = mysqli_prepare($conn, $order_sql);
            mysqli_stmt_bind_param($order_stmt, "iidssss", $_SESSION['user_id'], $restaurant_id, $total, $delivery_address, $delivery_phone, $payment_method, $notes);
            
            if (mysqli_stmt_execute($order_stmt)) {
                $order_id = mysqli_insert_id($conn);
                
                // Insert order items
                $item_sql = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)";
                $item_stmt = mysqli_prepare($conn, $item_sql);
                
                while ($cart_item = mysqli_fetch_assoc($cart_items)) {
                    mysqli_stmt_bind_param($item_stmt, "iiid", $order_id, $cart_item['menu_item_id'], $cart_item['quantity'], $cart_item['price']);
                    mysqli_stmt_execute($item_stmt);
                }
                
                // Clear cart
                $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
                $clear_cart_stmt = mysqli_prepare($conn, $clear_cart_sql);
                mysqli_stmt_bind_param($clear_cart_stmt, "i", $_SESSION['user_id']);
                mysqli_stmt_execute($clear_cart_stmt);
                
                // Commit transaction
                mysqli_commit($conn);
                
                // Redirect to order confirmation
                header("Location: order_confirmation.php?id=$order_id");
                exit();
                
            } else {
                throw new Exception('Không thể tạo đơn hàng');
            }
            
        } catch (Exception $e) {
            // Rollback transaction
            mysqli_rollback($conn);
            $error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - Food Delivery</title>
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
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge bg-danger"><?php echo $cart_count; ?></span>
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
                        <i class="fas fa-credit-card me-2 text-primary"></i>Thanh toán
                    </h1>
                    <p class="text-muted mb-0">Hoàn tất thông tin đặt hàng</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="cart.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại giỏ hàng
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Thông tin giao hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" id="full_name" 
                                           value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="delivery_address" class="form-label">
                                    Địa chỉ giao hàng <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="delivery_address" name="delivery_address" rows="3" 
                                          placeholder="Nhập địa chỉ giao hàng chi tiết" required><?php echo isset($_POST['delivery_address']) ? htmlspecialchars($_POST['delivery_address']) : htmlspecialchars($user['address']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="delivery_phone" class="form-label">
                                    Số điện thoại giao hàng <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control" id="delivery_phone" name="delivery_phone" 
                                       placeholder="Nhập số điện thoại" 
                                       value="<?php echo isset($_POST['delivery_phone']) ? htmlspecialchars($_POST['delivery_phone']) : htmlspecialchars($user['phone']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Phương thức thanh toán</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="cash" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'cash') ? 'selected' : ''; ?>>
                                        <i class="fas fa-money-bill me-2"></i>Tiền mặt khi nhận hàng
                                    </option>
                                    <option value="bank_transfer" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'bank_transfer') ? 'selected' : ''; ?>>
                                        <i class="fas fa-university me-2"></i>Chuyển khoản ngân hàng
                                    </option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Ghi chú</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                          placeholder="Ghi chú thêm về đơn hàng (không bắt buộc)"><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check me-2"></i>Xác nhận đặt hàng
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>Chi tiết đơn hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Nhà hàng:</strong>
                            <p class="mb-0 text-muted"><?php echo htmlspecialchars($restaurant_name); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Số món:</strong>
                            <p class="mb-0 text-muted"><?php echo $cart_count; ?> món</p>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span><?php echo number_format($total, 0, ',', '.'); ?> ₫</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí giao hàng:</span>
                            <span>Miễn phí</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Tổng cộng:</strong>
                            <strong class="text-success fs-5"><?php echo number_format($total, 0, ',', '.'); ?> ₫</strong>
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
