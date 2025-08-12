# Food Delivery System

Hệ thống giao đồ ăn hoàn chỉnh với 2 phiên bản: PHP thuần và Symfony.

## 📁 Cấu trúc dự án

### 1. Dự án PHP thuần (thư mục gốc)
- **Trang chủ**: `index.php` - Giao diện chính với hero section, features, categories
- **Đăng ký**: `register.php` - Form đăng ký tài khoản mới
- **Đăng nhập**: `login.php` - Form đăng nhập
- **Nhà hàng**: `restaurants.php` - Danh sách và chi tiết nhà hàng
- **Giỏ hàng**: `cart.php` - Quản lý giỏ hàng
- **Thanh toán**: `checkout.php` - Xử lý đặt hàng
- **Đơn hàng**: `orders.php` - Lịch sử đơn hàng
- **Hồ sơ**: `profile.php` - Thông tin cá nhân

### 2. Dự án Symfony (`food-delivery-symfony/`)
- **HomeController**: Trang chủ với giao diện đẹp
- **Entity**: Restaurant, User
- **Template**: Twig templates
- **Routing**: Định tuyến cơ bản

## 🚀 Cách sử dụng

### Dự án PHP thuần
1. **Cài đặt XAMPP** và khởi động Apache + MySQL
2. **Import database** từ `config/database.sql` (nếu có)
3. **Truy cập**: `http://localhost/food-delivery/`

### Dự án Symfony
1. **Cài đặt Composer** (đã có sẵn `composer.phar`)
2. **Cài đặt dependencies**:
   ```bash
   cd food-delivery-symfony
   php composer.phar install
   ```
3. **Khởi động server**:
   ```bash
   php -S localhost:8000 -t public
   ```
4. **Truy cập**: `http://localhost:8000/`

## ✨ Tính năng chính

### Trang chủ (`index.php`)
- ✅ Hero section với gradient đẹp mắt
- ✅ Features section giới thiệu dịch vụ
- ✅ Categories section với 6 loại món ăn
- ✅ Featured restaurants (nếu có dữ liệu)
- ✅ Stats section hiển thị số liệu
- ✅ Responsive design với Bootstrap 5
- ✅ Navigation sticky với dropdown menu
- ✅ Footer đầy đủ thông tin

### Đăng ký (`register.php`)
- ✅ Form đăng ký hoàn chỉnh
- ✅ Validation đầy đủ
- ✅ Kiểm tra username/email trùng lặp
- ✅ Hash password an toàn
- ✅ Giao diện Bootstrap đẹp mắt

### Đăng nhập (`login.php`)
- ✅ Form đăng nhập
- ✅ Session management
- ✅ Redirect sau đăng nhập

## 🎨 Giao diện

- **Bootstrap 5**: Framework CSS hiện đại
- **Font Awesome**: Icons đẹp mắt
- **Responsive**: Tương thích mọi thiết bị
- **Gradient**: Màu sắc gradient đẹp mắt
- **Hover effects**: Hiệu ứng hover cho cards
- **Sticky navigation**: Menu cố định khi scroll

## 🔧 Cấu hình

### Database
- File cấu hình: `config/database.php`
- Hỗ trợ MySQL/MariaDB
- Prepared statements để bảo mật

### Assets
- CSS: `assets/css/style.css`
- JavaScript: `assets/js/main.js`
- Images: `assets/images/`

## 📱 Responsive Design

- **Mobile-first** approach
- **Bootstrap Grid** system
- **Flexbox** layouts
- **Media queries** cho breakpoints

## 🚀 Deployment

### Local Development
- XAMPP/WAMP cho PHP thuần
- Symfony CLI cho Symfony project

### Production
- Web server (Apache/Nginx)
- PHP 7.4+
- MySQL 5.7+
- Composer (cho Symfony)

## 📝 Ghi chú

- Dự án PHP thuần đã hoàn thiện và sẵn sàng sử dụng
- Dự án Symfony cần hoàn thiện thêm các controller và template
- Cả hai đều có giao diện đẹp và responsive
- Trang register đã có sẵn và hoạt động tốt

## 🤝 Đóng góp

1. Fork dự án
2. Tạo feature branch
3. Commit changes
4. Push to branch
5. Tạo Pull Request

## 📄 License

MIT License - xem file LICENSE để biết thêm chi tiết.

---

**Lưu ý**: Đây là dự án demo, vui lòng cập nhật thông tin database và cấu hình phù hợp với môi trường của bạn.
