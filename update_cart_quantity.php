<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$cart_item_id = (int)$_POST['cart_item_id'];
$quantity = (int)$_POST['quantity'];
$user_id = $_SESSION['user_id'];

if ($cart_item_id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

// Update cart item quantity
$sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iii", $quantity, $cart_item_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    // Get updated item price for total calculation
    $sql = "SELECT mi.price FROM cart c 
            JOIN menu_items mi ON c.menu_item_id = mi.id 
            WHERE c.id = ? AND c.user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $cart_item_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $item = mysqli_fetch_assoc($result);
    
    $item_price = $item['price'] ?? 0;
    
    echo json_encode([
        'success' => true, 
        'message' => 'Quantity updated successfully',
        'item_price' => $item_price
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update quantity']);
}
?>
