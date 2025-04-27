<?php
session_start();
include('config.php');

if (!isset($_GET['type'])) {
    header("Location: index.html");
    exit();
}

$type = $_GET['type'];

if ($type == 'farmer' && isset($_SESSION['farmer_id'])) {
    $farmer_id = $_SESSION['farmer_id'];
    
    // Delete farmer and related products (cascading delete)
    $sql = "DELETE FROM farmers WHERE farmer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $farmer_id);
    
    if ($stmt->execute()) {
        session_destroy();
        header("Location: index.html?message=Account deleted successfully");
    } else {
        header("Location: farmer_dashboard.php?error=Error deleting account");
    }
    
    $stmt->close();
} elseif ($type == 'user' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Delete user and related orders (cascading delete)
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        session_destroy();
        header("Location: index.html?message=Account deleted successfully");
    } else {
        header("Location: user_dashboard.php?error=Error deleting account");
    }
    
    $stmt->close();
} else {
    header("Location: index.html");
}

exit();
?>