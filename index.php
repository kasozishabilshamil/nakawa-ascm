<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nakawa ASCM | Connecting the Market</title>
    <!-- Link to CSS -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Google Fonts: Poppins for a clean, modern look -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Extra inline styles specifically for the homepage market preview */
        .market-preview {
            padding: 80px 5%;
            background: #f8fdf9;
            text-align: center;
        }
        
        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            max-width: 1000px;
            margin: 40px auto;
        }

        .preview-card {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-top: 4px solid #2e7d32;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: 0.3s;
        }

        .preview-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .preview-card i {
            font-size: 2rem;
            color: #2e7d32;
            margin-bottom: 15px;
        }

        .preview-card h4 {
            color: #444;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .preview-card .price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #e67e22;
        }

        .preview-card .unit {
            font-size: 0.8rem;
            color: #888;
            margin-top: 5px;
        }
        
        .btn-view-all {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: transparent;
            color: #2e7d32;
            border: 2px solid #2e7d32;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-view-all:hover {
            background: #2e7d32;
            color: #fff;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">
            <i class="fa-solid fa-leaf"></i> Nakawa <span>ASCM</span>
        </div>
        <ul class="nav-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#features">Features</a></li>
            <li><a href="prices.php">Market Prices</a></li> <!-- Updated Link -->
            <li><a href="#about">About</a></li>
            <li><a href="login.php" class="btn-login">Login</a></li>
            <li><a href="register.php" class="btn-register">Register</a></li>
        </ul>
        <div class="hamburger">
            <i class="fa-solid fa-bars"></i>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <h1>Connecting <span class="text-green">Farmers</span>, <span class="text-orange">Vendors</span> & Transporters</h1>
            <p>The centralized digital supply chain management platform for Nakawa Market. Real-time pricing, logistics tracking, and seamless transactions.</p>
            <div class="hero-buttons">
                <a href="register.php" class="btn-primary">Join the Network</a>
                <a href="prices.php" class="btn-secondary">View Today's Prices</a>
            </div>
        </div>
        <div class="hero-image">
    <!-- Your local farm produce image -->
    <img src="photos/farm_produce.jpg.avif" alt="Fresh Farm Produce">
</div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <h2 class="section-title">Why Use <span class="text-green">Nakawa ASCM</span>?</h2>
        <p class="section-subtitle">Built to solve the real challenges of Nakawa Market stakeholders.</p>
        
        <div class="features-grid">
            <div class="feature-card">
                <i class="fa-solid fa-chart-line icon-green"></i>
                <h3>Real-Time Pricing</h3>
                <p>Eliminate information asymmetry. Farmers and vendors get live market prices to ensure fair trade.</p>
            </div>
            <div class="feature-card">
                <i class="fa-solid fa-truck-fast icon-orange"></i>
                <h3>Logistics Tracking</h3>
                <p>Track produce from farm to market. Coordinate with transporters to reduce post-harvest losses.</p>
            </div>
            <div class="feature-card">
                <i class="fa-solid fa-mobile-screen-button icon-green"></i>
                <h3>Digital Transactions</h3>
                <p>Secure, traceable mobile money and e-payment integration for all market transactions.</p>
            </div>
            <div class="feature-card">
                <i class="fa-solid fa-seedling icon-orange"></i>
                <h3>Market Analytics</h3>
                <p>Administrators get data-driven insights to forecast demand and manage the market efficiently.</p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="about-container">
            <div class="about-text">
                <h2 class="section-title">About <span class="text-green">The Project</span></h2>
                <p>Developed by students at Makerere University Business School, this platform aims to digitize the agricultural supply chain at Nakawa Market, reducing the 30-40% post-harvest losses currently experienced in Uganda.</p>
                <p>By connecting farmers directly with vendors and transporters, we are building a more transparent, efficient, and profitable market ecosystem.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-logo"><i class="fa-solid fa-leaf"></i> Nakawa ASCM</div>
            <p>&copy; 2026 Nakawa ASCM Project | Makerere University Business School</p>
        </div>
    </footer>

    <!-- Link to JavaScript -->
    <script src="js/script.js"></script>
</body>
</html>