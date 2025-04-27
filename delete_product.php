<?php
session_start();
include('config.php');

if (!isset($_SESSION['farmer_id']) || !isset($_GET['id'])) {
    header("Location: farmer_login.php");
    exit();
}

$product_id = $_GET['id'];
$farmer_id = $_SESSION['farmer_id'];

// Verify the product belongs to the farmer before deleting
$sql = "DELETE FROM products WHERE product_id = ? AND farmer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $product_id, $farmer_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Product deleted successfully";
} else {
    $_SESSION['error'] = "Error deleting product";
}

$stmt->close();
header("Location: farmer_dashboard.php");
exit();
?>