<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit();
}

// Check if request is POST and contains JSON
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit();
}

$menu_item_id = isset($input['menu_item_id']) ? (int)$input['menu_item_id'] : 0;
$quantity = isset($input['quantity']) ? (int)$input['quantity'] : 1;
$restaurant_id = isset($input['restaurant_id']) ? (int)$input['restaurant_id'] : 0;

// Validation
if ($menu_item_id <= 0 || $quantity <= 0 || $restaurant_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ']);
    exit();
}

// Check if menu item exists and is available
$sql = "SELECT * FROM menu_items WHERE id = ? AND available = 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $menu_item_id);
mysqli_stmt_execute($stmt);
$menu_item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$menu_item) {
    echo json_encode(['success' => false, 'message' => 'Món ăn không tồn tại hoặc không khả dụng']);
    exit();
}

// Check if menu item belongs to the specified restaurant
if ($menu_item['restaurant_id'] != $restaurant_id) {
    echo json_encode(['success' => false, 'message' => 'Món ăn không thuộc nhà hàng này']);
    exit();
}

// Check if user already has items from a different restaurant in cart
$check_sql = "SELECT restaurant_id FROM cart WHERE user_id = ? AND restaurant_id != ? LIMIT 1";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "ii", $_SESSION['user_id'], $restaurant_id);
mysqli_stmt_execute($check_stmt);
$existing_cart = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($existing_cart) > 0) {
    echo json_encode(['success' => false, 'message' => 'Bạn chỉ có thể đặt món từ một nhà hàng trong một lần']);
    exit();
}

// Check if item already exists in cart
$existing_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND menu_item_id = ?";
$existing_stmt = mysqli_prepare($conn, $existing_sql);
mysqli_stmt_bind_param($existing_stmt, "ii", $_SESSION['user_id'], $menu_item_id);
mysqli_stmt_execute($stmt);
$existing_item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($existing_item) {
    // Update quantity
    $new_quantity = $existing_item['quantity'] + $quantity;
    $update_sql = "UPDATE cart SET quantity = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "ii", $new_quantity, $existing_item['id']);
    
    if (mysqli_stmt_execute($update_stmt)) {
        // Get updated cart count
        $count_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
        $count_stmt = mysqli_prepare($conn, $count_sql);
        mysqli_stmt_bind_param($count_stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($count_stmt);
        $count_result = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt));
        
        echo json_encode([
            'success' => true, 
            'message' => 'Đã cập nhật số lượng món ăn',
            'cart_count' => $count_result['total'] ?: 0
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật giỏ hàng']);
    }
} else {
    // Add new item to cart
    $insert_sql = "INSERT INTO cart (user_id, menu_item_id, quantity, restaurant_id) VALUES (?, ?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "iiii", $_SESSION['user_id'], $menu_item_id, $quantity, $restaurant_id);
    
    if (mysqli_stmt_execute($insert_stmt)) {
        // Get updated cart count
        $count_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
        $count_stmt = mysqli_prepare($conn, $count_sql);
        mysqli_stmt_bind_param($count_stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($count_stmt);
        $count_result = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt));
        
        echo json_encode([
            'success' => true, 
            'message' => 'Đã thêm món ăn vào giỏ hàng',
            'cart_count' => $count_result['total'] ?: 0
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể thêm vào giỏ hàng']);
    }
}
?>
