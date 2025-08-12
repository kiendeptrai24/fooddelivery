# Food Delivery Symfony Project

Dự án Food Delivery sử dụng Symfony framework với giao diện đẹp mắt.

## 🚀 Cách chạy nhanh (không cần Composer)

### Bước 1: Khởi động server
```bash
cd food-delivery-symfony
php -S localhost:8000 -t public
```

### Bước 2: Truy cập
Mở trình duyệt và truy cập: `http://localhost:8000/`

## ✨ Tính năng đã hoàn thiện

### Trang chủ (`/`)
- ✅ Hero section với gradient đẹp mắt
- ✅ Features section giới thiệu dịch vụ
- ✅ Categories section với 6 loại món ăn
- ✅ Stats section hiển thị số liệu
- ✅ Responsive design với Bootstrap 5
- ✅ Navigation sticky với menu đầy đủ
- ✅ Footer với thông tin liên hệ và social links

### Routing
- ✅ `/` - Trang chủ
- ✅ `/restaurants` - Trang nhà hàng (coming soon)
- ✅ `/login` - Trang đăng nhập (coming soon)
- ✅ `/register` - Trang đăng ký (coming soon)

## 🎨 Giao diện

- **Bootstrap 5**: Framework CSS hiện đại
- **Font Awesome**: Icons đẹp mắt
- **Responsive**: Tương thích mọi thiết bị
- **Gradient**: Màu sắc gradient đẹp mắt
- **Hover effects**: Hiệu ứng hover cho cards
- **Sticky navigation**: Menu cố định khi scroll

## 🔧 Cấu trúc dự án

```
food-delivery-symfony/
├── public/
│   ├── index.php          # Front controller
│   └── .htaccess          # URL rewriting
├── src/
│   └── Controller/
│       └── HomeController.php  # Controller chính
├── templates/              # Twig templates (sẽ sử dụng sau)
└── config/                 # Cấu hình Symfony
```

## 📱 Responsive Design

- **Mobile-first** approach
- **Bootstrap Grid** system
- **Flexbox** layouts
- **Media queries** cho breakpoints

## 🚀 Nâng cấp lên Symfony đầy đủ

### Khi muốn sử dụng Symfony đầy đủ:

1. **Cài đặt Composer** (nếu chưa có)
2. **Cài đặt dependencies**:
   ```bash
   php composer.phar install
   ```
3. **Sử dụng Symfony CLI**:
   ```bash
   symfony server:start
   ```

### Lợi ích của Symfony đầy đủ:
- Twig templates
- Doctrine ORM
- Security system
- Form handling
- Validation
- Caching
- Profiler

## 📝 Ghi chú

- **Phiên bản hiện tại**: Hoạt động không cần Composer
- **Tương thích**: PHP 7.4+ (khuyến nghị PHP 8.0+)
- **Database**: Chưa cần (static content)
- **Performance**: Tối ưu cho development

## 🤝 Đóng góp

1. Fork dự án
2. Tạo feature branch
3. Commit changes
4. Push to branch
5. Tạo Pull Request

---

**Lưu ý**: Đây là phiên bản đơn giản để development nhanh. Khi cần production, hãy sử dụng Symfony đầy đủ với Composer.
