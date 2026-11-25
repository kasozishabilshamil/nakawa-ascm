<?php
session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Find user by email
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] == 'farmer') {
                header("Location: farmer_dashboard.php");
            } elseif ($user['role'] == 'vendor') {
                header("Location: vendor_dashboard.php");
            } elseif ($user['role'] == 'transporter') {
                header("Location: transporter_dashboard.php");
            }
            exit();
        } else {
            $message = "<div class='alert error'>Incorrect password. Please try again.</div>";
        }
    } else {
        $message = "<div class='alert error'>No account found with that email.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Nakawa ASCM</title>
    <link rel="stylesheet" href="css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="auth-body">

    <!-- Left Side: Image and Branding -->
    <div class="auth-image">
        <a href="index.php" class="auth-logo-top"><i class="fa-solid fa-leaf"></i> Nakawa ASCM</a>
        <h1>Welcome Back to the Market.</h1>
        <p>Log in to manage your produce, track your deliveries, and grow your business.</p>
    </div>

    <!-- Right Side: Form -->
    <div class="auth-form-container">
        <div class="auth-box">
            <div class="auth-header">
                <i class="fa-solid fa-leaf"></i>
                <h2>Welcome Back</h2>
                <p>Login to your Nakawa ASCM account</p>
            </div>

            <?php echo $message; ?>

            <form action="login.php" method="POST" class="auth-form">
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter your password">
                </div>

                <button type="submit" class="btn-block">Login to Dashboard</button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>

</body>
</html>