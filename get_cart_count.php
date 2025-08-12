<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in', 'count' => 0]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get cart count
$sql = "SELECT SUM(quantity) as count FROM cart WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

$count = $row['count'] ? (int)$row['count'] : 0;

echo json_encode([
    'success' => true,
    'count' => $count
]);
?>
