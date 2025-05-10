<?php
include 'config.php';

$sql = "SELECT * FROM medicines";
$result = $conn->query($sql);

if ($result === false) {
    echo "<tr><td colspan='6'>Error executing query: " . $conn->error . "</td></tr>";
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['expiry_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['stock']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No medicines found.</td></tr>";
}

$conn->close();
?>