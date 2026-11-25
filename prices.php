<?php
session_start();
include 'db_connect.php';

// Fetch all current market prices
$pricesQuery = "SELECT * FROM market_prices ORDER BY product_name ASC";
$pricesResult = $conn->query($pricesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Market Prices | Nakawa ASCM</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .prices-hero {
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
            color: #fff;
            padding: 120px 5% 60px 5%;
            text-align: center;
        }
        .prices-hero h1 { font-size: 2.5rem; margin-bottom: 10px; }
        .prices-hero p { font-size: 1.1rem; opacity: 0.9; }
        
        .prices-container {
            max-width: 900px;
            margin: -30px auto 50px auto;
            padding: 0 20px;
        }
        
        .price-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .price-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            padding: 20px;
            background: #f8fdf9;
            border-bottom: 2px solid #2e7d32;
            font-weight: 700;
            color: #1b5e20;
        }
        
        .price-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            align-items: center;
            transition: 0.2s;
        }
        
        .price-row:hover {
            background: #f8fdf9;
        }
        
        .price-row .product-name {
            font-weight: 600;
            color: #333;
        }
        
        .price-row .price-value {
            font-weight: 700;
            color: #e67e22;
            font-size: 1.1rem;
        }
        
        .price-row .last-updated {
            font-size: 0.85rem;
            color: #888;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar (Reused from index.php) -->
    <nav class="navbar">
        <div class="logo">
            <i class="fa-solid fa-leaf"></i> Nakawa <span>ASCM</span>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="prices.php" style="color: #2e7d32; font-weight: bold;">Market Prices</a></li>
            <li><a href="login.php" class="btn-login">Login</a></li>
            <li><a href="register.php" class="btn-register">Register</a></li>
        </ul>
    </nav>

    <!-- Hero Section -->
    <section class="prices-hero">
        <h1>Today's Market Prices</h1>
        <p>Live rates updated daily by the Nakawa Market Administration to ensure fair trade.</p>
    </section>

    <!-- Prices Container -->
    <div class="prices-container">
        <div class="price-card">
            <div class="price-header">
                <div>Product Name</div>
                <div>Price (UGX)</div>
                <div>Last Updated</div>
            </div>
            
            <?php if ($pricesResult->num_rows > 0): ?>
                <?php while($price = $pricesResult->fetch_assoc()): ?>
                    <div class="price-row">
                        <div class="product-name"><i class="fa-solid fa-seedling" style="color: #2e7d32; margin-right: 10px;"></i><?php echo htmlspecialchars($price['product_name']); ?></div>
                        <div class="price-value"><?php echo number_format($price['current_price']); ?> / Unit</div>
                        <div class="last-updated"><?php echo date('M d, H:i', strtotime($price['date_updated'])); ?></div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="padding: 40px; text-align: center; color: #888;">
                    <i class="fa-solid fa-info-circle" style="font-size: 2rem; margin-bottom: 10px; color: #ccc;"></i>
                    <p>No market prices have been published yet. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
        <p style="text-align: center; margin-top: 20px; color: #666; font-size: 0.9rem;">
            <i class="fa-solid fa-circle-info"></i> Prices are indicative and may vary based on quality and quantity.
        </p>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-logo"><i class="fa-solid fa-leaf"></i> Nakawa ASCM</div>
            <p>&copy; 2026 Nakawa ASCM Project | Makerere University Business School</p>
        </div>
    </footer>
</body>
</html>