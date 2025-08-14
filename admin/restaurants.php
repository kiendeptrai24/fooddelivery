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
    mysqli_query($conn, "DELETE FROM restaurants WHERE id = $id");
    header('Location: restaurants.php');
    exit();
}

// Handle add restaurant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_restaurant'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $cuisine = trim($_POST['cuisine']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $status = $_POST['status'];
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_name = basename($_FILES['image']['name']);
        $target_dir = '../assets/images/';
        $target_file = $target_dir . $img_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = 'assets/images/' . $img_name;
        }
    }
    $sql = "INSERT INTO restaurants (name, description, cuisine, address, phone, status, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssss", $name, $description, $cuisine, $address, $phone, $status, $image_url);
    mysqli_stmt_execute($stmt);
    header('Location: restaurants.php');
    exit();
}

// Handle edit restaurant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_restaurant'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $cuisine = trim($_POST['cuisine']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $status = $_POST['status'];
    $sql = "UPDATE restaurants SET name=?, description=?, cuisine=?, address=?, phone=?, status=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssi", $name, $description, $cuisine, $address, $phone, $status, $id);
    mysqli_stmt_execute($stmt);
    header('Location: restaurants.php');
    exit();
}

$restaurants_sql = "SELECT * FROM restaurants ORDER BY created_at DESC";
$restaurants_result = mysqli_query($conn, $restaurants_sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhà hàng - Admin</title>
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
                            <a class="nav-link active text-white bg-secondary" href="restaurants.php">
                                <i class="fas fa-store"></i> Quản lý nhà hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="orders.php">
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
                        <i class="fas fa-store me-2"></i>Quản lý nhà hàng
                    </h1>
                </div>
                <form method="POST" class="row g-3 mb-4" enctype="multipart/form-data">
                    <input type="hidden" name="add_restaurant" value="1">
                    <div class="col-md-2"><input type="text" name="name" class="form-control" placeholder="Tên nhà hàng" required></div>
                    <div class="col-md-2"><input type="text" name="cuisine" class="form-control" placeholder="Ẩm thực" required></div>
                    <div class="col-md-2"><input type="text" name="address" class="form-control" placeholder="Địa chỉ" required></div>
                    <div class="col-md-2"><input type="text" name="phone" class="form-control" placeholder="Số điện thoại"></div>
                    <div class="col-md-2"><input type="text" name="description" class="form-control" placeholder="Mô tả"></div>
                    <div class="col-md-1">
                        <select name="status" class="form-select">
                            <option value="active">Hoạt động</option>
                            <option value="inactive">Ngừng</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-12"><button type="submit" class="btn btn-success"><i class="fas fa-plus me-1"></i>Thêm</button></div>
                </form>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-store me-2"></i>Danh sách nhà hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên nhà hàng</th>
                                        <th>Ẩm thực</th>
                                        <th>Địa chỉ</th>
                                        <th>Số điện thoại</th>
                                        <th>Mô tả</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($restaurant = mysqli_fetch_assoc($restaurants_result)): ?>
                                    <tr>
                                        <td><?php echo $restaurant['id']; ?></td>
                                        <td>
                                            <form method="POST" class="d-inline-flex">
                                                <input type="hidden" name="edit_restaurant" value="1">
                                                <input type="hidden" name="id" value="<?php echo $restaurant['id']; ?>">
                                                <input type="text" name="name" value="<?php echo htmlspecialchars($restaurant['name']); ?>" class="form-control form-control-sm me-1" style="width:110px;">
                                        </td>
                                        <td><input type="text" name="cuisine" value="<?php echo htmlspecialchars($restaurant['cuisine']); ?>" class="form-control form-control-sm me-1" style="width:90px;"></td>
                                        <td><input type="text" name="address" value="<?php echo htmlspecialchars($restaurant['address']); ?>" class="form-control form-control-sm me-1" style="width:110px;"></td>
                                        <td><input type="text" name="phone" value="<?php echo htmlspecialchars($restaurant['phone']); ?>" class="form-control form-control-sm me-1" style="width:90px;"></td>
                                        <td><input type="text" name="description" value="<?php echo htmlspecialchars($restaurant['description']); ?>" class="form-control form-control-sm me-1" style="width:110px;"></td>
                                        <td>
                                            <select name="status" class="form-select form-select-sm me-1" style="width:90px;">
                                                <option value="active" <?php if($restaurant['status']=='active') echo 'selected'; ?>>Hoạt động</option>
                                                <option value="inactive" <?php if($restaurant['status']=='inactive') echo 'selected'; ?>>Ngừng</option>
                                            </select>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($restaurant['created_at'])); ?></td>
                                        <td>
                                                <button type="submit" class="btn btn-sm btn-primary me-1"><i class="fas fa-save"></i></button>
                                            </form>
                                            <a href="restaurants.php?delete=<?php echo $restaurant['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa nhà hàng này?');"><i class="fas fa-trash"></i></a>
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
