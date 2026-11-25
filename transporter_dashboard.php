<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is a transporter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'transporter') {
    header("Location: login.php");
    exit();
}

$transporter_id = $_SESSION['user_id'];
$message = "";

// Handle Accepting an Order (Assigning to self)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accept_order'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $pickup = "Nakawa Market"; // Default for prototype
    $delivery = "Kampala Center"; // Default for prototype

    $sql = "INSERT INTO logistics (order_id, transporter_id, pickup_location, delivery_location, status) 
            VALUES ('$order_id', '$transporter_id', '$pickup', '$delivery', 'assigned')";
    
    if ($conn->query($sql) === TRUE) {
        // Update order status to accepted/in_transit
        $conn->query("UPDATE orders SET order_status = 'in_transit' WHERE id = '$order_id'");
        $message = "<div class='alert success'>Order #$order_id accepted! Please proceed to pickup.</div>";
    } else {
        $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
    }
}

// Handle Status Updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $logistics_id = mysqli_real_escape_string($conn, $_POST['logistics_id']);
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);

    // Update logistics table
    $conn->query("UPDATE logistics SET status = '$new_status' WHERE id = '$logistics_id'");
    
    // Sync order status if delivered
    if ($new_status == 'delivered') {
        $conn->query("UPDATE orders SET order_status = 'delivered' WHERE id = '$order_id'");
    }

    $message = "<div class='alert success'>Delivery status updated to " . ucfirst(str_replace('_', ' ', $new_status)) . "!</div>";
}

// Fetch all unassigned orders (Orders that don't have a logistics record yet)
$availableOrdersQuery = "SELECT orders.*, produce.product_name, users.full_name as vendor_name 
                         FROM orders 
                         JOIN produce ON orders.produce_id = produce.id 
                         JOIN users ON orders.vendor_id = users.id 
                         LEFT JOIN logistics ON orders.id = logistics.order_id 
                         WHERE logistics.id IS NULL AND orders.order_status != 'cancelled'
                         ORDER BY orders.created_at DESC";
$availableOrders = $conn->query($availableOrdersQuery);

// Fetch my active deliveries
$myDeliveriesQuery = "SELECT logistics.*, orders.total_price, produce.product_name, users.full_name as vendor_name 
                      FROM logistics 
                      JOIN orders ON logistics.order_id = orders.id 
                      JOIN produce ON orders.produce_id = produce.id 
                      JOIN users ON orders.vendor_id = users.id 
                      WHERE logistics.transporter_id = '$transporter_id' 
                      ORDER BY logistics.updated_at DESC";
$myDeliveries = $conn->query($myDeliveriesQuery);

// Stats
$activeDeliveries = 0;
$completedDeliveries = 0;
$deliveryList = [];
while($row = $myDeliveries->fetch_assoc()) {
    $deliveryList[] = $row;
    if ($row['status'] == 'delivered') {
        $completedDeliveries++;
    } else {
        $activeDeliveries++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transporter Dashboard | Nakawa ASCM</title>
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
            <li><a href="transporter_dashboard.php" class="active"><i class="fa-solid fa-truck-fast"></i> <span>Available Jobs</span></a></li>
            <li><a href="#my-deliveries"><i class="fa-solid fa-route"></i> <span>My Deliveries</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <h1>Transporter Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <div class="user-avatar"><i class="fa-solid fa-truck"></i></div>
            </div>
        </div>

        <?php echo $message; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fa-solid fa-truck-moving"></i>
                <div>
                    <h3>Active Deliveries</h3>
                    <p><?php echo $activeDeliveries; ?></p>
                </div>
            </div>
            <div class="stat-card" style="border-left-color: #2e7d32;">
                <i class="fa-solid fa-check-double" style="color: #2e7d32; background: #e8f5e9;"></i>
                <div>
                    <h3>Completed Deliveries</h3>
                    <p><?php echo $completedDeliveries; ?></p>
                </div>
            </div>
        </div>

        <!-- Available Jobs -->
        <div class="content-section">
            <h2 class="section-header">Available Delivery Jobs</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Vendor (Buyer)</th>
                            <th>Qty</th>
                            <th>Total Value (UGX)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($availableOrders->num_rows > 0): ?>
                            <?php while($order = $availableOrders->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($order['product_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($order['vendor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['quantity_ordered']); ?></td>
                                    <td><?php echo number_format($order['total_price']); ?></td>
                                    <td>
                                        <form action="transporter_dashboard.php" method="POST">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <button type="submit" name="accept_order" class="btn-action btn-assign">Accept Job</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #888;">No available jobs right now.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- My Deliveries -->
        <div class="content-section" id="my-deliveries">
            <h2 class="section-header">My Deliveries</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Route</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($deliveryList) > 0): ?>
                            <?php foreach ($deliveryList as $delivery): ?>
                                <tr>
                                    <td>#<?php echo $delivery['order_id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($delivery['product_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($delivery['pickup_location']); ?> <i class="fa-solid fa-arrow-right" style="font-size:0.8rem;"></i> <?php echo htmlspecialchars($delivery['delivery_location']); ?></td>
                                    <td>
                                        <span class="badge badge-available"><?php echo ucfirst(str_replace('_', ' ', $delivery['status'])); ?></span>
                                    </td>
                                    <td>
                                        <!-- Dynamic Action Buttons based on status -->
                                        <?php if ($delivery['status'] == 'assigned'): ?>
                                            <form action="transporter_dashboard.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="logistics_id" value="<?php echo $delivery['id']; ?>">
                                                <input type="hidden" name="order_id" value="<?php echo $delivery['order_id']; ?>">
                                                <input type="hidden" name="new_status" value="picked_up">
                                                <button type="submit" name="update_status" class="btn-action btn-pickup">Mark Picked Up</button>
                                            </form>
                                        <?php elseif ($delivery['status'] == 'picked_up'): ?>
                                            <form action="transporter_dashboard.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="logistics_id" value="<?php echo $delivery['id']; ?>">
                                                <input type="hidden" name="order_id" value="<?php echo $delivery['order_id']; ?>">
                                                <input type="hidden" name="new_status" value="in_transit">
                                                <button type="submit" name="update_status" class="btn-action btn-transit">Mark In Transit</button>
                                            </form>
                                        <?php elseif ($delivery['status'] == 'in_transit'): ?>
                                            <form action="transporter_dashboard.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="logistics_id" value="<?php echo $delivery['id']; ?>">
                                                <input type="hidden" name="order_id" value="<?php echo $delivery['order_id']; ?>">
                                                <input type="hidden" name="new_status" value="delivered">
                                                <button type="submit" name="update_status" class="btn-action btn-deliver">Mark Delivered</button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn-action btn-disabled" disabled>Completed</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #888;">You have no active deliveries. Accept a job above!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</body>
</html>