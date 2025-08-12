<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get cart items
$sql = "SELECT c.*, mi.name, mi.description, mi.price, mi.image, r.name as restaurant_name 
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
while ($item = mysqli_fetch_assoc($cart_items)) {
    $total += $item['price'] * $item['quantity'];
    $cart_count += $item['quantity'];
    $restaurant_name = $item['restaurant_name'];
}
mysqli_data_seek($cart_items, 0); // Reset pointer

// Handle quantity updates and removals
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $item_id = (int)$_POST['item_id'];
        
        if ($_POST['action'] == 'update') {
            $quantity = (int)$_POST['quantity'];
            if ($quantity > 0) {
                $update_sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "iii", $quantity, $item_id, $_SESSION['user_id']);
                mysqli_stmt_execute($update_stmt);
            } else {
                $delete_sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
                $delete_stmt = mysqli_prepare($conn, $delete_sql);
                mysqli_stmt_bind_param($delete_stmt, "ii", $item_id, $_SESSION['user_id']);
                mysqli_stmt_execute($delete_stmt);
            }
        } elseif ($_POST['action'] == 'remove') {
            $delete_sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
            $delete_stmt = mysqli_prepare($conn, $delete_sql);
            mysqli_stmt_bind_param($delete_stmt, "ii", $item_id, $_SESSION['user_id']);
            mysqli_stmt_execute($delete_stmt);
        }
        
        // Redirect to refresh the page
        header('Location: cart.php');
        exit();
    }
}
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
        <?php if ($cart_count > 0): ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-store me-2"></i><?php echo htmlspecialchars($restaurant_name); ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php while ($item = mysqli_fetch_assoc($cart_items)): ?>
                                <div class="cart-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                 class="img-fluid rounded" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                 onerror="this.src='assets/images/default-food.jpg'">
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            <p class="text-muted small mb-0">
                                                <?php echo htmlspecialchars($item['description']); ?>
                                            </p>
                                            <small class="text-muted">
                                                <?php echo number_format($item['price'], 0, ',', '.'); ?> ₫
                                            </small>
                                        </div>
                                        <div class="col-md-3">
                                            <form method="POST" action="" class="d-flex align-items-center">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                <div class="input-group input-group-sm" style="width: 120px;">
                                                    <button type="button" class="btn btn-outline-secondary quantity-btn" 
                                                            data-action="decrease" data-item-id="<?php echo $item['id']; ?>">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                           min="1" max="99" class="form-control text-center quantity-input">
                                                    <button type="button" class="btn btn-outline-secondary quantity-btn" 
                                                            data-action="increase" data-item-id="<?php echo $item['id']; ?>">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <strong class="text-primary">
                                                <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> ₫
                                            </strong>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <form method="POST" action="" style="display: inline;">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                        onclick="return confirm('Bạn có chắc muốn xóa món này?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card sticky-top" style="top: 100px;">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-receipt me-2 text-primary"></i>Tổng đơn hàng
                            </h5>
                            
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
                                <strong class="text-primary fs-5"><?php echo number_format($total, 0, ',', '.'); ?> ₫</strong>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="checkout.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-credit-card me-2"></i>Tiến hành đặt hàng
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
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Giỏ hàng trống</h4>
                <p class="text-muted">Bạn chưa có món ăn nào trong giỏ hàng</p>
                <a href="restaurants.php" class="btn btn-primary">
                    <i class="fas fa-utensils me-2"></i>Khám phá nhà hàng
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Quantity buttons functionality
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                const action = this.dataset.action;
                const itemId = this.dataset.itemId;
                const input = this.parentNode.querySelector('.quantity-input');
                let quantity = parseInt(input.value);
                
                if (action === 'increase') {
                    quantity = Math.min(99, quantity + 1);
                } else if (action === 'decrease') {
                    quantity = Math.max(1, quantity - 1);
                }
                
                input.value = quantity;
                
                // Auto-submit form to update quantity
                const form = input.closest('form');
                form.submit();
            });
        });
        
        // Auto-submit form when quantity input changes
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                const form = this.closest('form');
                form.submit();
            });
        });
    </script>
</body>
</html>
