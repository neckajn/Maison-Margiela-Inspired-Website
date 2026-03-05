<?php
session_start();
include 'db.php';

if (isset($_POST['logout'])) {
    session_unset(); 
    session_destroy(); 
    header("Location: mm1.html"); 
    exit();
}
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
 
    $sql = "SELECT username, password FROM admin WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
 
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['username'] = $admin['username'];
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Database query failed.";
    }
} // <-- This closing bracket was missing!


// fetch accounts
$accounts_query = "SELECT * FROM accounts";
$accounts_result = $conn->query($accounts_query);

if (isset($_GET['delete_account_id'])) {
    $delete_account_id = $conn->real_escape_string($_GET['delete_account_id']);

    $check_user_sql = "SELECT * FROM accounts WHERE account_id = '$delete_account_id'";
    $check_user_result = $conn->query($check_user_sql);

    if ($check_user_result->num_rows == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'User account not found or cannot be deleted.'
        ]);
        exit;
    }

    
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_order_id'])) {
    $orderId = $_GET['delete_order_id'];

    $orderId = intval($orderId);

    $delete_items_sql = "DELETE FROM order_items WHERE order_id = ?";
    if ($stmt = $conn->prepare($delete_items_sql)) {
        $stmt->bind_param("i", $orderId);
        if (!$stmt->execute()) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete order items: ' . $stmt->error
            ]);
            $stmt->close();
            exit;
        }
        $stmt->close();
    }

    $delete_order_sql = "DELETE FROM orders WHERE order_id = ?";
    if ($stmt = $conn->prepare($delete_order_sql)) {
        $stmt->bind_param("i", $orderId);
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Order deleted successfully.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete order: ' . $stmt->error
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $conn->error
        ]);
    }

    $conn->close();
    exit;
}

    // delete the user account from the database
    $delete_user_sql = "DELETE FROM accounts WHERE account_id = '$delete_account_id'";
    if ($conn->query($delete_user_sql) === TRUE) {
        echo json_encode([
            'success' => true,
            'message' => 'User account deleted successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $conn->error
        ]);
    }
    exit;
}

if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // delete the order from the database
    $sql = "DELETE FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        echo "Order deleted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}


// fetch orders
$orders_query = "SELECT * FROM orders";
$orders_result = $conn->query($orders_query);


  // fetch product count
$productCountQuery = "SELECT COUNT(*) AS product_count FROM products";
$productResult = $conn->query($productCountQuery);
$productCount = $productResult->fetch_assoc()['product_count'];

// fetch user count 
$accountCountQuery = "SELECT COUNT(*) AS account_count FROM accounts";
$accountResult = $conn->query($accountCountQuery);
$accountCount = $accountResult->fetch_assoc()['account_count'];

// adding a new product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_name']) && isset($_FILES['product_image'])) {
    
    $product_id = $conn->real_escape_string($_POST['product_id']);
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $product_price = $conn->real_escape_string($_POST['product_price']);
    $product_stock = $conn->real_escape_string($_POST['product_stock']);

    $image = $_FILES['product_image']['name'];
    $image_tmp_name = $_FILES['product_image']['tmp_name'];
    $target_dir = "images/"; 
    $target_file = $target_dir . basename($image); 

    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($image_file_type, $allowed_types)) {
        if (move_uploaded_file($image_tmp_name, $target_file)) {
            $stmt = $conn->prepare("INSERT INTO products (product_id, name, price, stock, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isdis", $product_id, $product_name, $product_price, $product_stock, $image);
            $stmt->execute();
        } else {
            $error_message = "Error uploading image.";
        }
    } else {
        $error_message = "Invalid image type. Only JPG, JPEG, PNG, GIF files are allowed.";
    }
}

$query = "SELECT orders.order_id, orders.account_id, orders.total_price, 
                 MIN(order_items.order_date) AS order_date 
          FROM orders
          JOIN order_items ON orders.order_id = order_items.order_id
          GROUP BY orders.order_id, orders.account_id, orders.total_price
          ORDER BY order_date DESC";  // Show newest orders first

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}


$insertOrderItem = "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)";
$stmt2 = $conn->prepare($insertOrderItem);
if (!$stmt2) {
    die("Prepare failed for order_items: " . $conn->error);
}
$stmt2->bind_param("iii", $order_id, $product_id, $quantity);
$stmt2->execute();


$query = "SELECT DATE_FORMAT(order_date, '%Y-%m') AS month, COUNT(*) AS total_sales 
          FROM order_items
          GROUP BY YEAR(order_date), MONTH(order_date)
          ORDER BY YEAR(order_date), MONTH(order_date)";

$result = $conn->query($query);

$salesData = [];
while ($row = $result->fetch_assoc()) {
    $salesData[] = $row;
}

$query = "SELECT DATE_FORMAT(order_date, '%Y-%m') AS month, COUNT(*) AS total_sales 
          FROM order_items
          GROUP BY month";
$result = $conn->query($query);

$salesData = [];
while ($row = $result->fetch_assoc()) {
    $salesData[] = $row;
}

// Ensure target file and image variables are set
if (isset($target_file, $image_tmp_name, $product_name, $product_price, $product_stock)) {
    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($image_file_type, $allowed_types)) {
        if (move_uploaded_file($image_tmp_name, $target_file)) {
            $stmt = $conn->prepare("INSERT INTO products (name, price, stock, image) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                die("Prepare failed for products: " . $conn->error);
            }
            $stmt->bind_param("sdis", $product_name, $product_price, $product_stock, $image);
            $stmt->execute();
        } else {
            $error_message = "Error uploading image.";
        }
    } else {
        $error_message = "Invalid image type. Only JPG, JPEG, PNG, GIF files are allowed.";
    }
}

// Edit Product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editProductId'])) {
    $productId = $_POST['editProductId'];
    $productName = $_POST['editProductName'];
    $productPrice = $_POST['editProductPrice'];
    $productStock = $_POST['editProductStock'];

    // Update product
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, stock = ? WHERE product_id = ?");
    if (!$stmt) {
        die("Prepare failed for update: " . $conn->error);
    }
    $stmt->bind_param("sdii", $productName, $productPrice, $productStock, $productId);

    if ($stmt->execute()) {
        $success_message = "Product updated successfully!";
    } else {
        $error_message = "Error updating product: " . $stmt->error;
    }
}

// assuming the order and items are being deleted
if (isset($_GET['delete_order_id'])) {
    $orderId = $_GET['delete_order_id'];

    // start a transaction to ensure consistency
    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $quantity = $row['quantity'];

            $stmt_update = $conn->prepare("UPDATE products SET stock = stock + ? WHERE product_id = ?");
            $stmt_update->bind_param("ii", $quantity, $product_id);
            $stmt_update->execute();
        }

        $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();

        $conn->commit();
        echo "Order and stock updated successfully.";
    } catch (Exception $e) {
        
        $conn->rollback();
        echo "Error deleting order: " . $e->getMessage();
    }

    
    $stmt->close();
    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['products'])) {
    $order_id = $_POST['order_id']; 
    $products = $_POST['products']; 
    
    
    $conn->begin_transaction();

    try {
        
        foreach ($products as $product) {
            $product_id = $product['product_id']; 
            $quantity_ordered = $product['quantity']; 

            $check_stock_sql = "SELECT stock FROM products WHERE product_id = ?";
            $stmt = $conn->prepare($check_stock_sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $stmt->bind_result($current_stock);
            $stmt->fetch();
            $stmt->close();

            
            if ($current_stock >= $quantity_ordered) {
                
                $update_stock_sql = "UPDATE products SET stock = stock - ? WHERE product_id = ?";
                $stmt = $conn->prepare($update_stock_sql);
                $stmt->bind_param("ii", $quantity_ordered, $product_id);
                $stmt->execute();
                $stmt->close();

        
                $insert_order_sql = "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($insert_order_sql);
                $stmt->bind_param("iii", $order_id, $product_id, $quantity_ordered);
                $stmt->execute();
                $stmt->close();
            } else {
                
                throw new Exception("Not enough stock for product ID $product_id");
            }
        }

        $update_stock_sql = "UPDATE products SET stock = stock - ? WHERE product_id = ?";
        $stmt = $conn->prepare($update_stock_sql);
            if ($stmt === false) {
            die('Error preparing statement: ' . $conn->error);
}

        $stmt->bind_param("ii", $quantity_ordered, $product_id);
        $stmt->execute();

            if ($stmt->affected_rows === 0) {
            echo "No rows were updated. Check stock value or product_id.";
}
        $stmt->close();
    
        $conn->commit();
        echo "Order placed successfully, stock updated!";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}

if (isset($_GET['delete_product_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_product_id']);

    $check_sql = "SELECT * FROM products WHERE product_id = '$delete_id'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found.'
        ]);
        exit;
    }

    $image_query = "SELECT image FROM products WHERE product_id = '$delete_id'";
    $image_result = $conn->query($image_query);

    if ($image_result->num_rows > 0) {
        $image_row = $image_result->fetch_assoc();
        $image_path = "images/" . $image_row['image'];

        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    $delete_sql = "DELETE FROM products WHERE product_id = '$delete_id'";
    if ($conn->query($delete_sql) === TRUE) {
        echo json_encode([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $conn->error
        ]);
    }
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $order_items = $_POST['order_items']; 

    
    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO orders (order_id, account_id, order_date) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $order_id, $_SESSION['account_id'], date('Y-m-d H:i:s'));
        $stmt->execute();

        foreach ($order_items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];

            
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $order_id, $product_id, $quantity);
            $stmt->execute();

            
            $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
            $stmt->bind_param("ii", $quantity, $product_id);
            $stmt->execute();
        }

        $conn->commit();
        echo "Order placed successfully and stock updated.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error placing order: " . $e->getMessage();
    }

    
    $stmt->close();
    $conn->close();
}

$orders_per_month_query = "
    SELECT DATE_FORMAT(MIN(order_items.order_date), '%Y-%m') AS order_month, 
           COUNT(DISTINCT orders.order_id) AS order_count
    FROM orders
    JOIN order_items ON orders.order_id = order_items.order_id
    GROUP BY order_month
    ORDER BY order_month ASC";


// fetch all products from the database
$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <body>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <div class="maisonmargiela-container">Maison Margiela</div>
    <div class="paris-container">P A R I S</div>
    

    <div id="signout-container">
        <form method="post">
            <button type="submit" name="logout" id="signout-button">Sign Out</button>
        </form>
    </div>

    <style>
        #signout-container {
            position: fixed;
            top: 20px;
            right: 20px; 
            z-index: 1000;
        }

        #signout-button {
            background-color:rgb(255, 255, 255);
            color: #1d1d1d;
            border: 2px solid #1d1d1d;
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #signout-button:hover {
            background-color: #1d1d1d;
            color:rgb(255, 255, 255);
            border-color: #1d1d1d;
        }
    </style>

    <div class="container">
        <div class="sidebar">
            <ul>
                <li><a href="javascript:void(0)" onclick="showContent('dashboard')">Dashboard</a></li>
                <li><a href="javascript:void(0)" onclick="showContent('products')">Products</a></li>
                <li><a href="javascript:void(0)" onclick="showContent('orders')">Orders</a></li>
                <li><a href="javascript:void(0)" onclick="showContent('accounts')">Accounts</a></li>
            </ul>
        </div>


        <div class="main-content">

    <div id="dashboard" class="content-section" style="display:block;">
    <h2 style="
        font-family: 'Goudy', sans-serif; 
        font-size: 24px; color: #1d1d1d;
        margin-left: 390px;
        margin-top: 70px;"
    >
        Dashboard Information
    </h2>
            
    <div class="stats-container" style="
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 40px; /* Adds spacing between the items */
        margin-top: 30px; /* Optional for spacing above */
    ">
        <!-- products count -->
        <div class="stat-item" style="text-align: center;">
            <h3 style="
                font-family: 'Goudy', sans-serif; 
                font-size: 24px; color: #c7c7c7;"
            >
                Total Products
            </h3>
            <p style="font-family: 'Goudy', sans-serif; font-size: 18px; color: #fff;">
                <?php echo $productCount; ?>
            </p>
        </div>
        
        <!-- user accounts count -->
        <div class="stat-item" style="text-align: center;">
            <h3 style="
                font-family: 'Goudy', sans-serif; 
                font-size: 24px; color: #c7c7c7;"
            >
                Total<br>Users
            </h3>
            <p style="font-family: 'Goudy', sans-serif; font-size: 18px; color: #fff;">
                <?php echo $accountCount; ?> 
            </p>
        </div>
    </div>
</div>

<div id="orders" class="content-section" style="display:none;">
    <h2 style="
        font-family: 'Goudy', sans-serif; 
        font-size: 24px; 
        color: #1d1d1d;
        margin-left: 549px;">
        Order Information
    </h2>
    
    <table class="orders-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Account ID</th>
                <th>Total Price</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($orders_result->num_rows > 0) {
                while ($row = $orders_result->fetch_assoc()) {
                    echo "<tr>";  
                    echo "<td>" . $row['order_id'] . "</td>";
                    echo "<td>" . $row['account_id'] . "</td>";
                    echo "<td>" . $row['total_price'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['order_date']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3' style='text-align: center;'>No orders found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>


            
<div id="accounts" class="content-section" style="display:none;">
    <h2 style="
        font-family: 'Goudy', sans-serif; 
        font-size: 24px; 
        color: #1d1d1d;
        margin-left: 565px;"
    >
        User Accounts
    </h2>

    <?php if (isset($account_error_message)) echo "<p class='error-message'>$account_error_message</p>"; ?>
    <?php if (isset($account_success_message)) echo "<p class='success-message'>$account_success_message</p>"; ?>

    <div id="accountList">
        <?php
        if ($accounts_result->num_rows > 0) {
            echo "<table class='accounts-table'>";
            echo "<tr><th>User ID</th><th>Username</th><th>Email</th><th>Actions</th></tr>";

            while ($account = $accounts_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($account['account_id']) . "</td>";
                echo "<td>" . htmlspecialchars($account['username']) . "</td>";
                echo "<td>" . htmlspecialchars($account['email']) . "</td>";
                echo "<td>
                        <button class='delete-account-btn' onclick='deleteAccount(\"" . htmlspecialchars($account['account_id'], ENT_QUOTES) . "\")'>DELETE</button>
                      </td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p class='right-margin-text'>No user accounts found.</p>";
        }
        ?>
    </div>
</div>

            <div id="products" class="content-section" style="display:none;">
                <?php if (isset($error_message)) echo "<p class='error-message'>$error_message</p>"; ?>
                <?php if (isset($success_message)) echo "<p class='success-message'>$success_message</p>"; ?>

                <button class="add-product" id="addProductBtn" onclick="showAddProductForm()">Add Product</button>

                <div id="productList">
                    <?php
                    if ($result->num_rows > 0) {
                        echo "<table class='product-table'>";
                        echo "<tr><th>Product ID</th><th>Image</th><th>Name</th><th>Price</th><th>Stock</th><th>Actions</th></tr>";

                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['product_id']) . "</td>";

                            $image = "images/" . htmlspecialchars($row['image']);
                            echo "<td><img src='$image' alt='Product Image' width='100'></td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>€" . number_format($row['price'], 2) . "</td>";
                            echo "<td>" . htmlspecialchars($row['stock']) . "</td>";
                            echo "<td>
                                    <button class ='edit-btn' onclick=\"showEditProductForm('" . htmlspecialchars($row['product_id']) . "', '" . htmlspecialchars($row['name']) . "', '" . htmlspecialchars($row['price']) . "', '" . htmlspecialchars($row['stock']) . "')\">EDIT</button>
                                    <button class='delete-btn' onclick='deleteProduct(\"" . htmlspecialchars($row['product_id'], ENT_QUOTES) . "\")'>DELETE</button>
                                </td>";
                            echo "</tr>";
                        }

                        echo "</table>";
                    } else {
                        echo "<p class='right-margin-text'>No products available.</p>";
                    }
                    ?>
                </div>

                <div id="productFormContainer"></div>
            </div>
            
            <!-- add product form modal -->
    <div class="popup" id="addProductPopup">
        <div class="modal-content">
            <h2>Add New Product</h2>
            <form id="addProductForm" action="admin_dashboard.php" method="POST" enctype="multipart/form-data">
                <label for="productID">Product ID:</label>
                <input type="number" id="productID" name="product_id" required><br><br>    

                <label for="productName">Product Name:</label>
                <input type="text" id="productName" name="product_name" required><br><br>

                <label for="productPrice">Price:</label>
                <input type="number" id="productPrice" name="product_price" required><br><br>

                <label for="productStock">Stock:</label>
                <input type="number" id="productStock" name="product_stock" required><br><br>

                <label for="productImage">Product Image:</label>
                <input type="file" id="productImage" name="product_image" accept="images/*" required><br><br>

                <div class="form-actions">
                    <input type="submit" value="Add">
                </div>
                <button type="button" class="close-btn" onclick="closeAddProductForm()">×</button>
            </form>
        </div>
    </div>

    <div id="overlay" class="overlay"></div>

            <!-- edit product modal -->
            <div id="editProductModal" class="modal">
                <div class="modal-content">
                    <h2>Edit Product</h2>
                    <form id="editProductForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="existing_image" name="existing_image" value="">

                        <label for="editProductId">Product ID:</label>
                        <input type="number" id="editProductId" name="editProductId" required>
                        
                        <label for="editProductName">Product Name:</label>
                        <input type="text" id="editProductName" name="editProductName" required>
                        
                        <label for="editProductPrice">Price:</label>
                        <input type="number" id="editProductPrice" name="editProductPrice" step="0.01" required>
                        
                        <label for="editProductStock">Stock:</label>
                        <input type="number" id="editProductStock" name="editProductStock" required>
                        
                        <div class="form-actions">
                            <button type="submit">Update</button>
                            <span class="close" onclick="closeEditProductForm()">&times;</span>
                        </div>
                    </form>
                </div>
            </div>

            <script src="admin_dashboard.js"></script>

            <style>

#editProductForm label {
    display: block;
    margin-bottom: 5px; 
    font-weight: bold; 
}

#editProductForm input[type="text"],
#editProductForm input[type="number"] {
    display: block;
    width: 100%;
    padding: 10px;
    margin-bottom: 20px; 
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box; 
}

#editProductModal {
    display: none;
    position: fixed;
    top: 50%;
    left: 53%;
    transform: translate(-50%, -50%) translateX(70px);
    color: black;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    font-size: 16px;
    z-index: 1000;
    font-family: 'Goudy', sans-serif;
    font-weight: bold;
    margin-top: 20px;
}

#editProductModal .modal-content {
    background-color:rgb(255, 255, 255);
    padding: 30px;
    border-radius: 8px;
    width: 450px;
    max-width: 90%;
    position: relative;
    font-family: 'Goudy', sans-serif;
}

#editProductModal .close {
    background-color: transparent;
    color: black;
    border: none;
    font-size: 24px;
    cursor: pointer;
    font-weight: bold;
    position: absolute; 
    top: 10px; 
    right: 20px; 
    z-index: 1001; 
}

#editProductModal .close:hover {
    color: rgb(213, 19, 19);
}

#editProductForm button[type="submit"] {
    background-color: #1d1d1d; 
    color: white; 
    padding: 8px 20px; 
    font-size: 15px; 
    border: none; 
    cursor: pointer; 
    border-radius: 5px; 
    transition: background-color 0.3s ease; 
    font-family: 'Goudy', sans-serif; 
    font-weight: bold; 
    margin-top: 3%;
}

#editProductModal button[type="submit"]:hover {
    background-color: #b9b9b9;
    color: #1d1d1d 
}


/*edit n delete button*/
.edit-btn {
    background-color: #1D1D1D; 
    color: white; 
    padding: 8px 15px; 
    border: none;
    cursor: pointer; 
    border-radius: 5px; 
    font-family: 'Arial', sans-serif;
    font-weight: bold;
    transition: background-color 0.3s ease;
    margin-bottom: 10px;
    right: 10%;
}

.edit-btn:hover {
    background-color:#b9b9b9;
    color: #1d1d1d; 
}

/* delete Button */
.delete-btn {
    background-color: #1D1D1D; 
    color: white; 
    padding: 8px 15px; 
    border: none; 
    cursor: pointer; 
    border-radius: 5px; 
    font-family: 'Arial', sans-serif;
    font-weight: bold;
    transition: background-color 0.3s ease; 
}

.delete-btn:hover {
    background-color:#b9b9b9;
    color: #1d1d1d; 
}

.orders-table {
    width: 120%;
    border-collapse: collapse; 
    margin-top: 20px;
    font-family: 'Goudy', sans-serif;
}

.orders-table th, .orders-table td {
    padding: 15px;
    text-align: center;
    border: 1px solid #ddd;
    font-size: 16px;
}

.orders-table th {
    background-color: #1d1d1d;
    color: white;
    font-weight: bold;
}

.orders-table tr:nth-child(even) {
    background-color: #f2f2f2;
}

.orders-table tr:hover {
    background-color: #ddd;
}


    </style>
</head>

<?php
// Database connection (assuming $conn is your PDO or MySQLi connection)

$salesData = [];
$query = "
    SELECT DATE_FORMAT(order_date, '%Y-%m') AS month, COUNT(order_id) AS total_sales 
    FROM orders 
    GROUP BY month 
    ORDER BY month ASC";
$result = $conn->query($query);

if ($result) {
    $salesData = $result->fetch_all(MYSQLI_ASSOC);
}
?>


<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Sales Chart Container (ONLY for Dashboard) -->
<div id="dashboard-sales-chart" class="sales-chart-container" style="display: none;">
    <canvas id="salesChart"></canvas>
</div>
<script>
function showContent(section) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(section => section.style.display = 'none');
    
    // Show selected section
    document.getElementById(section).style.display = 'block';

    // Show sales chart only on the dashboard
    if (section === 'dashboard') {
        document.getElementById('dashboard-sales-chart').style.display = 'block';
    } else {
        document.getElementById('dashboard-sales-chart').style.display = 'none';
    }
}

window.onload = function () {
    let salesData = <?php echo json_encode($salesData ?? []); ?>;

    if (!Array.isArray(salesData) || salesData.length === 0) {
        console.error("No sales data available.");
        return;
    }

    let labels = salesData.map(item => item.month);
    let data = salesData.map(item => Number(item.total_sales));

    new Chart(document.getElementById("salesChart"), {
    type: "bar",
    data: {
        labels: labels,
        datasets: [{
            label: "Monthly Sales",
            data: data.map(value => Math.round(value)), // Ensures whole numbers
            backgroundColor: "black"
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1, // Force whole number increments
                    callback: function(value) { return Number.isInteger(value) ? value : null; } // Hide decimals
                }
            }
        }
    }
});

    // Ensure the chart is visible on page load (Dashboard is default)
    document.getElementById('dashboard-sales-chart').style.display = 'block';
};
</script>

<!-- Style to Fix Chart Size -->
<style>
.sales-chart-container {
    width: 400px;
    height: 250px;
    margin: auto;
    background: white;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 2px 2px 10px rgba(255, 255, 255, 0.1);
    margin-left: 300px;
    margin-top: 20px;
}
canvas {
    width: 100% !important;
    height: 100% !important;
}
</style>