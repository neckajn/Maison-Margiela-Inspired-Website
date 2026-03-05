<?php
session_start();
include 'db.php';

$error_message = "";
$is_admin = isset($_GET['admin']) && $_GET['admin'] == 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($is_admin) {
        $sql = "SELECT username, password FROM admin WHERE username = ?";
    } else {
        $sql = "SELECT account_id, username, password FROM accounts WHERE username = ?";
    }

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                if ($is_admin) {
                    header("Location: admin_dashboard.php");
                } else {
                    $_SESSION['account_id'] = $user['account_id'];
                    header("Location: shop.php");
                }
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no">
    <title><?php echo $is_admin ? "Admin Login" : "User Login"; ?></title>
    <link rel="stylesheet" href="login.css">
    <style>
        .error-container {
            width: 100%;
            margin-top: 10px;
            text-align: center;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-left: 10px;
        }
        .BACK {
            font-size: 16px; 
            color: #1d1d1d; 
            text-decoration: none; 
            font-weight: 550; 
            padding: 10px 20px;
            position: fixed;
            bottom: 10px; /* Adjust to move higher or lower */
            left: 51%; 
            transform: translateX(-50%);
            font-family: 'Goudy', sans-serif;
            z-index: 1000; /* Ensures it stays above other elements */
        }
    </style>
</head>
<body>
    <div class="mmlogo-container">
        <img src="images/mmlogo.png" alt="Logo" class="mmlogo">
    </div>

    <div class="login-container">
        <h2></h2>
        <form action="login.php<?php echo $is_admin ? '?admin=1' : ''; ?>" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
        </form>
        <div class="error-container">
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </div>
        
        <?php if (!$is_admin): ?>
            <p class="signup-text">No account? <a href="signup.php">Sign up here</a></p>
            <p class="signup-text">Admin? <a href="login.php?admin=1">Sign in here</a></p>
        <?php endif; ?>

        <!-- "BACK" button now functions as the 'Back to User Login' link when in Admin mode -->
        <a href="<?php echo $is_admin ? 'login.php' : 'mm1.html'; ?>" class="BACK">BACK</a>
    </div>
</body>
</html>
