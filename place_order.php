<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || !isset($_POST['product_id']) || !isset($_POST['quantity'])) {
    header("Location: user_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];

// Check product availability
$sql = "SELECT quantity FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $product = $result->fetch_assoc();
    if ($product['quantity'] >= $quantity) {
        // Place order
        $insert_sql = "INSERT INTO orders (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
        
        if ($insert_stmt->execute()) {
            // Update product quantity
            $update_sql = "UPDATE products SET quantity = quantity - ? WHERE product_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $quantity, $product_id);
            $update_stmt->execute();
            $update_stmt->close();
            
            $_SESSION['message'] = "Order placed successfully!";
        } else {
            $_SESSION['error'] = "Error placing order";
        }
        
        $insert_stmt->close();
    } else {
        $_SESSION['error'] = "Not enough quantity available";
    }
} else {
    $_SESSION['error'] = "Product not found";
}

$stmt->close();
header("Location: user_dashboard.php");
exit();
?>