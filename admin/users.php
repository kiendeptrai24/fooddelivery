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
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Quản lý người dùng</h2>
    <!-- Add User Form -->
    <form method="POST" class="row g-3 mb-4">
        <input type="hidden" name="add_user" value="1">
        <div class="col-md-2"><input type="text" name="username" class="form-control" placeholder="Tên đăng nhập" required></div>
        <div class="col-md-2"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
        <div class="col-md-2"><input type="password" name="password" class="form-control" placeholder="Mật khẩu" required></div>
        <div class="col-md-2"><input type="text" name="full_name" class="form-control" placeholder="Họ tên" required></div>
        <div class="col-md-2"><input type="text" name="phone" class="form-control" placeholder="Số điện thoại"></div>
        <div class="col-md-2"><input type="text" name="address" class="form-control" placeholder="Địa chỉ"></div>
        <div class="col-md-12"><button type="submit" class="btn btn-success">Thêm người dùng</button></div>
    </form>
    <table class="table table-bordered table-striped">
        <thead>
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
                        <button type="submit" class="btn btn-sm btn-primary me-1">Lưu</button>
                    </form>
                    <a href="users.php?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa người dùng này?');">Xóa</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary">Quay lại Dashboard</a>
</div>
</body>
</html>
