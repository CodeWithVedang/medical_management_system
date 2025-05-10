<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $expiry_date = $_POST['expiry_date'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    if (empty($id)) {
        // Add new medicine
        $sql = "INSERT INTO medicines (name, type, expiry_date, price, stock) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdi", $name, $type, $expiry_date, $price, $stock);
    } else {
        // Update existing medicine
        $sql = "UPDATE medicines SET name=?, type=?, expiry_date=?, price=?, stock=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdii", $name, $type, $expiry_date, $price, $stock, $id);
    }

    if ($stmt->execute()) {
        header("Location: ../index.php?status=success"); // Keep redirect to index.php for Medicine Management
    } else {
        header("Location: ../index.php?status=error");
    }

    $stmt->close();
}

$conn->close();
?>