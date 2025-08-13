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
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Quản lý đơn hàng</h2>
    <table class="table table-bordered table-striped">
        <thead>
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
                        <button type="submit" class="btn btn-sm btn-primary">Cập nhật</button>
                    </form>
                </td>
                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                <td>
                    <a href="../order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary">Quay lại Dashboard</a>
</div>
</body>
</html>
