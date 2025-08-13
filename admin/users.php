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
    mysqli_query($conn, "DELETE FROM users WHERE id = $id AND role = 'user'");
    header('Location: users.php');
    exit();
}

// Handle add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $sql = "INSERT INTO users (username, email, password, full_name, phone, address, role) VALUES (?, ?, ?, ?, ?, ?, 'user')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $username, $email, $password, $full_name, $phone, $address);
    mysqli_stmt_execute($stmt);
    header('Location: users.php');
    exit();
}

// Handle edit user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $id = intval($_POST['id']);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $sql = "UPDATE users SET full_name=?, phone=?, address=? WHERE id=? AND role='user'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $full_name, $phone, $address, $id);
    mysqli_stmt_execute($stmt);
    header('Location: users.php');
    exit();
}

$users_sql = "SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng - Admin</title>
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
                            <a class="nav-link active text-white bg-secondary" href="users.php">
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
                        <i class="fas fa-users me-2"></i>Quản lý người dùng
                    </h1>
                </div>
                <form method="POST" class="row g-3 mb-4">
                    <input type="hidden" name="add_user" value="1">
                    <div class="col-md-2"><input type="text" name="username" class="form-control" placeholder="Tên đăng nhập" required></div>
                    <div class="col-md-2"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
                    <div class="col-md-2"><input type="password" name="password" class="form-control" placeholder="Mật khẩu" required></div>
                    <div class="col-md-2"><input type="text" name="full_name" class="form-control" placeholder="Họ tên" required></div>
                    <div class="col-md-2"><input type="text" name="phone" class="form-control" placeholder="Số điện thoại"></div>
                    <div class="col-md-2"><input type="text" name="address" class="form-control" placeholder="Địa chỉ"></div>
                    <div class="col-md-12"><button type="submit" class="btn btn-success"><i class="fas fa-plus me-1"></i>Thêm người dùng</button></div>
                </form>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Danh sách người dùng</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên đăng nhập</th>
                                        <th>Họ và tên</th>
                                        <th>Email</th>
                                        <th>Số điện thoại</th>
                                        <th>Địa chỉ</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td>
                                            <form method="POST" class="d-inline-flex">
                                                <input type="hidden" name="edit_user" value="1">
                                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" class="form-control form-control-sm me-1" style="width:120px;">
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" class="form-control form-control-sm me-1" style="width:110px;"></td>
                                        <td><input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" class="form-control form-control-sm me-1" style="width:110px;"></td>
                                        <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                                <button type="submit" class="btn btn-sm btn-primary me-1"><i class="fas fa-save"></i></button>
                                            </form>
                                            <a href="users.php?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa người dùng này?');"><i class="fas fa-trash"></i></a>
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
