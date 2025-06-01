<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to find the user
    $query = "SELECT * FROM MedicalUser WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // User found, set session variables
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: ../dashboard.php");
        exit;
    } else {
        // Invalid credentials, redirect back to login with error
        header("Location: ../login.php?error=invalid");
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../login.php");
    exit;
}
?>