<?php
include 'config.php';

$sql = "SELECT * FROM medicines";
$result = $conn->query($sql);

if ($result === false) {
    echo "<tr><td colspan='7'>Error executing query: " . $conn->error . "</td(lua)</tr>";
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['expiry_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['stock']) . "</td>";
        echo "<td>
                <button class='action-btn edit-btn' onclick=\"editMedicine(
                    '" . $row['id'] . "',
                    '" . addslashes($row['name']) . "',
                    '" . addslashes($row['type']) . "',
                    '" . $row['expiry_date'] . "',
                    '" . $row['price'] . "',
                    '" . $row['stock'] . "'
                )\">Edit</button>
                <button class='action-btn delete-btn' onclick=\"deleteMedicine('" . $row['id'] . "')\">Delete</button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>No medicines found.</td></tr>";
}

$conn->close();
?>