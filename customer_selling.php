<?php
session_start();
$status = isset($_GET['status']) ? $_GET['status'] : '';
$sale_id = isset($_SESSION['sale_id']) ? $_SESSION['sale_id'] : null;
$bill = '';

if ($sale_id) {
    // Fetch bill content dynamically from get_bill.php
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/medical_management_system/php/get_bill.php?sale_id=$sale_id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $bill = curl_exec($ch);
    if (curl_errno($ch)) {
        $bill = '<p>Error loading bill. Please try again.</p>';
    }
    curl_close($ch);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Management System - Customer Selling</title>
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
                <li><a href="index.php">Medicine Management</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="customer_selling.php" class="active">Customer Selling</a></li>
                <li><a href="sales_history.php">Sales History</a></li>
            </ul>
        </nav>

        <!-- Status Messages -->
        <?php
        if ($status == 'success') {
            echo '<p style="color: #28A745;">Sale processed successfully! Bill generated.</p>';
        } elseif ($status == 'error') {
            echo '<p style="color: #dc3545;">An error occurred. Please try again.</p>';
        } elseif ($status == 'low_stock') {
            echo '<p style="color: #dc3545;">Insufficient stock for one or more medicines.</p>';
        }
        ?>

        <!-- Selling Form -->
        <section class="form-section">
            <h2>Add Medicine to Sale</h2>
            <div id="sellingForm">
                <div class="form-group">
                    <label for="medicine_id">Select Medicine:</label>
                    <select id="medicine_id" required>
                        <option value="">Select a medicine</option>
                        <?php
                        include 'php/config.php';
                        $sql = "SELECT id, name, price, stock FROM medicines WHERE stock > 0";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}' data-price='{$row['price']}' data-stock='{$row['stock']}' data-name='{$row['name']}'>{$row['name']} (Stock: {$row['stock']})</option>";
                        }
                        $conn->close();
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" min="1" required>
                </div>
                <div class="form-group">
                    <label for="subtotal">Subtotal (₹):</label>
                    <input type="text" id="subtotal" readonly>
                </div>
                <button type="button" onclick="addToCart()">Add</button>
            </div>
        </section>

        <!-- Cart Table -->
        <section class="table-section">
            <h2>Selected Medicines</h2>
            <form id="cartForm" action="php/process_sale.php" method="POST">
                <table id="cartTable">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Price (₹)</th>
                            <th>Quantity</th>
                            <th>Subtotal (₹)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cartTableBody">
                        <!-- Dynamically populated by JavaScript -->
                    </tbody>
                </table>
                <div class="form-group">
                    <label for="bill_discount">Bill Discount (%):</label>
                    <input type="number" id="bill_discount" name="bill_discount" min="0" max="100" value="0" step="0.01" oninput="calculateTotal()">
                </div>
                <div class="form-group">
                    <label for="total_price">Total Price (₹):</label>
                    <input type="text" id="total_price" name="total_price" readonly>
                </div>
                <button type="button" onclick="showPersonalDetailsModal()">Generate Bill</button>
                <button type="button" onclick="resetCart()">Reset</button>
            </form>
        </section>

        <!-- Personal Details Modal -->
        <div id="personalDetailsModal" class="modal">
            <div class="modal-content">
                <h2>Enter Customer Details</h2>
                <form id="personalDetailsForm">
                    <div class="form-group">
                        <label for="customer_name">Customer Name:</label>
                        <input type="text" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_phone">Customer Phone Number:</label>
                        <input type="tel" id="customer_phone" name="customer_phone" required>
                    </div>
                    <div class="form-group">
                        <label for="doctor_name">Doctor Name (Optional):</label>
                        <input type="text" id="doctor_name" name="doctor_name">
                    </div>
                    <button type="button" onclick="submitSale()">Confirm</button>
                    <button type="button" onclick="closePersonalDetailsModal()">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Bill Preview -->
        <section class="bill-section" id="billPreview" <?php echo $bill ? '' : 'style="display: none;"'; ?>>
            <h2>Bill Preview</h2>
            <div id="billContent"><?php echo $bill; ?></div>
            <button onclick="printBill(<?php echo json_encode($sale_id); ?>)">Print Bill</button>
        </section>
    </div>

    <script>
        let cart = [];
        let selectedPrice = 0;
        let selectedStock = 0;
        let selectedName = '';

        // Update subtotal when medicine or quantity changes
        function updateSubtotal() {
            const select = document.getElementById('medicine_id');
            const selectedOption = select.options[select.selectedIndex];
            selectedPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            selectedStock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
            selectedName = selectedOption.getAttribute('data-name') || '';
            const quantity = parseInt(document.getElementById('quantity').value) || 0;

            if (quantity > selectedStock) {
                alert('Quantity exceeds available stock!');
                document.getElementById('quantity').value = selectedStock;
                return;
            }

            const subtotal = selectedPrice * quantity;
            document.getElementById('subtotal').value = subtotal.toFixed(2);
        }

        // Add medicine to cart table
        function addToCart() {
            const medicineId = document.getElementById('medicine_id').value;
            const quantity = parseInt(document.getElementById('quantity').value) || 0;
            const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;

            if (!medicineId || quantity <= 0) {
                alert('Please select a medicine and enter a valid quantity.');
                return;
            }

            // Check if medicine already in cart
            const existingItem = cart.find(item => item.medicineId === medicineId);
            if (existingItem) {
                alert('This medicine is already in the cart. Update the quantity in the table.');
                return;
            }

            cart.push({
                medicineId,
                name: selectedName,
                price: selectedPrice,
                quantity,
                subtotal,
                stock: selectedStock
            });

            updateCartTable();
            document.getElementById('sellingForm').reset();
            document.getElementById('subtotal').value = '';
        }

        // Update cart table
        function updateCartTable() {
            const tbody = document.getElementById('cartTableBody');
            tbody.innerHTML = '';

            cart.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.name}</td>
                    <td>${item.price.toFixed(2)}</td>
                    <td><input type="number" name="medicines[${index}][quantity]" value="${item.quantity}" min="1" onchange="updateCartItem(${index}, this.value)" required></td>
                    <td>${item.subtotal.toFixed(2)}</td>
                    <td>
                        <button type="button" class="remove-btn" onclick="removeCartItem(${index})">Remove</button>
                        <input type="hidden" name="medicines[${index}][medicine_id]" value="${item.medicineId}">
                        <input type="hidden" name="medicines[${index}][subtotal]" value="${item.subtotal}">
                    </td>
                `;
                tbody.appendChild(row);
            });

            calculateTotal();
        }

        // Update quantity in cart
        function updateCartItem(index, quantity) {
            const item = cart[index];
            const newQuantity = parseInt(quantity);

            if (newQuantity > item.stock) {
                alert('Quantity exceeds available stock!');
                cart[index].quantity = item.stock;
                updateCartTable();
                return;
            }

            cart[index].quantity = newQuantity;
            cart[index].subtotal = item.price * newQuantity;

            updateCartTable();
        }

        // Remove item from cart
        function removeCartItem(index) {
            cart.splice(index, 1);
            updateCartTable();
        }

        // Calculate total price with bill discount
        function calculateTotal() {
            const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
            const discount = parseFloat(document.getElementById('bill_discount').value) || 0;
            const discountAmount = subtotal * (discount / 100);
            const total = subtotal - discountAmount;
            document.getElementById('total_price').value = total.toFixed(2);
        }

        // Show personal details modal
        function showPersonalDetailsModal() {
            if (cart.length === 0) {
                alert('Please add at least one medicine to generate a bill.');
                return;
            }
            document.getElementById('personalDetailsModal').style.display = 'flex';
        }

        // Close personal details modal
        function closePersonalDetailsModal() {
            document.getElementById('personalDetailsModal').style.display = 'none';
            document.getElementById('personalDetailsForm').reset();
        }

        // Submit sale with personal details
        function submitSale() {
            const customerName = document.getElementById('customer_name').value.trim();
            const customerPhone = document.getElementById('customer_phone').value.trim();
            const doctorName = document.getElementById('doctor_name').value.trim();

            if (!customerName || !customerPhone) {
                alert('Customer Name and Phone Number are required.');
                return;
            }

            // Add personal details to cart form
            const form = document.getElementById('cartForm');
            const inputs = `
                <input type="hidden" name="customer_name" value="${customerName}">
                <input type="hidden" name="customer_phone" value="${customerPhone}">
                <input type="hidden" name="doctor_name" value="${doctorName}">
            `;
            // Clear any previous hidden inputs to avoid duplicates
            const existingInputs = form.querySelectorAll('input[name="customer_name"], input[name="customer_phone"], input[name="doctor_name"]');
            existingInputs.forEach(input => input.remove());
            form.insertAdjacentHTML('beforeend', inputs);

            // Submit the form
            closePersonalDetailsModal();
            form.submit();
        }

        // Print bill
        function printBill(saleId) {
            if (!saleId) {
                alert('No bill available to print.');
                return;
            }
            fetch(`php/get_bill.php?sale_id=${saleId}`)
                .then(response => response.text())
                .then(billContent => {
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                        <html>
                        <head>
                            <title>Bill</title>
                            <link rel="stylesheet" href="css/style.css">
                        </head>
                        <body>
                            ${billContent}
                        </body>
                        </html>
                    `);
                    printWindow.document.close();
                    setTimeout(() => {
                        printWindow.print();
                    }, 500); // Delay to ensure styles load
                })
                .catch(error => {
                    console.error('Error fetching bill:', error);
                    alert('Failed to load bill. Please try again.');
                });
        }

        // Reset cart and form
        function resetCart() {
            cart = [];
            updateCartTable();
            document.getElementById('sellingForm').reset();
            document.getElementById('subtotal').value = '';
            document.getElementById('bill_discount').value = '0';
            document.getElementById('billPreview').style.display = 'none';
        }

        // Event listeners
        document.getElementById('medicine_id').addEventListener('change', updateSubtotal);
        document.getElementById('quantity').addEventListener('input', updateSubtotal);
    </script>
    <script src="js/script.js"></script>
</body>
</html>