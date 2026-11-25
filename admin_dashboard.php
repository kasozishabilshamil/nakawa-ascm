<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle Market Price Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_price'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $current_price = mysqli_real_escape_string($conn, $_POST['current_price']);

    // Check if product exists in market_prices
    $checkSql = "SELECT * FROM market_prices WHERE product_name = '$product_name'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult->num_rows > 0) {
        $sql = "UPDATE market_prices SET current_price = '$current_price' WHERE product_name = '$product_name'";
    } else {
        $sql = "INSERT INTO market_prices (product_name, current_price) VALUES ('$product_name', '$current_price')";
    }

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert success'>Market price updated successfully!</div>";
    } else {
        $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
    }
}

// 1. Fetch Global Stats (SAFER VERSION)
$totalUsersQuery = $conn->query("SELECT COUNT(*) as count FROM users");
$totalUsers = $totalUsersQuery->fetch_assoc()['count'] ?? 0;

$totalProduceQuery = $conn->query("SELECT COUNT(*) as count FROM produce");
$totalProduce = $totalProduceQuery->fetch_assoc()['count'] ?? 0;

$totalOrdersQuery = $conn->query("SELECT COUNT(*) as count FROM orders");
$totalOrders = $totalOrdersQuery->fetch_assoc()['count'] ?? 0;

$revenueQuery = $conn->query("SELECT SUM(total_price) as sum FROM orders");
$totalRevenue = $revenueQuery->fetch_assoc()['sum'] ?? 0;
if (!$totalRevenue) { $totalRevenue = 0; }

// 2. Fetch All Users
$usersQuery = "SELECT id, full_name, email, phone, role, location, created_at FROM users ORDER BY created_at DESC";
$usersResult = $conn->query($usersQuery);

// 3. Fetch All Recent Orders
$ordersQuery = "SELECT orders.*, produce.product_name, 
                vendor.full_name as vendor_name, farmer.full_name as farmer_name 
                FROM orders 
                JOIN produce ON orders.produce_id = produce.id 
                JOIN users vendor ON orders.vendor_id = vendor.id 
                JOIN users farmer ON produce.farmer_id = farmer.id 
                ORDER BY orders.created_at DESC LIMIT 10";
$ordersResult = $conn->query($ordersQuery);

// 4. Fetch Current Market Prices
$pricesQuery = "SELECT * FROM market_prices ORDER BY product_name ASC";
$pricesResult = $conn->query($pricesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Nakawa ASCM</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="dashboard-body">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="fa-solid fa-leaf"></i> Nakawa <span>ASCM</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php" class="active"><i class="fa-solid fa-gauge-high"></i> <span>Overview</span></a></li>
            <li><a href="#manage-users"><i class="fa-solid fa-users"></i> <span>Manage Users</span></a></li>
            <li><a href="#transactions"><i class="fa-solid fa-money-check-dollar"></i> <span>Transactions</span></a></li>
            <li><a href="#market-prices"><i class="fa-solid fa-tags"></i> <span>Market Prices</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <h1>Administrator Overview</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <div class="user-avatar"><i class="fa-solid fa-user-shield"></i></div>
            </div>
        </div>

        <?php echo $message; ?>

        <!-- Global Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fa-solid fa-users"></i>
                <div>
                    <h3>Total Users</h3>
                    <p><?php echo $totalUsers; ?></p>
                </div>
            </div>
            <div class="stat-card" style="border-left-color: #e67e22;">
                <i class="fa-solid fa-seedling" style="color: #e67e22; background: #fff3e0;"></i>
                <div>
                    <h3>Produce Listings</h3>
                    <p><?php echo $totalProduce; ?></p>
                </div>
            </div>
            <div class="stat-card" style="border-left-color: #1976d2;">
                <i class="fa-solid fa-box" style="color: #1976d2; background: #e3f2fd;"></i>
                <div>
                    <h3>Total Orders</h3>
                    <p><?php echo $totalOrders; ?></p>
                </div>
            </div>
            <div class="stat-card" style="border-left-color: #2e7d32;">
                <i class="fa-solid fa-money-bill-trend-up" style="color: #2e7d32; background: #e8f5e9;"></i>
                <div>
                    <h3>Market Revenue (UGX)</h3>
                    <p><?php echo number_format($totalRevenue); ?></p>
                </div>
            </div>
        </div>

        <!-- Two Column Layout for Prices and Orders -->
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-bottom: 30px;">
            
            <!-- Market Prices Form -->
            <div class="content-section" id="market-prices">
                <h2 class="section-header">Update Market Price</h2>
                <form action="admin_dashboard.php" method="POST" style="margin-bottom: 20px;">
                    <div class="input-group" style="margin-bottom: 10px;">
                        <label>Product Name</label>
                        <input type="text" name="product_name" required placeholder="e.g., Tomatoes">
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <label>Price per Kg/Unit (UGX)</label>
                        <input type="number" step="0.01" name="current_price" required placeholder="e.g., 3500">
                    </div>
                    <button type="submit" name="update_price" class="btn-green" style="width: 100%;">Update Price</button>
                </form>

                <h3 style="font-size: 1rem; margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">Current Prices</h3>
                <ul style="list-style: none; margin-top: 10px;">
                    <?php if ($pricesResult->num_rows > 0): ?>
                        <?php while($price = $pricesResult->fetch_assoc()): ?>
                            <li style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eee; font-size: 0.9rem;">
                                <span><?php echo htmlspecialchars($price['product_name']); ?></span>
                                <strong style="color: #2e7d32;">UGX <?php echo number_format($price['current_price']); ?></strong>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li style="color: #888; font-size: 0.9rem;">No prices set yet.</li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Recent Orders -->
            <div class="content-section" id="transactions">
                <h2 class="section-header">Recent Transactions</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Product</th>
                                <th>Buyer (Vendor)</th>
                                <th>Seller (Farmer)</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($ordersResult && $ordersResult->num_rows > 0): ?>
                                <?php while($order = $ordersResult->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($order['product_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($order['vendor_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['farmer_name']); ?></td>
                                        <td><?php echo number_format($order['total_price']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $order['order_status'] == 'delivered' ? 'badge-available' : 'badge-sold'; ?>">
                                                <?php echo ucfirst($order['order_status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; color: #888;">No transactions recorded yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Manage Users -->
        <div class="content-section" id="manage-users">
            <h2 class="section-header">Registered Users</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Location</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($usersResult && $usersResult->num_rows > 0): ?>
                            <?php while($user = $usersResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($user['full_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                    <td>
                                        <span class="badge" style="background: #e3f2fd; color: #1976d2;">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['location']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #888;">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</body>
</html>