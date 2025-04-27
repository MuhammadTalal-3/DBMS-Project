<?php 
session_start();
include('config.php');

if (!isset($_SESSION['farmer_id'])) {
    header("Location: farmer_login.php");
    exit();
}

$farmer_id = $_SESSION['farmer_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard</title>
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
        .container:hover{
            transform: scale(1.05);
    
    transition: transform 0.3s ease-in-out;
        }
        .welcome {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .btn-danger {
            background-color: #f44336;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .products {
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .action-links a {
            color: #4CAF50;
            text-decoration: none;
            margin-right: 10px;
        }
        .action-links a.delete {
            color: #f44336;
        }
    </style>
</head>
<body>
    <header>
        <h1>Farmer Dashboard</h1>
    </header>
    <nav>
        <a href="farmer_dashboard.php">Dashboard</a>
        <a href="add_product.php">Add Product</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <div class="welcome">
            <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
            <p>Manage your products and account.</p>
            <a href="delete_account.php?type=farmer" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete your account? This cannot be undone.');">Delete Account</a>
        </div>
        
        <div class="products">
            <h3>Your Products</h3>
            <?php
            $sql = "SELECT * FROM products WHERE farmer_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $farmer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo '<table>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>';
                
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>
                        <td>'.$row['name'].'</td>
                        <td>'.$row['description'].'</td>
                        <td>$'.$row['price'].'</td>
                        <td>'.$row['quantity'].'</td>
                        <td class="action-links">
                            <a href="edit_product.php?id='.$row['product_id'].'">Edit</a>
                            <a href="delete_product.php?id='.$row['product_id'].'" class="delete" onclick="return confirm(\'Are you sure you want to delete this product?\');">Delete</a>
                        </td>
                    </tr>';
                }
                
                echo '</table>';
            } else {
                echo '<p>No products found. <a href="add_product.php">Add your first product</a></p>';
            }
            
            $stmt->close();
            ?>
        </div>
    </div>
</body>
</html>