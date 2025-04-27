<?php 
session_start();
include('config.php');

if (!isset($_SESSION['farmer_id'])) {
    header("Location: farmer_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: farmer_dashboard.php");
    exit();
}

$product_id = $_GET['id'];
$farmer_id = $_SESSION['farmer_id'];

// Check if product belongs to the logged-in farmer
$sql = "SELECT * FROM products WHERE product_id = ? AND farmer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $product_id, $farmer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: farmer_dashboard.php");
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];
    
    $sql = "UPDATE products SET name = ?, description = ?, price = ?, quantity = ?, category = ? WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdisi", $name, $description, $price, $quantity, $category, $product_id);
    
    if ($stmt->execute()) {
        header("Location: farmer_dashboard.php");
        exit();
    } else {
        $error = "Error updating product: " . $stmt->error;
    }
    
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
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
            max-width: 600px;
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

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #4CAF50;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        textarea {
            height: 100px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <h1>Edit Product</h1>
    </header>
    <nav>
        <a href="farmer_dashboard.php">Dashboard</a>
        <a href="add_product.php">Add Product</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <h2>Edit Product</h2>
        
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price ($):</label>
                <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" min="1" value="<?php echo $product['quantity']; ?>" required>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <option value="Vegetables" <?php echo ($product['category'] == 'Vegetables') ? 'selected' : ''; ?>>Vegetables</option>
                    <option value="Fruits" <?php echo ($product['category'] == 'Fruits') ? 'selected' : ''; ?>>Fruits</option>
                    <option value="Dairy" <?php echo ($product['category'] == 'Dairy') ? 'selected' : ''; ?>>Dairy</option>
                    <option value="Grains" <?php echo ($product['category'] == 'Grains') ? 'selected' : ''; ?>>Grains</option>
                    <option value="Meat" <?php echo ($product['category'] == 'Meat') ? 'selected' : ''; ?>>Meat</option>
                    <option value="Other" <?php echo ($product['category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <button type="submit">Update Product</button>
        </form>
    </div>
</body>
</html>