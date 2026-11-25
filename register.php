<?php
// Include database connection
include 'db_connect.php';

$message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    // Hash the password for security
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmail = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        $message = "<div class='alert error'>Email already registered. Please login.</div>";
    } else {
        // Insert user into database
        $sql = "INSERT INTO users (full_name, email, phone, password, role, location) 
                VALUES ('$full_name', '$email', '$phone', '$password', '$role', '$location')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert success'>Registration successful! <a href='login.php'>Login here</a></div>";
        } else {
            $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Nakawa ASCM</title>
    <link rel="stylesheet" href="css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="auth-body">

    <!-- Left Side: Image and Branding -->
    <div class="auth-image">
        <a href="index.php" class="auth-logo-top"><i class="fa-solid fa-leaf"></i> Nakawa ASCM</a>
        <h1>Join the Digital Market.</h1>
        <p>Connect with farmers, vendors, and transporters to grow your agricultural business today.</p>
    </div>

    <!-- Right Side: Form -->
    <div class="auth-form-container">
        <div class="auth-box">
            <div class="auth-header">
                <i class="fa-solid fa-seedling"></i>
                <h2>Create an Account</h2>
                <p>Join the Nakawa Market digital network</p>
            </div>

            <?php echo $message; ?>

            <form action="register.php" method="POST" class="auth-form">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required placeholder="e.g., David Kurama">
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="e.g., david@example.com">
                </div>

                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" required placeholder="e.g., 0771234567">
                </div>

                <div class="input-group">
                    <label>I am a...</label>
                    <select name="role" required>
                        <option value="" disabled selected>Select your role</option>
                        <option value="farmer">Farmer (I supply produce)</option>
                        <option value="vendor">Vendor (I buy produce)</option>
                        <option value="transporter">Transporter (I deliver goods)</option>
                        <option value="admin">Administrator (Market Admin)</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Location / Market Area</label>
                    <input type="text" name="location" required placeholder="e.g., Nakawa Division">
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Create a strong password">
                </div>

                <button type="submit" class="btn-block">Register Now</button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>

</body>
</html>