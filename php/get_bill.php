<?php
session_start();
include 'config.php';

if (isset($_GET['sale_id'])) {
    $sale_id = $_GET['sale_id'];

    // Fetch sale details
    $sql = "SELECT s.id, s.customer_name, s.customer_phone, s.doctor_name, s.discount, s.total_price, s.sale_date,
                   GROUP_CONCAT(CONCAT(m.name, ' (Qty: ', si.quantity, ', Subtotal: ₹', si.subtotal, ')') SEPARATOR '<br>') as medicines
            FROM sales s
            JOIN sale_items si ON s.id = si.sale_id
            JOIN medicines m ON si.medicine_id = m.id
            WHERE s.id = ?
            GROUP BY s.id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sale_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sale = $result->fetch_assoc();

        // Fetch individual items for the table
        $items_sql = "SELECT m.name, si.quantity, m.price, si.subtotal
                      FROM sale_items si
                      JOIN medicines m ON si.medicine_id = m.id
                      WHERE si.sale_id = ?";
        $items_stmt = $conn->prepare($items_sql);
        $items_stmt->bind_param("i", $sale_id);
        $items_stmt->execute();
        $items_result = $items_stmt->get_result();

        // Calculate subtotal (sum of subtotals)
        $subtotal = 0;
        $items = [];
        while ($item = $items_result->fetch_assoc()) {
            $items[] = $item;
            $subtotal += $item['subtotal'];
        }

        // Calculate discount amount
        $discount_amount = ($subtotal * $sale['discount']) / 100;
        $total_amount = $subtotal - $discount_amount;

        // Generate professional bill HTML
        $bill_date = date('d-M-Y'); // Current date for the bill
        $sale_date = date('d-M-Y', strtotime($sale['sale_date']));
        $html = '
        <div class="bill-container">
            <!-- Header -->
            <div class="bill-header">
                <h1>Medical Management System Pharmacy</h1>
                <p>1013,MIDC,Finolex College Road,Ratnagiri</p>
                <p>Phone: +91 7843013109 | Email: pharmacy@medicalsystem.com</p>
                <p>Date: ' . $bill_date . '</p>
            </div>

            <!-- Title -->
            <h2 class="bill-title">Tax Invoice</h2>

            <!-- Bill and Customer Details -->
            <div class="bill-details">
                <div class="bill-details-left">
                    <p><strong>Bill No.:</strong> ' . $sale['id'] . '</p>
                    <p><strong>Date of Sale:</strong> ' . $sale_date . '</p>
                </div>
                <div class="bill-details-right">
                    <p><strong>Customer Name:</strong> ' . htmlspecialchars($sale['customer_name']) . '</p>
                    <p><strong>Phone Number:</strong> ' . htmlspecialchars($sale['customer_phone']) . '</p>
                    ' . ($sale['doctor_name'] ? '<p><strong>Doctor Name:</strong> ' . htmlspecialchars($sale['doctor_name']) . '</p>' : '') . '
                </div>
            </div>

            <!-- Items Table -->
            <table class="bill-items-table">
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Quantity</th>
                        <th>Price (₹)</th>
                        <th>Subtotal (₹)</th>
                    </tr>
                </thead>
                <tbody>';
        foreach ($items as $item) {
            $html .= '
                    <tr>
                        <td>' . htmlspecialchars($item['name']) . '</td>
                        <td>' . $item['quantity'] . '</td>
                        <td>' . number_format($item['price'], 2) . '</td>
                        <td>' . number_format($item['subtotal'], 2) . '</td>
                    </tr>';
        }
        $html .= '
                </tbody>
            </table>

            <!-- Summary -->
            <div class="bill-summary">
                <p><strong>Subtotal:</strong> ₹' . number_format($subtotal, 2) . '</p>
                <p><strong>Discount (' . number_format($sale['discount'], 2) . '%):</strong> ₹' . number_format($discount_amount, 2) . '</p>
                <p><strong>Total Amount:</strong> ₹' . number_format($total_amount, 2) . '</p>
            </div>

            <!-- Footer -->
            <div class="bill-footer">
                <p>Thank you for your purchase!</p>
                <p>All sales are final. No refunds after 7 days.</p>
            </div>
        </div>';

        echo $html;
    } else {
        echo '<p>No bill found for this sale.</p>';
    }

    $stmt->close();
    $items_stmt->close();
    $conn->close();
} else {
    echo '<p>Invalid sale ID.</p>';
}