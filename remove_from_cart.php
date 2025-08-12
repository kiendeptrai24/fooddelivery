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
$user_id = $_SESSION['user_id'];

if ($cart_item_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item ID']);
    exit();
}

// Delete cart item
$sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $cart_item_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove item from cart']);
}
?>
