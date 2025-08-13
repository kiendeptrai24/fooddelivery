<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM menu_items WHERE id = $id");
    header('Location: menu.php');
    exit();
}

// Handle add menu item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_menu'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $restaurant_id = intval($_POST['restaurant_id']);
    $sql = "INSERT INTO menu_items (name, description, price, category_id, restaurant_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdii", $name, $description, $price, $category_id, $restaurant_id);
    mysqli_stmt_execute($stmt);
    header('Location: menu.php');
    exit();
}

// Handle edit menu item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_menu'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $sql = "UPDATE menu_items SET name=?, description=?, price=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdi", $name, $description, $price, $id);
    mysqli_stmt_execute($stmt);
    header('Location: menu.php');
    exit();
}

// Get all menu items with restaurant and category
$menu_sql = "SELECT mi.*, r.name as restaurant_name, c.name as category_name FROM menu_items mi JOIN restaurants r ON mi.restaurant_id = r.id LEFT JOIN categories c ON mi.category_id = c.id ORDER BY mi.id DESC";
$menu_result = mysqli_query($conn, $menu_sql);

// Get all restaurants and categories for add form
$restaurants = mysqli_query($conn, "SELECT id, name FROM restaurants");
$categories = mysqli_query($conn, "SELECT id, name FROM categories");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thực đơn - Admin</title>
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
                            <a class="nav-link text-white" href="orders.php">
                                <i class="fas fa-shopping-cart"></i> Quản lý đơn hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-white bg-secondary" href="menu.php">
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
                        <i class="fas fa-utensils me-2"></i>Quản lý thực đơn
                    </h1>
                </div>
                <form method="POST" class="row g-3 mb-4">
                    <input type="hidden" name="add_menu" value="1">
                    <div class="col-md-2"><input type="text" name="name" class="form-control" placeholder="Tên món" required></div>
                    <div class="col-md-2"><input type="text" name="description" class="form-control" placeholder="Mô tả"></div>
                    <div class="col-md-2"><input type="number" name="price" class="form-control" placeholder="Giá" required></div>
                    <div class="col-md-2">
                        <select name="restaurant_id" class="form-select" required>
                            <option value="">Nhà hàng</option>
                            <?php 
                            $restaurants2 = mysqli_query($conn, "SELECT id, name FROM restaurants");
                            while($r = mysqli_fetch_assoc($restaurants2)): ?>
                                <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="category_id" class="form-select">
                            <option value="">Danh mục</option>
                            <?php 
                            $categories2 = mysqli_query($conn, "SELECT id, name FROM categories");
                            while($c = mysqli_fetch_assoc($categories2)): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2"><button type="submit" class="btn btn-success"><i class="fas fa-plus me-1"></i>Thêm món</button></div>
                </form>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-utensils me-2"></i>Danh sách thực đơn</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên món</th>
                                        <th>Mô tả</th>
                                        <th>Giá</th>
                                        <th>Nhà hàng</th>
                                        <th>Danh mục</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($item = mysqli_fetch_assoc($menu_result)): ?>
                                    <tr>
                                        <td><?php echo $item['id']; ?></td>
                                        <td>
                                            <form method="POST" class="d-inline-flex">
                                                <input type="hidden" name="edit_menu" value="1">
                                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                <input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" class="form-control form-control-sm me-1" style="width:110px;">
                                        </td>
                                        <td><input type="text" name="description" value="<?php echo htmlspecialchars($item['description']); ?>" class="form-control form-control-sm me-1" style="width:110px;"></td>
                                        <td><input type="number" name="price" value="<?php echo $item['price']; ?>" class="form-control form-control-sm me-1" style="width:80px;"></td>
                                        <td><?php echo htmlspecialchars($item['restaurant_name']); ?></td>
                                        <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                        <td><?php echo isset($item['created_at']) ? date('d/m/Y', strtotime($item['created_at'])) : '-'; ?></td>
                                        <td>
                                                <button type="submit" class="btn btn-sm btn-primary me-1"><i class="fas fa-save"></i></button>
                                            </form>
                                            <a href="menu.php?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa món này?');"><i class="fas fa-trash"></i></a>
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
