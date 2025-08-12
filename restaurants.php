<?php
session_start();
require_once 'config/database.php';

// Get search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$cuisine = isset($_GET['cuisine']) ? $_GET['cuisine'] : '';

// Build query
$where_conditions = ["status = 'active'"];
$params = [];
$param_types = '';

if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'ss';
}

if (!empty($cuisine)) {
    $where_conditions[] = "cuisine = ?";
    $params[] = $cuisine;
    $param_types .= 's';
}

$where_clause = implode(' AND ', $where_conditions);

// Get restaurants
$sql = "SELECT * FROM restaurants WHERE $where_clause ORDER BY featured DESC, name ASC";
$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
}

mysqli_stmt_execute($stmt);
$restaurants = mysqli_stmt_get_result($stmt);

// Get unique cuisines for filter
$cuisines_sql = "SELECT DISTINCT cuisine FROM restaurants WHERE status = 'active' AND cuisine IS NOT NULL ORDER BY cuisine";
$cuisines_result = mysqli_query($conn, $cuisines_sql);
$cuisines = [];
while ($row = mysqli_fetch_assoc($cuisines_result)) {
    $cuisines[] = $row['cuisine'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhà hàng - Food Delivery</title>
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
                        <a class="nav-link active" href="restaurants.php">Nhà hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Đơn hàng</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
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
                                <span class="badge bg-danger" id="cart-count">0</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Đăng ký</a>
                        </li>
                    <?php endif; ?>
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
                        <i class="fas fa-store me-2 text-primary"></i>Nhà hàng
                    </h1>
                    <p class="text-muted mb-0">Khám phá các nhà hàng ngon trong khu vực</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Về trang chủ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Tìm kiếm</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Tìm nhà hàng..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="cuisine" class="form-label">Loại ẩm thực</label>
                        <select class="form-select" id="cuisine" name="cuisine">
                            <option value="">Tất cả</option>
                            <?php foreach ($cuisines as $c): ?>
                                <option value="<?php echo htmlspecialchars($c); ?>" 
                                        <?php echo $cuisine === $c ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Lọc
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Restaurants List -->
    <div class="container mt-4">
        <?php if (mysqli_num_rows($restaurants) > 0): ?>
            <div class="row">
                <?php while ($restaurant = mysqli_fetch_assoc($restaurants)): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 restaurant-card">
                            <img src="<?php echo htmlspecialchars($restaurant['image']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($restaurant['name']); ?>"
                                 onerror="this.src='assets/images/default-restaurant.jpg'">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($restaurant['name']); ?></h5>
                                    <?php if ($restaurant['featured']): ?>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-star me-1"></i>Nổi bật
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <p class="card-text text-muted">
                                    <?php echo htmlspecialchars($restaurant['description']); ?>
                                </p>
                                
                                <div class="mb-3">
                                    <span class="badge bg-success me-2">
                                        <i class="fas fa-utensils me-1"></i>
                                        <?php echo htmlspecialchars($restaurant['cuisine']); ?>
                                    </span>
                                    <span class="badge bg-info">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($restaurant['address']); ?>
                                    </span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-phone me-1"></i>
                                        <?php echo htmlspecialchars($restaurant['phone']); ?>
                                    </small>
                                    <a href="restaurant.php?id=<?php echo $restaurant['id']; ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-utensils me-1"></i>Xem menu
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Không tìm thấy nhà hàng</h4>
                <p class="text-muted">Hãy thử tìm kiếm với từ khóa khác hoặc bỏ bộ lọc</p>
                <a href="restaurants.php" class="btn btn-primary">
                    <i class="fas fa-refresh me-2"></i>Xem tất cả
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
    <script src="assets/js/main.js"></script>
</body>
</html>
