<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get orders from localStorage (will be handled by JavaScript)
$orders = [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của tôi - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .order-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .order-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .order-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 1rem;
            border-radius: 10px 10px 0 0;
        }
        
        .order-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-preparing {
            background: #d4edda;
            color: #155724;
        }
        
        .status-delivering {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-delivered {
            background: #d1e7dd;
            color: #0f5132;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .order-items {
            padding: 1rem;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-item-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 1rem;
        }
        
        .order-summary {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0 0 10px 10px;
        }
        
        .empty-orders {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .empty-orders i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .loading {
            text-align: center;
            padding: 2rem;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        
        .order-timeline {
            padding: 1rem;
            border-left: 2px solid #dee2e6;
            margin-left: 1rem;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -0.5rem;
            top: 0.25rem;
            width: 0.75rem;
            height: 0.75rem;
            background: #007bff;
            border-radius: 50%;
        }
        
        .timeline-item.active::before {
            background: #28a745;
        }
    </style>
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
                        <a class="nav-link active" href="orders.php">Đơn hàng</a>
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
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge bg-danger" id="cart-count">0</span>
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
                        <i class="fas fa-list-alt me-2 text-primary"></i>Đơn hàng của tôi
                    </h1>
                    <p class="text-muted mb-0">Theo dõi trạng thái đơn hàng</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="restaurants.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Đặt món mới
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Content -->
    <div class="container mt-4">
        <!-- Loading State -->
        <div id="loadingState" class="loading">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
            <p class="mt-2">Đang tải đơn hàng...</p>
        </div>

        <!-- Orders Container -->
        <div id="ordersContainer" style="display: none;">
            <!-- Orders will be populated here -->
        </div>

        <!-- Empty Orders State -->
        <div id="emptyOrders" style="display: none;">
            <div class="empty-orders">
                <i class="fas fa-clipboard-list"></i>
                <h4 class="text-muted">Chưa có đơn hàng nào</h4>
                <p class="text-muted">Bạn chưa đặt món ăn nào</p>
                <a href="restaurants.php" class="btn btn-primary">
                    <i class="fas fa-utensils me-2"></i>Khám phá nhà hàng
                </a>
            </div>
        </div>
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
        let orders = [];

        // Initialize orders on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadOrders();
            updateCartCount();
        });

        function loadOrders() {
            // Get orders from localStorage
            orders = JSON.parse(localStorage.getItem('orders')) || [];
            
            if (orders.length === 0) {
                showEmptyOrders();
            } else {
                displayOrders();
            }
        }

        function displayOrders() {
            // Hide loading and empty states
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('emptyOrders').style.display = 'none';
            document.getElementById('ordersContainer').style.display = 'block';

            // Sort orders by creation date (newest first)
            orders.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

            let ordersHtml = '';
            orders.forEach(order => {
                ordersHtml += createOrderCard(order);
            });

            document.getElementById('ordersContainer').innerHTML = ordersHtml;
        }

        function createOrderCard(order) {
            const orderDate = new Date(order.created_at);
            const statusText = getStatusText(order.status);
            const statusClass = getStatusClass(order.status);
            
            return `
                <div class="order-card">
                    <div class="order-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="mb-1">
                                    <i class="fas fa-receipt me-2"></i>Đơn hàng #${order.id}
                                </h6>
                                <small>Đặt lúc: ${orderDate.toLocaleString('vi-VN')}</small>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <span class="order-status ${statusClass}">${statusText}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-items">
                        <h6 class="mb-3">
                            <i class="fas fa-store me-2 text-primary"></i>${order.restaurant_name}
                        </h6>
                        
                        ${order.items.map(item => `
                            <div class="order-item">
                                <img src="${item.image || 'assets/images/default-food.jpg'}" 
                                     class="order-item-image" 
                                     alt="${item.name}"
                                     onerror="this.src='assets/images/default-food.jpg'">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">${item.name}</h6>
                                    <small class="text-muted">Số lượng: ${item.quantity}</small>
                                </div>
                                <div class="text-end">
                                    <strong>${(item.price * item.quantity).toLocaleString()} ₫</strong>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    
                    <div class="order-summary">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="order-timeline">
                                    <div class="timeline-item active">
                                        <strong>Đơn hàng đã được xác nhận</strong>
                                        <br><small class="text-muted">${orderDate.toLocaleString('vi-VN')}</small>
                                    </div>
                                    <div class="timeline-item">
                                        <strong>Nhà hàng đang chuẩn bị</strong>
                                        <br><small class="text-muted">Dự kiến: ${order.delivery_time}</small>
                                    </div>
                                    <div class="timeline-item">
                                        <strong>Đang giao hàng</strong>
                                        <br><small class="text-muted">Shipper đang đến</small>
                                    </div>
                                    <div class="timeline-item">
                                        <strong>Giao hàng thành công</strong>
                                        <br><small class="text-muted">Đã hoàn thành</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-end">
                                    <div class="mb-2">
                                        <small class="text-muted">Tạm tính:</small>
                                        <br><strong>${order.subtotal.toLocaleString()} ₫</strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Thuế VAT:</small>
                                        <br><strong>${order.vat.toLocaleString()} ₫</strong>
                                    </div>
                                    <hr>
                                    <div class="mb-2">
                                        <strong class="fs-5">Tổng cộng:</strong>
                                        <br><strong class="text-primary fs-5">${order.total.toLocaleString()} ₫</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function getStatusText(status) {
            const statusMap = {
                'pending': 'Chờ xác nhận',
                'confirmed': 'Đã xác nhận',
                'preparing': 'Đang chuẩn bị',
                'delivering': 'Đang giao hàng',
                'delivered': 'Đã giao hàng',
                'cancelled': 'Đã hủy'
            };
            return statusMap[status] || 'Chờ xác nhận';
        }

        function getStatusClass(status) {
            const classMap = {
                'pending': 'status-pending',
                'confirmed': 'status-confirmed',
                'preparing': 'status-preparing',
                'delivering': 'status-delivering',
                'delivered': 'status-delivered',
                'cancelled': 'status-cancelled'
            };
            return classMap[status] || 'status-pending';
        }

        function showEmptyOrders() {
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('ordersContainer').style.display = 'none';
            document.getElementById('emptyOrders').style.display = 'block';
        }

        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
            document.getElementById('cart-count').textContent = totalItems;
        }
    </script>
</body>
</html>
