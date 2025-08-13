<?php
require_once 'config/database.php';

echo "<h2>Kiểm tra cấu trúc bảng menu_items</h2>";

if ($conn) {
    echo "<p style='color: green;'>✅ Kết nối database thành công!</p>";
    
    // Kiểm tra cấu trúc bảng menu_items
    $structure_sql = "DESCRIBE menu_items";
    $structure_result = mysqli_query($conn, $structure_sql);
    
    if ($structure_result) {
        echo "<h3>Cấu trúc bảng menu_items:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th>";
        echo "</tr>";
        
        while ($row = mysqli_fetch_assoc($structure_result)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "<td>{$row['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Kiểm tra dữ liệu mẫu
        echo "<h3>Dữ liệu mẫu (5 record đầu tiên):</h3>";
        $sample_sql = "SELECT * FROM menu_items LIMIT 5";
        $sample_result = mysqli_query($conn, $sample_sql);
        
        if (mysqli_num_rows($sample_result) > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #f0f0f0;'>";
            
            // Lấy tên cột từ kết quả đầu tiên
            $first_row = mysqli_fetch_assoc($sample_result);
            foreach ($first_row as $key => $value) {
                echo "<th>$key</th>";
            }
            echo "</tr>";
            
            // Hiển thị dữ liệu
            echo "<tr>";
            foreach ($first_row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
            
            // Hiển thị các record còn lại
            while ($row = mysqli_fetch_assoc($sample_result)) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Không thể lấy cấu trúc bảng!</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Không thể kết nối database!</p>";
}

echo "<br><a href='restaurants.php'>← Quay lại trang nhà hàng</a>";
?>

