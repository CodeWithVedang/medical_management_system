<?php
session_start();
header('Content-Type: application/json');

include 'config.php';

// Log the request for debugging
error_log("reset_all_data.php called with method: " . $_SERVER['REQUEST_METHOD']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'error' => 'Invalid request method']);
    exit;
}

try {
    // Truncate all relevant tables
    $conn->query("SET FOREIGN_KEY_CHECKS = 0"); // Disable foreign key checks to avoid constraint issues
    $conn->query("TRUNCATE TABLE sale_items");
    $conn->query("TRUNCATE TABLE sales");
    $conn->query("TRUNCATE TABLE medicines");
    $conn->query("SET FOREIGN_KEY_CHECKS = 1"); // Re-enable foreign key checks

    // Clear session data
    session_unset();
    session_destroy();

    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'error' => 'Failed to reset data: ' . $e->getMessage()]);
}

$conn->close();