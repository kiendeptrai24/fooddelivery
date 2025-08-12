<?php

class RestaurantController
{
    public function index(): string
    {
        return '
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhà hàng - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
        }
        
        .restaurant-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            height: 100%;
        }
        
        .restaurant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .filter-section {
            background: #f8f9fa;
            padding: 30px 0;
            border-radius: 15px;
            margin-bottom: 40px;
        }
        
        .filter-btn {
            border-radius: 25px;
            padding: 8px 20px;
            margin: 5px;
            transition: all 0.3s ease;
        }
        
        .filter-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .rating {
            color: #ffc107;
        }
        
        .cuisine-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-utensils me-2"></i>Food Delivery
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/restaurants">Nhà hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/menu">Thực đơn</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/cart">
                            <i class="fas fa-shopping-cart me-1"></i>Giỏ hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/register">Đăng ký</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">Khám phá nhà hàng</h1>
            <p class="lead mb-4">Hàng trăm nhà hàng ngon với đa dạng món ăn</p>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-lg" placeholder="Tìm kiếm nhà hàng, món ăn...">
                        <button class="btn btn-warning btn-lg" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="filter-section">
        <div class="container">
            <h5 class="mb-3">Lọc theo loại món ăn:</h5>
            <div class="d-flex flex-wrap">
                <button class="btn btn-outline-primary filter-btn active">Tất cả</button>
                <button class="btn btn-outline-primary filter-btn">Món Việt</button>
                <button class="btn btn-outline-primary filter-btn">Món Á</button>
                <button class="btn btn-outline-primary filter-btn">Món Âu</button>
                <button class="btn btn-outline-primary filter-btn">Đồ uống</button>
                <button class="btn btn-outline-primary filter-btn">Tráng miệng</button>
                <button class="btn btn-outline-primary filter-btn">Đồ chay</button>
            </div>
        </div>
    </section>

    <!-- Restaurants Grid -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Restaurant 1 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card restaurant-card">
                        <img src="https://via.placeholder.com/300x200/FF6B6B/FFFFFF?text=Restaurant+1" 
                             class="card-img-top" alt="Nhà hàng 1">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">Nhà hàng Việt Nam</h5>
                                <span class="cuisine-badge">Món Việt</span>
                            </div>
                            <p class="card-text text-muted">Chuyên về các món ăn truyền thống Việt Nam</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                    <span class="text-muted ms-1">4.5</span>
                                </div>
                                <small class="text-muted">30-45 phút</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-success fw-bold">Miễn phí giao hàng</span>
                                <a href="/restaurants/1" class="btn btn-outline-primary btn-sm">Xem menu</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Restaurant 2 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card restaurant-card">
                        <img src="https://via.placeholder.com/300x200/4ECDC4/FFFFFF?text=Restaurant+2" 
                             class="card-img-top" alt="Nhà hàng 2">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">Sushi Bar</h5>
                                <span class="cuisine-badge">Món Á</span>
                            </div>
                            <p class="card-text text-muted">Sushi tươi ngon, sashimi chất lượng cao</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <span class="text-muted ms-1">4.8</span>
                                </div>
                                <small class="text-muted">25-40 phút</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-success fw-bold">Miễn phí giao hàng</span>
                                <a href="/restaurants/2" class="btn btn-outline-primary btn-sm">Xem menu</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Restaurant 3 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card restaurant-card">
                        <img src="https://via.placeholder.com/300x200/45B7D1/FFFFFF?text=Restaurant+3" 
                             class="card-img-top" alt="Nhà hàng 3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">Pizza House</h5>
                                <span class="cuisine-badge">Món Âu</span>
                            </div>
                            <p class="card-text text-muted">Pizza Ý chính gốc với bột mỏng giòn</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <span class="text-muted ms-1">4.2</span>
                                </div>
                                <small class="text-muted">35-50 phút</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Phí giao: 15k</span>
                                <a href="/restaurants/3" class="btn btn-outline-primary btn-sm">Xem menu</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Restaurant 4 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card restaurant-card">
                        <img src="https://via.placeholder.com/300x200/96CEB4/FFFFFF?text=Restaurant+4" 
                             class="card-img-top" alt="Nhà hàng 4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">Café Sài Gòn</h5>
                                <span class="cuisine-badge">Đồ uống</span>
                            </div>
                            <p class="card-text text-muted">Cà phê truyền thống và đồ uống hiện đại</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <span class="text-muted ms-1">4.7</span>
                                </div>
                                <small class="text-muted">20-35 phút</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-success fw-bold">Miễn phí giao hàng</span>
                                <a href="/restaurants/4" class="btn btn-outline-primary btn-sm">Xem menu</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Restaurant 5 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card restaurant-card">
                        <img src="https://via.placeholder.com/300x200/FFEAA7/FFFFFF?text=Restaurant+5" 
                             class="card-img-top" alt="Nhà hàng 5">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">Sweet Dreams</h5>
                                <span class="cuisine-badge">Tráng miệng</span>
                            </div>
                            <p class="card-text text-muted">Bánh ngọt, kem và đồ tráng miệng handmade</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <span class="text-muted ms-1">4.4</span>
                                </div>
                                <small class="text-muted">15-30 phút</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-success fw-bold">Miễn phí giao hàng</span>
                                <a href="/restaurants/4" class="btn btn-outline-primary btn-sm">Xem menu</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Restaurant 6 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card restaurant-card">
                        <img src="https://via.placeholder.com/300x200/DDA0DD/FFFFFF?text=Restaurant+6" 
                             class="card-img-top" alt="Nhà hàng 6">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">Chay Garden</h5>
                                <span class="cuisine-badge">Đồ chay</span>
                            </div>
                            <p class="card-text text-muted">Món chay ngon, đa dạng và bổ dưỡng</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <span class="text-muted ms-1">4.6</span>
                                </div>
                                <small class="text-muted">25-40 phút</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-success fw-bold">Miễn phí giao hàng</span>
                                <a href="/restaurants/6" class="btn btn-outline-primary btn-sm">Xem menu</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <nav aria-label="Restaurant pagination" class="mt-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Trước</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Sau</a>
                    </li>
                </ul>
            </nav>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-utensils me-2"></i>Food Delivery</h5>
                    <p class="text-muted">Dịch vụ giao đồ ăn tốt nhất với nhiều ưu đãi</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted">&copy; 2024 Food Delivery. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter functionality
        document.querySelectorAll(".filter-btn").forEach(btn => {
            btn.addEventListener("click", function() {
                // Remove active class from all buttons
                document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
                // Add active class to clicked button
                this.classList.add("active");
                
                // Here you would implement the actual filtering logic
                console.log("Filter by:", this.textContent);
            });
        });
    </script>
</body>
</html>';
    }

    public function show($id): string
    {
        return '
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết nhà hàng - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .restaurant-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
        }
        
        .menu-item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .add-to-cart-btn {
            border-radius: 25px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }
        
        .add-to-cart-btn:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-utensils me-2"></i>Food Delivery
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/restaurants">Nhà hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/menu">Thực đơn</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/cart">
                            <i class="fas fa-shopping-cart me-1"></i>Giỏ hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/register">Đăng ký</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Restaurant Hero -->
    <section class="restaurant-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4 fw-bold mb-3">Nhà hàng Việt Nam</h1>
                    <p class="lead mb-3">Chuyên về các món ăn truyền thống Việt Nam</p>
                    <div class="d-flex align-items-center mb-3">
                        <div class="rating me-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                            <span class="ms-2">4.5 (120 đánh giá)</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-4"><i class="fas fa-clock me-2"></i>30-45 phút</span>
                        <span class="me-4"><i class="fas fa-truck me-2"></i>Miễn phí giao hàng</span>
                        <span><i class="fas fa-map-marker-alt me-2"></i>Quận 1, TP.HCM</span>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <img src="https://via.placeholder.com/300x200/FF6B6B/FFFFFF?text=Restaurant+1" 
                         alt="Nhà hàng" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Thực đơn</h2>
            
            <!-- Categories -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-center">
                        <button class="btn btn-outline-primary me-2 mb-2 active">Tất cả</button>
                        <button class="btn btn-outline-primary me-2 mb-2">Món chính</button>
                        <button class="btn btn-outline-primary me-2 mb-2">Món canh</button>
                        <button class="btn btn-outline-primary me-2 mb-2">Món xào</button>
                        <button class="btn btn-outline-primary me-2 mb-2">Đồ uống</button>
                    </div>
                </div>
            </div>

            <!-- Menu Items -->
            <div class="row">
                <!-- Menu Item 1 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card menu-item h-100">
                        <img src="https://via.placeholder.com/300x200/FF6B6B/FFFFFF?text=Pho+Bo" 
                             class="card-img-top" alt="Phở bò">
                        <div class="card-body">
                            <h5 class="card-title">Phở bò</h5>
                            <p class="card-text text-muted">Phở bò truyền thống với nước dùng đậm đà</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-primary mb-0">45.000đ</span>
                                <button class="btn btn-primary add-to-cart-btn">
                                    <i class="fas fa-plus me-2"></i>Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Item 2 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card menu-item h-100">
                        <img src="https://via.placeholder.com/300x200/4ECDC4/FFFFFF?text=Com+Suon" 
                             class="card-img-top" alt="Cơm sườn">
                        <div class="card-body">
                            <h5 class="card-title">Cơm sườn</h5>
                            <p class="card-text text-muted">Cơm sườn nướng với rau sống và nước mắm</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-primary mb-0">35.000đ</span>
                                <button class="btn btn-primary add-to-cart-btn">
                                    <i class="fas fa-plus me-2"></i>Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Item 3 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card menu-item h-100">
                        <img src="https://via.placeholder.com/300x200/45B7D1/FFFFFF?text=Bun+Cha" 
                             class="card-img-top" alt="Bún chả">
                        <div class="card-body">
                            <h5 class="card-title">Bún chả</h5>
                            <p class="card-text text-muted">Bún chả Hà Nội với thịt nướng thơm ngon</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-primary mb-0">40.000đ</span>
                                <button class="btn btn-primary add-to-cart-btn">
                                    <i class="fas fa-plus me-2"></i>Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Item 4 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card menu-item h-100">
                        <img src="https://via.placeholder.com/300x200/96CEB4/FFFFFF?text=Banh+Mi" 
                             class="card-img-top" alt="Bánh mì">
                        <div class="card-body">
                            <h5 class="card-title">Bánh mì thịt</h5>
                            <p class="card-text text-muted">Bánh mì Việt Nam với thịt, rau và sốt</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-primary mb-0">25.000đ</span>
                                <button class="btn btn-primary add-to-cart-btn">
                                    <i class="fas fa-plus me-2"></i>Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Item 5 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card menu-item h-100">
                        <img src="https://via.placeholder.com/300x200/FFEAA7/FFFFFF?text=Ca+Phe" 
                             class="card-img-top" alt="Cà phê">
                        <div class="card-body">
                            <h5 class="card-title">Cà phê sữa đá</h5>
                            <p class="card-text text-muted">Cà phê đen với sữa đặc và đá</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-primary mb-0">15.000đ</span>
                                <button class="btn btn-primary add-to-cart-btn">
                                    <i class="fas fa-plus me-2"></i>Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Item 6 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card menu-item h-100">
                        <img src="https://via.placeholder.com/300x200/DDA0DD/FFFFFF?text=Che" 
                             class="card-img-top" alt="Chè">
                        <div class="card-body">
                            <h5 class="card-title">Chè ba màu</h5>
                            <p class="card-text text-muted">Chè ba màu truyền thống với nước cốt dừa</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-primary mb-0">20.000đ</span>
                                <button class="btn btn-primary add-to-cart-btn">
                                    <i class="fas fa-plus me-2"></i>Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-utensils me-2"></i>Food Delivery</h5>
                    <p class="text-muted">Dịch vụ giao đồ ăn tốt nhất với nhiều ưu đãi</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted">&copy; 2024 Food Delivery. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
    }
}
