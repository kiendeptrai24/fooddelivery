<?php

class HomeController
{
    public function index(): string
    {
        // Simple HTML response for development
        return '
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Delivery - Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Cdefs%3E%3Cpattern id=\'grain\' width=\'100\' height=\'100\' patternUnits=\'userSpaceOnUse\'%3E%3Ccircle cx=\'50\' cy=\'50\' r=\'1\' fill=\'white\' opacity=\'0.1\'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width=\'100\' height=\'100\' fill=\'url(%23grain)\'/%3E%3C/svg%3E");
            opacity: 0.3;
        }
        
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .category-card {
            text-align: center;
            padding: 30px 20px;
            border-radius: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .stats-section {
            background: #f8f9fa;
            padding: 80px 0;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .btn-custom {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
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
                        <a class="nav-link active" href="/">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/restaurants">Nhà hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/menu">Thực đơn</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Liên hệ</a>
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
        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        Đặt đồ ăn ngon tại nhà
                    </h1>
                    <p class="lead mb-4">
                        Khám phá hàng trăm món ăn ngon từ các nhà hàng uy tín.
                        Đặt hàng dễ dàng, giao hàng nhanh chóng!
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="/restaurants" class="btn btn-warning btn-lg btn-custom">
                            <i class="fas fa-utensils me-2"></i>Đặt hàng ngay
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg btn-custom">
                            <i class="fas fa-info-circle me-2"></i>Tìm hiểu thêm
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="https://via.placeholder.com/500x400/FF6B6B/FFFFFF?text=Food+Delivery" 
                         alt="Food Delivery" class="img-fluid" style="max-width: 500px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Tại sao chọn chúng tôi?</h2>
                <p class="lead text-muted">Dịch vụ giao đồ ăn tốt nhất với nhiều ưu đãi</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-clock fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Giao hàng nhanh</h5>
                            <p class="card-text">Giao hàng trong vòng 30-45 phút, đảm bảo đồ ăn còn nóng hổi</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-utensils fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title">Đa dạng món ăn</h5>
                            <p class="card-text">Hàng trăm món ăn từ các nhà hàng uy tín, đáp ứng mọi khẩu vị</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-mobile-alt fa-3x text-warning"></i>
                            </div>
                            <h5 class="card-title">Đặt hàng dễ dàng</h5>
                            <p class="card-text">Giao diện thân thiện, dễ sử dụng trên mọi thiết bị</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Danh mục món ăn</h2>
                <p class="lead text-muted">Khám phá các loại món ăn đa dạng</p>
            </div>
            <div class="row">
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="category-card" onclick="location.href=\'/restaurants?category=vietnamese\'">
                        <i class="fas fa-flag fa-2x text-primary mb-3"></i>
                        <h6 class="fw-bold">Món Việt</h6>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="category-card" onclick="location.href=\'/restaurants?category=asian\'">
                        <i class="fas fa-utensils fa-2x text-success mb-3"></i>
                        <h6 class="fw-bold">Món Á</h6>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="category-card" onclick="location.href=\'/restaurants?category=european\'">
                        <i class="fas fa-cheese fa-2x text-warning mb-3"></i>
                        <h6 class="fw-bold">Món Âu</h6>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="category-card" onclick="location.href=\'/restaurants?category=beverages\'">
                        <i class="fas fa-coffee fa-2x text-info mb-3"></i>
                        <h6 class="fw-bold">Đồ uống</h6>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="category-card" onclick="location.href=\'/restaurants?category=desserts\'">
                        <i class="fas fa-ice-cream fa-2x text-danger mb-3"></i>
                        <h6 class="fw-bold">Tráng miệng</h6>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="category-card" onclick="location.href=\'/restaurants?category=vegetarian\'">
                        <i class="fas fa-leaf fa-2x text-success mb-3"></i>
                        <h6 class="fw-bold">Đồ chay</h6>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <p class="text-muted">Món ăn</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">50+</div>
                        <p class="text-muted">Nhà hàng</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">1000+</div>
                        <p class="text-muted">Khách hàng</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">30</div>
                        <p class="text-muted">Phút giao hàng</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">Sẵn sàng đặt hàng?</h2>
            <p class="lead mb-4">Tham gia cùng hàng nghìn khách hàng đang sử dụng dịch vụ của chúng tôi</p>
            <a href="/register" class="btn btn-warning btn-lg btn-custom">
                <i class="fas fa-user-plus me-2"></i>Đăng ký ngay
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5><i class="fas fa-utensils me-2"></i>Food Delivery</h5>
                    <p class="text-muted">Dịch vụ giao đồ ăn tốt nhất với nhiều ưu đãi</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>Liên kết nhanh</h6>
                    <ul class="list-unstyled">
                        <li><a href="/restaurants" class="text-muted text-decoration-none">Nhà hàng</a></li>
                        <li><a href="/menu" class="text-muted text-decoration-none">Thực đơn</a></li>
                        <li><a href="/about" class="text-muted text-decoration-none">Giới thiệu</a></li>
                        <li><a href="/contact" class="text-muted text-decoration-none">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>Liên hệ</h6>
                    <p class="text-muted">
                        <i class="fas fa-phone me-2"></i>0123 456 789<br>
                        <i class="fas fa-envelope me-2"></i>info@fooddelivery.com<br>
                        <i class="fas fa-map-marker-alt me-2"></i>123 Đường ABC, Quận 1, TP.HCM
                    </p>
                </div>
            </div>
            <hr class="my-3">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; 2024 Food Delivery. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="social-links">
                        <a href="#" class="text-muted me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-muted me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-muted me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-youtube fa-lg"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll(\'a[href^="#"]\').forEach(anchor => {
            anchor.addEventListener(\'click\', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute(\'href\'));
                if (target) {
                    target.scrollIntoView({
                        behavior: \'smooth\',
                        block: \'start\'
                    });
                }
            });
        });
        
        // Add active class to current nav item
        document.addEventListener(\'DOMContentLoaded\', function() {
            const currentLocation = location.pathname;
            const navLinks = document.querySelectorAll(\'.navbar-nav .nav-link\');
            
            navLinks.forEach(link => {
                if (link.getAttribute(\'href\') === currentLocation) {
                    link.classList.add(\'active\');
                }
            });
        });
    </script>
</body>
</html>';
    }
}
