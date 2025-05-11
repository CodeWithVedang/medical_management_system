<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Management System - Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <!-- Navbar -->
        <nav class="navbar">
            <div class="logo">
                <h2>Medical Management System</h2>
            </div>
            <ul class="nav-links">
                <li><a href="home.php" class="active">Home</a></li>
                <li><a href="index.php">Medicine Management</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="customer_selling.php">Customer Selling</a></li>
                <li><a href="sales_history.php">Sales History</a></li>
                <li><a href="symptom_checker.php">Symptom Checker</a></li>

            </ul>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section">
            <h1>Welcome to the Medical Management System</h1>
            <p>Efficiently manage your medical inventory, track sales, and gain insights with our comprehensive system.</p>
            <div class="module-buttons">
                <a href="index.php" class="btn">Manage Medicines</a>
                <a href="dashboard.php" class="btn">View Dashboard</a>
                <a href="customer_selling.php" class="btn">Customer Selling</a>
                <a href="sales_history.php" class="btn">Sales History</a>
                <a href="symptom_checker.php" class="btn">Symptom Checker</a>

            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <h2>Key Features</h2>
            <div class="feature-cards">
                <div class="card">
                    <h3>Medicine Management</h3>
                    <p>Add, update, delete, and view medicines with ease, including details like type, expiry date, and stock.</p>
                </div>
                <div class="card">
                    <h3>Dashboard Insights</h3>
                    <p>Visualize total medicines, low stock alerts, and sales data with interactive charts.</p>
                </div>
                <div class="card">
                    <h3>Customer Selling</h3>
                    <p>Process sales, generate bills, and manage customer transactions efficiently.</p>
                </div>
                <div class="card">
                    <h3>Sales History</h3>
                    <p>View detailed sales history sorted by medicine type, doctor, or customer, with bill printing.</p>
                </div>
            </div>
        </section>
    </div>
    <script src="js/script.js"></script>
</body>
</html>