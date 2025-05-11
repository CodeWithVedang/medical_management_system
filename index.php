<?php
session_start();
$status = isset($_GET['status']) ? $_GET['status'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Management System - Medicine Management</title>
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
                <li><a href="index.php" class="active">Medicine Management</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="customer_selling.php">Customer Selling</a></li>
                <li><a href="sales_history.php">Sales History</a></li>
                <li><a href="symptom_checker.php">Symptom Checker</a></li>

            </ul>
        </nav>

        <!-- Status Messages -->
        <?php
        if ($status == 'success') {
            echo '<p style="color: #28A745;">Medicine added/updated successfully!</p>';
        } elseif ($status == 'deleted') {
            echo '<p style="color: #28A745;">Medicine deleted successfully!</p>';
        } elseif ($status == 'error') {
            echo '<p style="color: #dc3545;">An error occurred. Please try again.</p>';
        }
        ?>

        <!-- Medicine Form -->
        <section class="form-section">
            <h2>Add/Edit Medicine</h2>
            <form id="medicineForm" action="php/add_medicine.php" method="POST">
                <input type="hidden" id="medicineId" name="id">
                <div class="form-group">
                    <label for="name">Medicine Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="type">Type:</label>
                    <select id="type" name="type" required>
                        <option value="Tablet">Tablet</option>
                        <option value="Syrup">Syrup</option>
                        <option value="Injection">Injection</option>
                        <option value="Capsule">Capsule</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="expiry_date">Expiry Date:</label>
                    <input type="date" id="expiry_date" name="expiry_date" required>
                </div>
                <div class="form-group">
                    <label for="price">Price (₹):</label>
                    <input type="number" id="price" name="price" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" min="0" required>
                </div>
                <button type="submit" id="submitBtn">Add Medicine</button>
                <button type="button" id="resetBtn" onclick="resetForm()">Reset</button>
            </form>
        </section>

        <!-- Medicine List -->
        <section class="table-section">
            <h2>Medicine List</h2>
            <table id="medicineTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Expiry Date</th>
                        <th>Price (₹)</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php include 'php/view_medicines.php'; ?>
                </tbody>
            </table>
        </section>
    </div>
    <script src="js/script.js"></script>
</body>
</html>