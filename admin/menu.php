<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get all menu items with restaurant info
$menu_sql = "SELECT mi.*, r.name as restaurant_name, c.name as category_name FROM menu_items mi JOIN restaurants r ON mi.restaurant_id = r.id LEFT JOIN categories c ON mi.category_id = c.id ORDER BY mi.created_at DESC";
$menu_result = mysqli_query($conn, $menu_sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thực đơn - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Quản lý thực đơn</h2>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên món</th>
                    <th>Nhà hàng</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = mysqli_fetch_assoc($menu_result)): ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['restaurant_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                    <td><?php echo number_format($item['price'], 0, ',', '.'); ?> ₫</td>
                    <td><?php echo $item['available'] ? 'Còn bán' : 'Hết hàng'; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($item['created_at'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary">Quay lại Dashboard</a>
    </div>
</body>
</html>
