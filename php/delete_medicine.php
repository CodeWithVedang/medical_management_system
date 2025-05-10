<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM medicines WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../index.php?status=deleted"); // Keep redirect to index.php
    } else {
        header("Location: ../index.php?status=error");
    }

    $stmt->close();
}

$conn->close();
?>