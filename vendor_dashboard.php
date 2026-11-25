<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is a vendor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vendor') {
    header("Location: login.php");
    exit();
}

$vendor_id = $_SESSION['user_id'];
$message = "";

// Handle Order Placement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    $produce_id = mysqli_real_escape_string($conn, $_POST['produce_id']);
    $quantity_ordered = mysqli_real_escape_string($conn, $_POST['quantity_ordered']);

    // Get produce details to calculate total price
    $prodQuery = "SELECT price_per_unit, quantity, product_name FROM produce WHERE id = '$produce_id'";
    $prodResult = $conn->query($prodQuery);
    
    if ($prodResult->num_rows == 1) {
        $produce = $prodResult->fetch_assoc();
        
        if ($quantity_ordered > $produce['quantity']) {
            $message = "<div class='alert error'>Not enough stock available!</div>";
        } else {
            $total_price = $produce['price_per_unit'] * $quantity_ordered;

            // Insert Order
            $orderSql = "INSERT INTO orders (vendor_id, produce_id, quantity_ordered, total_price) 
                         VALUES ('$vendor_id', '$produce_id', '$quantity_ordered', '$total_price')";
            
            if ($conn->query($orderSql) === TRUE) {
                // Deduct the quantity from the produce table
                $newQty = $produce['quantity'] - $quantity_ordered;
                $updateStatus = ($newQty <= 0) ? "sold_out" : "available";
                
                $updateSql = "UPDATE produce SET quantity = '$newQty', status = '$updateStatus' WHERE id = '$produce_id'";
                $conn->query($updateSql);

                $message = "<div class='alert success'>Order placed successfully for " . htmlspecialchars($produce['product_name']) . "!</div>";
            } else {
                $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
            }
        }
    }
}

// Fetch all available produce (joining with users table to get farmer names)
$marketQuery = "SELECT produce.*, users.full_name as farmer_name 
                FROM produce 
                JOIN users ON produce.farmer_id = users.id 
                WHERE produce.status = 'available' 
                ORDER BY produce.created_at DESC";
$marketResult = $conn->query($marketQuery);
$marketList = [];
while($row = $marketResult->fetch_assoc()) {
    $marketList[] = $row;
}

// Fetch vendor's order history
$orderQuery = "SELECT orders.*, produce.product_name, users.full_name as farmer_name 
               FROM orders 
               JOIN produce ON orders.produce_id = produce.id 
               JOIN users ON produce.farmer_id = users.id 
               WHERE orders.vendor_id = '$vendor_id' 
               ORDER BY orders.created_at DESC";
$orderResult = $conn->query($orderQuery);
$orderList = [];
$pendingOrders = 0;

while($row = $orderResult->fetch_assoc()) {
    $orderList[] = $row;
    if ($row['order_status'] == 'pending') {
        $pendingOrders++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Marketplace | Nakawa ASCM</title>
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
            <li><a href="vendor_dashboard.php" class="active"><i class="fa-solid fa-store"></i> <span>Marketplace</span></a></li>
            <li><a href="#my-orders"><i class="fa-solid fa-clipboard-list"></i> <span>My Orders</span></a></li>
            <li><a href="#prices"><i class="fa-solid fa-chart-line"></i> <span>Market Prices</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <h1>Vendor Marketplace</h1>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <div class="user-avatar"><i class="fa-solid fa-user"></i></div>
            </div>
        </div>

        <?php echo $message; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fa-solid fa-store"></i>
                <div>
                    <h3>Available Products</h3>
                    <p><?php echo count($marketList); ?></p>
                </div>
            </div>
            <div class="stat-card" style="border-left-color: #e67e22;">
                <i class="fa-solid fa-truck" style="color: #e67e22; background: #fff3e0;"></i>
                <div>
                    <h3>Pending Deliveries</h3>
                    <p><?php echo $pendingOrders; ?></p>
                </div>
            </div>
        </div>

        <!-- Marketplace -->
        <div class="content-section">
            <h2 class="section-header">Fresh Produce from Farmers</h2>
            
            <div class="marketplace-grid">
                <?php if (count($marketList) > 0): ?>
                    <?php foreach ($marketList as $item): ?>
                        <div class="product-card">
                            <div class="product-header">
                                <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                <span class="category-tag"><?php echo htmlspecialchars($item['category']); ?></span>
                            </div>
                            <div class="product-body">
                                <p><span>Farmer:</span> <strong><?php echo htmlspecialchars($item['farmer_name']); ?></strong></p>
                                <p><span>Available:</span> <strong><?php echo htmlspecialchars($item['quantity']) . " " . htmlspecialchars($item['unit']); ?></strong></p>
                                <div class="product-price">UGX <?php echo number_format($item['price_per_unit']); ?> <span style="font-size:0.8rem; color:#666;">/ <?php echo htmlspecialchars($item['unit']); ?></span></div>
                            </div>
                            <div class="product-footer">
                                <form action="vendor_dashboard.php" method="POST" class="order-form">
                                    <input type="hidden" name="produce_id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="quantity_ordered" min="1" max="<?php echo $item['quantity']; ?>" value="1" required>
                                    <button type="submit" name="place_order" class="btn-green">Order Now</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #888;">No produce available in the market right now. Check back later!</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- My Orders -->
        <div class="content-section" id="my-orders">
            <h2 class="section-header">My Order History</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Farmer</th>
                            <th>Quantity</th>
                            <th>Total Price (UGX)</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orderList) > 0): ?>
                            <?php foreach ($orderList as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($order['product_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($order['farmer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['quantity_ordered']); ?></td>
                                    <td><?php echo number_format($order['total_price']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $order['order_status'] == 'pending' ? 'badge-sold' : 'badge-available'; ?>">
                                            <?php echo ucfirst($order['order_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #888;">You haven't placed any orders yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</body>
</html>