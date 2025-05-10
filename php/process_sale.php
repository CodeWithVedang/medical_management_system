<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_phone = $_POST['customer_phone'] ?? '';
    $doctor_name = $_POST['doctor_name'] ?? '';
    $bill_discount = floatval($_POST['bill_discount'] ?? 0);
    $total_price = floatval($_POST['total_price'] ?? 0);
    $medicines = $_POST['medicines'] ?? [];

    if (empty($customer_name) || empty($customer_phone) || empty($medicines)) {
        header('Location: ../customer_selling.php?status=error');
        exit;
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Check stock availability
        foreach ($medicines as $medicine) {
            $medicine_id = $medicine['medicine_id'];
            $quantity = intval($medicine['quantity']);

            $stmt = $conn->prepare("SELECT stock FROM medicines WHERE id = ? FOR UPDATE");
            $stmt->bind_param("i", $medicine_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $medicine_data = $result->fetch_assoc();

            if ($medicine_data['stock'] < $quantity) {
                throw new Exception("Insufficient stock for medicine ID: $medicine_id");
            }
        }

        // Insert sale
        $stmt = $conn->prepare("INSERT INTO sales (customer_name, customer_phone, doctor_name, discount, total_price, sale_date) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssdd", $customer_name, $customer_phone, $doctor_name, $bill_discount, $total_price);
        $stmt->execute();
        $sale_id = $conn->insert_id;

        // Update stock and insert sale items
        foreach ($medicines as $medicine) {
            $medicine_id = $medicine['medicine_id'];
            $quantity = intval($medicine['quantity']);
            $subtotal = floatval($medicine['subtotal']);

            // Insert sale item
            $stmt = $conn->prepare("INSERT INTO sale_items (sale_id, medicine_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $sale_id, $medicine_id, $quantity, $subtotal);
            $stmt->execute();

            // Update stock
            $stmt = $conn->prepare("UPDATE medicines SET stock = stock - ? WHERE id = ?");
            $stmt->bind_param("ii", $quantity, $medicine_id);
            $stmt->execute();
        }

        // Commit transaction
        $conn->commit();

        // Store sale_id in session instead of generating bill HTML
        $_SESSION['sale_id'] = $sale_id;
        unset($_SESSION['bill']); // Clear any old bill HTML

        header('Location: ../customer_selling.php?status=success');
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log($e->getMessage(), 3, '../debug.log');
        if (strpos($e->getMessage(), 'Insufficient stock') !== false) {
            header('Location: ../customer_selling.php?status=low_stock');
        } else {
            header('Location: ../customer_selling.php?status=error');
        }
    }

    $stmt->close();
    $conn->close();
} else {
    header('Location: ../customer_selling.php?status=error');
}