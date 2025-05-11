<?php
session_start();
include 'php/config.php';

// Initialize filter variables
$customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
$doctor_name = isset($_POST['doctor_name']) ? trim($_POST['doctor_name']) : '';
$medicine_type = isset($_POST['medicine_type']) ? trim($_POST['medicine_type']) : '';
$search_performed = isset($_POST['search_submitted']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Management System - Sales History</title>
    <link rel="stylesheet" href="css/style.css">
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
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="customer_selling.php">Customer Selling</a></li>
                <li><a href="sales_history.php" class="active">Sales History</a></li>
                <li><a href="symptom_checker.php">Symptom Checker</a></li>

            </ul>
        </nav>

        <!-- Sales History Section -->
        <section class="sales-history-section">
            <h2>Sales History</h2>

            <!-- Filter Bar -->
            <div class="filter-bar">
                <h3>Filter Sales</h3>
                <form id="filterForm" action="sales_history.php" method="POST">
                    <div class="form-group">
                        <label for="customer_name">Customer Name:</label>
                        <input type="text" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($customer_name); ?>" placeholder="Enter customer name">
                    </div>
                    <div class="form-group">
                        <label for="doctor_name">Doctor Name:</label>
                        <input type="text" id="doctor_name" name="doctor_name" value="<?php echo htmlspecialchars($doctor_name); ?>" placeholder="Enter doctor name">
                    </div>
                    <div class="form-group">
                        <label for="medicine_type">Medicine Type:</label>
                        <select id="medicine_type" name="medicine_type">
                            <option value="" <?php echo $medicine_type == '' ? 'selected' : ''; ?>>All</option>
                            <option value="Tablet" <?php echo $medicine_type == 'Tablet' ? 'selected' : ''; ?>>Tablet</option>
                            <option value="Syrup" <?php echo $medicine_type == 'Syrup' ? 'selected' : ''; ?>>Syrup</option>
                            <option value="Injection" <?php echo $medicine_type == 'Injection' ? 'selected' : ''; ?>>Injection</option>
                            <option value="Capsule" <?php echo $medicine_type == 'Capsule' ? 'selected' : ''; ?>>Capsule</option>
                        </select>
                    </div>
                    <button type="submit" name="search_submitted">Search</button>
                </form>
            </div>

            <!-- Search Results -->
            <div class="table-section">
                <?php
                if ($search_performed) {
                    if (empty($customer_name) && empty($doctor_name) && empty($medicine_type)) {
                        echo '<p style="color: #dc3545;">Please apply at least one filter to search.</p>';
                    } else {
                        // Build dynamic SQL query
                        $sql = "SELECT s.id, s.customer_name, s.customer_phone, s.doctor_name, s.discount, s.total_price, s.sale_date, 
                                       GROUP_CONCAT(CONCAT(m.name, ' (Qty: ', si.quantity, ', Subtotal: ₹', si.subtotal, ')') SEPARATOR '<br>') as medicines
                                FROM sales s
                                JOIN sale_items si ON s.id = si.sale_id
                                JOIN medicines m ON si.medicine_id = m.id";
                        $conditions = [];
                        $params = [];
                        $param_types = '';

                        if (!empty($customer_name)) {
                            $conditions[] = "s.customer_name LIKE ?";
                            $params[] = "%" . $customer_name . "%";
                            $param_types .= 's';
                        }
                        if (!empty($doctor_name)) {
                            $conditions[] = "s.doctor_name LIKE ?";
                            $params[] = "%" . $doctor_name . "%";
                            $param_types .= 's';
                        }
                        if (!empty($medicine_type)) {
                            $conditions[] = "m.type = ?";
                            $params[] = $medicine_type;
                            $param_types .= 's';
                        }

                        if (!empty($conditions)) {
                            $sql .= " WHERE " . implode(" AND ", $conditions);
                        }

                        $sql .= " GROUP BY s.id ORDER BY s.sale_date DESC";

                        $stmt = $conn->prepare($sql);
                        if (!empty($params)) {
                            $stmt->bind_param($param_types, ...$params);
                        }
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            echo "<h4>Filtered Sales Results</h4>";
                            echo '<p>Filters Applied: ';
                            $filters = [];
                            if (!empty($customer_name)) $filters[] = "Customer: " . htmlspecialchars($customer_name);
                            if (!empty($doctor_name)) $filters[] = "Doctor: " . htmlspecialchars($doctor_name);
                            if (!empty($medicine_type)) $filters[] = "Medicine Type: " . htmlspecialchars($medicine_type);
                            echo implode(", ", $filters) . '</p>';
                            echo '<table>
                                    <thead>
                                        <tr>
                                            <th>Sale ID</th>
                                            <th>Customer</th>
                                            <th>Phone</th>
                                            <th>Doctor</th>
                                            <th>Medicines</th>
                                            <th>Discount (%)</th>
                                            <th>Total Price (₹)</th>
                                            <th>Sale Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . $row['id'] . "</td>
                                        <td>" . htmlspecialchars($row['customer_name']) . "</td>
                                        <td>" . htmlspecialchars($row['customer_phone']) . "</td>
                                        <td>" . ($row['doctor_name'] ? htmlspecialchars($row['doctor_name']) : '-') . "</td>
                                        <td>" . $row['medicines'] . "</td>
                                        <td>" . number_format($row['discount'], 2) . "</td>
                                        <td>" . number_format($row['total_price'], 2) . "</td>
                                        <td>" . $row['sale_date'] . "</td>
                                        <td><button class='action-btn print' onclick='printBill(" . $row['id'] . ")'>Print Bill</button></td>
                                      </tr>";
                            }
                            echo '</tbody></table>';
                        } else {
                            echo '<p style="color: #dc3545;">No sales records found for the applied filters: ';
                            $filters = [];
                            if (!empty($customer_name)) $filters[] = "Customer: " . htmlspecialchars($customer_name);
                            if (!empty($doctor_name)) $filters[] = "Doctor: " . htmlspecialchars($doctor_name);
                            if (!empty($medicine_type)) $filters[] = "Medicine Type: " . htmlspecialchars($medicine_type);
                            echo implode(", ", $filters) . '</p>';
                        }
                        $stmt->close();
                    }
                } else {
                    echo '<p>Apply filters above to view sales history.</p>';
                }
                $conn->close();
                ?>
            </div>
        </section>
    </div>

    <script>
        function printBill(saleId) {
            fetch(`php/get_bill.php?sale_id=${saleId}`)
                .then(response => response.text())
                .then(billContent => {
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                        <html>
                        <head>
                            <title>Bill</title>
                            <link rel="stylesheet" href="css/style.css">
                        </head>
                        <body>
                            ${billContent}
                        </body>
                        </html>
                    `);
                    printWindow.document.close();
                    setTimeout(() => {
                        printWindow.print();
                    }, 500); // Delay to ensure styles load
                })
                .catch(error => {
                    console.error('Error fetching bill:', error);
                    alert('Failed to load bill. Please try again.');
                });
        }
    </script>
    <script src="js/script.js"></script>
</body>
</html>