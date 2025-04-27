<?php 
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color:#206625;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        nav {
            display: flex;
            justify-content: center;
            background-color: #333;
            padding: 10px;
        }
        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
        }
        nav a:hover {
            background-color: #4CAF50;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .product-card h3 {
            color: #4CAF50;
            margin-bottom: 10px;
        }
        .product-card p {
            margin-bottom: 10px;
            color: #555;
        }
        .product-card .price {
            font-weight: bold;
            font-size: 1.2em;
            color: #333;
        }
        .product-card .quantity {
            color: #777;
        }
        .product-card .order-form {
            margin-top: 15px;
        }
        .product-card input[type="number"] {
            width: 60px;
            padding: 5px;
            margin-right: 10px;
        }
        .product-card button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .product-card button:hover {
            background-color: #45a049;
        }
        .category-filter {
            margin-bottom: 20px;
        }
        .category-filter select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <header>
        <h1>Browse Products</h1>
    </header>
    <nav>
        <a href="user_dashboard.php">Dashboard</a>
        <a href="products.php">Browse Products</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <div class="category-filter">
            <form method="get" action="">
                <label for="category">Filter by Category:</label>
                <select name="category" id="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <option value="Vegetables" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Vegetables') ? 'selected' : ''; ?>>Vegetables</option>
                    <option value="Fruits" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Fruits') ? 'selected' : ''; ?>>Fruits</option>
                    <option value="Dairy" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Dairy') ? 'selected' : ''; ?>>Dairy</option>
                    <option value="Grains" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Grains') ? 'selected' : ''; ?>>Grains</option>
                    <option value="Meat" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Meat') ? 'selected' : ''; ?>>Meat</option>
                    <option value="Other" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </form>
        </div>
        
        <div class="products">
            <?php
            $category = isset($_GET['category']) ? $_GET['category'] : '';
            $sql = "SELECT * FROM products WHERE quantity > 0";
            
            if (!empty($category)) {
                $sql .= " AND category = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $category);
            } else {
                $stmt = $conn->prepare($sql);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">
                        <h3>'.$row['name'].'</h3>
                        <p>'.$row['description'].'</p>
                        <p class="price">$'.$row['price'].'</p>
                        <p class="quantity">Available: '.$row['quantity'].'</p>
                        <div class="order-form">
                            <form method="post" action="place_order.php">
                                <input type="hidden" name="product_id" value="'.$row['product_id'].'">
                                <input type="number" name="quantity" min="1" max="'.$row['quantity'].'" value="1">
                                <button type="submit">Place Order</button>
                            </form>
                        </div>
                    </div>';
                }
            } else {
                echo '<p>No products found in this category.</p>';
            }
            
            $stmt->close();
            ?>
        </div>
    </div>
</body>
</html>