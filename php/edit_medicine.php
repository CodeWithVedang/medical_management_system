<?php
include 'config.php';

if (!isset($_GET['id'])) {
    header("Location: ../index.php?status=error");
    exit();
}

$id = $_GET['id'];

// Fetch medicine details
$sql = "SELECT * FROM medicines WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: ../index.php?status=notfound");
    exit();
}

$medicine = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Medicine - Medical Management System</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="logo">
                <h2>Medical Management System</h2>
            </div>
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="index.php" class="active">Medicine Management</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="customer_selling.php">Customer Selling</a></li>
            </ul>
        </nav>
        <section class="form-section">
            <h2>Edit Medicine</h2>
            <form id="medicineForm" action="add_medicine.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $medicine['id']; ?>">
                <div class="form-group">
                    <label for="name">Medicine Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo $medicine['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="type">Type:</label>
                    <select id="type" name="type" required>
                        <option value="Tablet" <?php if ($medicine['type'] == 'Tablet') echo 'selected'; ?>>Tablet</option>
                        <option value="Syrup" <?php if ($medicine['type'] == 'Syrup') echo 'selected'; ?>>Syrup</option>
                        <option value="Injection" <?php if ($medicine['type'] == 'Injection') echo 'selected'; ?>>Injection</option>
                        <option value="Capsule" <?php if ($medicine['type'] == 'Capsule') echo 'selected'; ?>>Capsule</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="expiry_date">Expiry Date:</label>
                    <input type="date" id="expiry_date" name="expiry_date" value="<?php echo $medicine['expiry_date']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="price">Price (₹):</label>
                    <input type="number" step="0.01" id="price" name="price" value="<?php echo $medicine['price']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" value="<?php echo $medicine['stock']; ?>" required>
                </div>
                <button type="submit" id="submitBtn">Update Medicine</button>
                <a href="../index.php" class="action-btn" style="background-color: #FFC107; padding: 10px 20px; text-decoration: none; color: #333;">Cancel</a>
            </form>
        </section>
    </div>
    <script src="js/script.js"></script>
</body>
</html>