<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $sql = "UPDATE orders SET status=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    mysqli_stmt_execute($stmt);
    header('Location: orders.php');
    exit();
}

// Get all orders with user and restaurant
$orders_sql = "SELECT o.*, u.username, r.name as restaurant_name FROM orders o JOIN users u ON o.user_id = u.id JOIN restaurants r ON o.restaurant_id = r.id ORDER BY o.created_at DESC";
$orders_result = mysqli_query($conn, $orders_sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block admin-sidebar collapse bg-primary">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">
                            <i class="fas fa-utensils me-2"></i>Admin Panel
                        </h4>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="users.php">
                                <i class="fas fa-users"></i> Quản lý người dùng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="restaurants.php">
                                <i class="fas fa-store"></i> Quản lý nhà hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-white bg-secondary" href="orders.php">
                                <i class="fas fa-shopping-cart"></i> Quản lý đơn hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="menu.php">
                                <i class="fas fa-utensils"></i> Quản lý thực đơn
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../index.php">
                                <i class="fas fa-home"></i> Về trang chủ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../logout.php">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-shopping-cart me-2"></i>Quản lý đơn hàng
                    </h1>
                </div>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Danh sách đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Khách hàng</th>
                                        <th>Nhà hàng</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày đặt</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['username']); ?></td>
                                        <td><?php echo htmlspecialchars($order['restaurant_name']); ?></td>
                                        <td><?php echo number_format($order['total'], 0, ',', '.'); ?> ₫</td>
                                        <td>
                                            <form method="POST" class="d-inline-flex">
                                                <input type="hidden" name="update_status" value="1">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <select name="status" class="form-select form-select-sm me-1">
                                                    <option value="pending" <?php if($order['status']=='pending') echo 'selected'; ?>>Chờ xác nhận</option>
                                                    <option value="confirmed" <?php if($order['status']=='confirmed') echo 'selected'; ?>>Đã xác nhận</option>
                                                    <option value="preparing" <?php if($order['status']=='preparing') echo 'selected'; ?>>Đang chuẩn bị</option>
                                                    <option value="delivering" <?php if($order['status']=='delivering') echo 'selected'; ?>>Đang giao hàng</option>
                                                    <option value="delivered" <?php if($order['status']=='delivered') echo 'selected'; ?>>Đã giao hàng</option>
                                                    <option value="cancelled" <?php if($order['status']=='cancelled') echo 'selected'; ?>>Đã hủy</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i></button>
                                            </form>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <a href="../order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Chi tiết</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <a href="dashboard.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left me-1"></i>Quay lại Dashboard</a>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
