<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get statistics
$stats = [];

// Total users
$users_sql = "SELECT COUNT(*) as count FROM users WHERE role = 'user'";
$users_result = mysqli_query($conn, $users_sql);
$stats['users'] = mysqli_fetch_assoc($users_result)['count'];

// Total restaurants
$restaurants_sql = "SELECT COUNT(*) as count FROM restaurants WHERE status = 'active'";
$restaurants_result = mysqli_query($conn, $restaurants_sql);
$stats['restaurants'] = mysqli_fetch_assoc($restaurants_result)['count'];

// Total orders
$orders_sql = "SELECT COUNT(*) as count FROM orders";
$orders_result = mysqli_query($conn, $orders_sql);
$stats['orders'] = mysqli_fetch_assoc($orders_result)['count'];

// Total revenue
$revenue_sql = "SELECT SUM(total_amount) as total FROM orders WHERE status = 'delivered'";
$revenue_result = mysqli_query($conn, $revenue_sql);
$stats['revenue'] = mysqli_fetch_assoc($revenue_result)['total'] ?: 0;

// Recent orders
$recent_orders_sql = "SELECT o.*, u.username, r.name as restaurant_name 
                      FROM orders o 
                      JOIN users u ON o.user_id = u.id 
                      JOIN restaurants r ON o.restaurant_id = r.id 
                      ORDER BY o.created_at DESC LIMIT 10";
$recent_orders = mysqli_query($conn, $recent_orders_sql);

// Recent users
$recent_users_sql = "SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT 5";
$recent_users = mysqli_query($conn, $recent_users_sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block admin-sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">
                            <i class="fas fa-utensils me-2"></i>Admin Panel
                        </h4>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users"></i>Quản lý người dùng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="restaurants.php">
                                <i class="fas fa-store"></i>Quản lý nhà hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">
                                <i class="fas fa-shopping-cart"></i>Quản lý đơn hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="menu.php">
                                <i class="fas fa-utensils"></i>Quản lý menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php">
                                <i class="fas fa-home"></i>Về trang chủ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-sign-out-alt"></i>Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <span class="text-muted">Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="stats-number"><?php echo number_format($stats['users']); ?></div>
                                    <div class="stats-label">Người dùng</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="stats-number"><?php echo number_format($stats['restaurants']); ?></div>
                                    <div class="stats-label">Nhà hàng</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-store fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="stats-number"><?php echo number_format($stats['orders']); ?></div>
                                    <div class="stats-label">Đơn hàng</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-shopping-cart fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="stats-number"><?php echo number_format($stats['revenue'], 0, ',', '.'); ?> ₫</div>
                                    <div class="stats-label">Doanh thu</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-money-bill-wave fa-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-clock me-2"></i>Đơn hàng gần đây
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Mã đơn hàng</th>
                                                <th>Khách hàng</th>
                                                <th>Nhà hàng</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái</th>
                                                <th>Ngày đặt</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                                                <tr>
                                                    <td>#<?php echo $order['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                                                    <td><?php echo htmlspecialchars($order['restaurant_name']); ?></td>
                                                    <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> ₫</td>
                                                    <td>
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
                                                    </td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                                    <td>
                                                        <a href="order_detail.php?id=<?php echo $order['id']; ?>" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="orders.php" class="btn btn-primary">
                                        <i class="fas fa-list me-2"></i>Xem tất cả đơn hàng
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-plus me-2"></i>Người dùng mới
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tên đăng nhập</th>
                                                <th>Họ và tên</th>
                                                <th>Email</th>
                                                <th>Ngày tạo</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($user = mysqli_fetch_assoc($recent_users)): ?>
                                                <tr>
                                                    <td><?php echo $user['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                                    <td>
                                                        <a href="user_detail.php?id=<?php echo $user['id']; ?>" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="users.php" class="btn btn-primary">
                                        <i class="fas fa-users me-2"></i>Xem tất cả người dùng
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
