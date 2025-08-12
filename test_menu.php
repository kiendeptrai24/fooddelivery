<?php
require_once 'config/database.php';

echo "<h2>Kiểm tra bảng menu_items</h2>";

// Kiểm tra kết nối database
if ($conn) {
    echo "<p style='color: green;'>✅ Kết nối database thành công!</p>";
    
    // Kiểm tra bảng menu_items
    $check_table = "SHOW TABLES LIKE 'menu_items'";
    $result = mysqli_query($conn, $check_table);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<p style='color: green;'>✅ Bảng menu_items tồn tại!</p>";
        
        // Đếm số lượng món ăn
        $count_sql = "SELECT COUNT(*) as total FROM menu_items";
        $count_result = mysqli_query($conn, $count_sql);
        $count = mysqli_fetch_assoc($count_result);
        
        echo "<p>📊 Tổng số món ăn: <strong>{$count['total']}</strong></p>";
        
        // Hiển thị một số món ăn mẫu
        $sample_sql = "SELECT m.*, r.name as restaurant_name 
                       FROM menu_items m 
                       JOIN restaurants r ON m.restaurant_id = r.id 
                       LIMIT 5";
        $sample_result = mysqli_query($conn, $sample_sql);
        
        if (mysqli_num_rows($sample_result) > 0) {
            echo "<h3>Món ăn mẫu:</h3>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #f0f0f0;'>";
            echo "<th>ID</th><th>Tên món</th><th>Nhà hàng</th><th>Danh mục</th><th>Giá</th>";
            echo "</tr>";
            
            while ($item = mysqli_fetch_assoc($sample_result)) {
                echo "<tr>";
                echo "<td>{$item['id']}</td>";
                echo "<td>{$item['name']}</td>";
                echo "<td>{$item['restaurant_name']}</td>";
                echo "<td>{$item['category']}</td>";
                echo "<td>" . number_format($item['price']) . "đ</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Kiểm tra số lượng món ăn theo nhà hàng
        echo "<h3>Số lượng món ăn theo nhà hàng:</h3>";
        $restaurant_menu_sql = "SELECT r.name, COUNT(m.id) as menu_count 
                                FROM restaurants r 
                                LEFT JOIN menu_items m ON r.id = m.restaurant_id
                                GROUP BY r.id, r.name 
                                ORDER BY r.name";
        $restaurant_menu_result = mysqli_query($conn, $restaurant_menu_sql);
        
        if (mysqli_num_rows($restaurant_menu_result) > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #f0f0f0;'>";
            echo "<th>Nhà hàng</th><th>Số món ăn</th>";
            echo "</tr>";
            
            while ($row = mysqli_fetch_assoc($restaurant_menu_result)) {
                echo "<tr>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['menu_count']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Bảng menu_items không tồn tại!</p>";
        echo "<p>Hãy chạy file SQL để tạo bảng.</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Không thể kết nối database!</p>";
}

echo "<br><a href='restaurants.php'>← Quay lại trang nhà hàng</a>";
?>
