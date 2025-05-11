<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Management System - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <!-- Navbar -->
        <nav class="navbar">
            <div class="logo">
                <h2>Medical Management System</h2>
            </div>
            <button class="menu-toggle">☰</button>
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="index.php">Medicine Management</a></li>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="customer_selling.php">Customer Selling</a></li>
                <li><a href="sales_history.php">Sales History</a></li>
                <li><a href="symptom_checker.php">Symptom Checker</a></li>

            </ul>
        </nav>

        <!-- Dashboard Stats -->
        <section class="stats-section">
            <h2>Dashboard Overview</h2>
            <div class="stats-cards">
                <?php
                include 'php/config.php';

                // Total Medicines
                $sql = "SELECT COUNT(*) as total FROM medicines";
                $result = $conn->query($sql);
                $total_medicines = $result->fetch_assoc()['total'];

                // Low Stock Medicines
                $sql = "SELECT COUNT(*) as low_stock FROM medicines WHERE stock < 10";
                $result = $conn->query($sql);
                $low_stock = $result->fetch_assoc()['low_stock'];

                // Total Sales
                $sql = "SELECT SUM(total_price) as total_sales FROM sales";
                $result = $conn->query($sql);
                $total_sales = $result->fetch_assoc()['total_sales'] ?: 0;

                $conn->close();
                ?>
                <div class="card">
                    <h3>Total Medicines</h3>
                    <p><?php echo $total_medicines; ?></p>
                </div>
                <div class="card">
                    <h3>Low Stock</h3>
                    <p><?php echo $low_stock; ?></p>
                </div>
                <div class="card">
                    <h3>Total Sales (₹)</h3>
                    <p><?php echo number_format($total_sales, 2); ?></p>
                </div>
            </div>
        </section>

        <!-- Charts -->
        <section class="charts-section">
            <h2>Analytics</h2>
            <div class="chart-container">
                <div class="chart">
                    <h3>Stock Levels</h3>
                    <canvas id="stockChart"></canvas>
                </div>
                <div class="chart">
                    <h3>Sales by Medicine Type</h3>
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </section>

        <!-- Available Medicines -->
        <section class="table-section">
            <h2>Available Medicines</h2>
            <table id="medicineTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Expiry Date</th>
                        <th>Price (₹)</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php include 'php/view_medicines_dashboard.php'; ?>
                </tbody>
            </table>
        </section>
    </div>

    <script>
        // Stock Levels Chart
        <?php
        include 'php/config.php';
        $sql = "SELECT name, stock FROM medicines";
        $result = $conn->query($sql);
        $medicine_names = [];
        $stock_levels = [];
        while ($row = $result->fetch_assoc()) {
            $medicine_names[] = $row['name'];
            $stock_levels[] = $row['stock'];
        }

        // Sales by Medicine Type
        $sql = "SELECT m.type, SUM(si.subtotal) as total FROM sale_items si JOIN medicines m ON si.medicine_id = m.id GROUP BY m.type";
        $result = $conn->query($sql);
        $types = [];
        $sales = [];
        while ($row = $result->fetch_assoc()) {
            $types[] = $row['type'];
            $sales[] = $row['total'];
        }
        $conn->close();
        ?>
        const stockChart = new Chart(document.getElementById('stockChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($medicine_names); ?>,
                datasets: [{
                    label: 'Stock Levels',
                    data: <?php echo json_encode($stock_levels); ?>,
                    backgroundColor: '#28A745',
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        const salesChart = new Chart(document.getElementById('salesChart'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($types); ?>,
                datasets: [{
                    label: 'Sales by Type',
                    data: <?php echo json_encode($sales); ?>,
                    backgroundColor: ['#007BFF', '#28A745', '#FFC107', '#dc3545'],
                }]
            }
        });
    </script>
    <script src="js/script.js"></script>
</body>
</html>