<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farmer') {
    header("Location: login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];
$message = "";

// Handle form submission to add new produce
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_produce'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $unit = mysqli_real_escape_string($conn, $_POST['unit']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);

    $sql = "INSERT INTO produce (farmer_id, product_name, category, quantity, unit, price_per_unit) 
            VALUES ('$farmer_id', '$product_name', '$category', '$quantity', '$unit', '$price')";
    
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert success'>Produce added successfully!</div>";
    } else {
        $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
    }
}

// Fetch all produce for this farmer
$produceQuery = "SELECT * FROM produce WHERE farmer_id = '$farmer_id' ORDER BY created_at DESC";
$produceResult = $conn->query($produceQuery);

// Fetch stats
$totalItems = $produceResult->num_rows;
$availableItems = 0;
$totalValue = 0;

// Store results in an array so we can loop twice (once for stats, once for table)
$produceList = [];
while($row = $produceResult->fetch_assoc()) {
    $produceList[] = $row;
    if ($row['status'] == 'available') {
        $availableItems++;
    }
    $totalValue += ($row['quantity'] * $row['price_per_unit']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard | Nakawa ASCM</title>
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
            <li><a href="farmer_dashboard.php" class="active"><i class="fa-solid fa-gauge"></i> <span>Dashboard</span></a></li>
            <li><a href="#my-produce"><i class="fa-solid fa-seedling"></i> <span>My Produce</span></a></li>
            <li><a href="#orders"><i class="fa-solid fa-box"></i> <span>Orders</span></a></li>
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
            <h1>Farmer Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <div class="user-avatar"><i class="fa-solid fa-user"></i></div>
            </div>
        </div>

        <?php echo $message; ?>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fa-solid fa-layer-group"></i>
                <div>
                    <h3>Total Listings</h3>
                    <p><?php echo $totalItems; ?></p>
                </div>
            </div>
            <div class="stat-card" style="border-left-color: #e67e22;">
                <i class="fa-solid fa-check-circle" style="color: #e67e22; background: #fff3e0;"></i>
                <div>
                    <h3>Available Items</h3>
                    <p><?php echo $availableItems; ?></p>
                </div>
            </div>
            <div class="stat-card" style="border-left-color: #1976d2;">
                <i class="fa-solid fa-money-bill-wave" style="color: #1976d2; background: #e3f2fd;"></i>
                <div>
                    <h3>Est. Total Value (UGX)</h3>
                    <p><?php echo number_format($totalValue); ?></p>
                </div>
            </div>
        </div>

        <!-- Add Produce Form -->
        <div class="content-section">
            <h2 class="section-header">Add New Produce</h2>
            <form action="farmer_dashboard.php" method="POST" class="produce-form">
                <div class="input-group">
                    <label>Product Name</label>
                    <input type="text" name="product_name" required placeholder="e.g., Tomatoes">
                </div>
                <div class="input-group">
                    <label>Category</label>
                    <select name="category" required>
                        <option value="" disabled selected>Select</option>
                        <option value="Vegetables">Vegetables</option>
                        <option value="Fruits">Fruits</option>
                        <option value="Grains">Grains</option>
                        <option value="Tubers">Tubers</option>
                        <option value="Legumes">Legumes</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Quantity</label>
                    <input type="number" step="0.01" name="quantity" required placeholder="e.g., 100">
                </div>
                <div class="input-group">
                    <label>Unit</label>
                    <select name="unit" required>
                        <option value="Kg">Kg</option>
                        <option value="Bunch">Bunch</option>
                        <option value="Sack">Sack</option>
                        <option value="Piece">Piece</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Price per Unit (UGX)</label>
                    <input type="number" step="0.01" name="price" required placeholder="e.g., 3000">
                </div>
                <div class="input-group">
                    <button type="submit" name="add_produce" class="btn-green">Add Produce</button>
                </div>
            </form>
        </div>

        <!-- My Produce Table -->
        <div class="content-section" id="my-produce">
            <h2 class="section-header">My Produce Listings</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Price/Unit (UGX)</th>
                            <th>Status</th>
                            <th>Date Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($produceList) > 0): ?>
                            <?php foreach ($produceList as $item): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($item['category']); ?></td>
                                    <td><?php echo htmlspecialchars($item['quantity']) . " " . htmlspecialchars($item['unit']); ?></td>
                                    <td><?php echo number_format($item['price_per_unit']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $item['status'] == 'available' ? 'badge-available' : 'badge-sold'; ?>">
                                            <?php echo ucfirst($item['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($item['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #888;">No produce listed yet. Add your first item above!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</body>
</html>