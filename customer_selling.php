<?php
session_start();
$status = isset($_GET['status']) ? $_GET['status'] : '';
$sale_id = isset($_SESSION['sale_id']) ? $_SESSION['sale_id'] : null;
$bill = '';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Role for access control
$user_role = $_SESSION['role'];

include 'php/config.php';
// Log session and status for debugging
error_log("Status: " . ($status ?? 'Not set'));
error_log("Session sale_id: " . ($sale_id ?? 'Not set'));

if ($sale_id) {
    // Fetch bill content dynamically from get_bill.php
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/medical_management_system/php/get_bill.php?sale_id=$sale_id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $bill = curl_exec($ch);
    if (curl_errno($ch)) {
        $bill = '<p>Error loading bill. Please try again.</p>';
        error_log("cURL error: " . curl_error($ch));
    }
    curl_close($ch);

    // Log bill content for debugging
    error_log("Bill content length: " . strlen($bill));
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Management System - Customer Selling</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Modal styles (for personal details modal only) */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            width: 300px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .modal-content h3 {
            margin-top: 0;
        }
        .modal-content input {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            box-sizing: border-box;
        }
        .modal-content button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        /* Style for Hard Reset button */
        .hard-reset-btn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }
        .hard-reset-btn:hover {
            background-color: #c82333;
        }
        /* Two-column layout */
        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
        }
        .left-section {
            flex: 1;
            min-width: 300px;
        }
        .right-section {
            flex: 0;
            min-width: 300px;
            max-width: 400px;
        }
        /* QR code scanner styles */
        #qrScannerVideo {
            width: 100%;
            height: auto;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        #qrScannerCanvas {
            display: none;
        }
        #qrScannerStatus {
            margin: 10px 0;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            text-align: center;
        }
        /* Ensure sections are responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .right-section {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Left Section: Form and Cart -->
        <div class="left-section">
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
                    <li><a href="symptom_checker.php">Symptom Checker</a></li>
                    <li class="user-info">
                <a href="php/logout.php" class="logout-btn">Logout</a>
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
                <div style="margin-top: 10px;">
                    <button type="button" onclick="resetCart()">Reset Cart</button>
                    <button type="button" class="hard-reset-btn" onclick="checkHardResetAccess('<?php echo $user_role; ?>')">Hard Reset</button>
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
                </form>
            </section>

            <!-- Bill Preview -->
            <section class="bill-section" id="billPreview" <?php echo $bill ? '' : 'style="display: none;"'; ?>>
                <h2>Bill Preview</h2>
                <div id="billContent"><?php echo $bill; ?></div>
                <button onclick="printBill(<?php echo json_encode($sale_id); ?>)">Print Bill</button>
            </section>
        </div>

        <!-- Right Section: QR Code Scanner -->
        <div class="right-section">
            <h3>QR Code Scanner</h3>
            <video id="qrScannerVideo" autoplay playsinline></video>
            <canvas id="qrScannerCanvas" style="display: none;"></canvas>
            <div id="qrScannerStatus">Camera not initialized...</div>
        </div>
    </div>

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

    <!-- Include jsQR for QR code scanning -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script>
        let cart = [];
        let selectedPrice = 0;
        let selectedStock = 0;
        let selectedName = '';
        let saleId = <?php echo json_encode($sale_id); ?>;
        let scannerActive = false;
        let video = null;
        let canvasElement = null;
        let canvas = null;
        let statusElement = null;

        // Log initial variables for debugging
        console.log('Initial saleId:', saleId);
        console.log('Initial status:', '<?php echo $status; ?>');

        // Initialize QR code scanner on page load
        function initializeQRScanner() {
            video = document.getElementById('qrScannerVideo');
            canvasElement = document.getElementById('qrScannerCanvas');
            canvas = canvasElement.getContext('2d');
            statusElement = document.getElementById('qrScannerStatus');

            // Set initial status
            statusElement.textContent = 'Initializing camera...';

            // Request camera access
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
                .then(stream => {
                    video.srcObject = stream;
                    video.setAttribute("playsinline", true); // Required for iOS
                    video.play();
                    scannerActive = true;
                    statusElement.textContent = 'Scanning for QR code...';
                    requestAnimationFrame(tick);
                })
                .catch(err => {
                    console.error('Camera access error:', err);
                    statusElement.textContent = 'Failed to access camera. Please grant camera permission.';
                });
        }

        // Process video frames to detect QR code
        function tick() {
            if (!scannerActive) return;

            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvasElement.height = video.videoHeight;
                canvasElement.width = video.videoWidth;
                canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                const imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: "dontInvert"
                });

                if (code) {
                    console.log('QR code detected:', code.data);
                    statusElement.textContent = `QR code detected: ${code.data}`;

                    // Assume QR code contains the medicine ID
                    const medicineId = parseInt(code.data);
                    if (isNaN(medicineId)) {
                        statusElement.textContent = 'Invalid QR code format. Expected a numeric medicine ID.';
                        setTimeout(() => {
                            if (scannerActive) {
                                statusElement.textContent = 'Scanning for QR code...';
                                requestAnimationFrame(tick);
                            }
                        }, 2000);
                        return;
                    }

                    // Search in inventory
                    statusElement.textContent = 'Searching in inventory...';

                    // Simulate a delay for better UX (remove in production if not needed)
                    setTimeout(() => {
                        // Fetch medicine details based on the scanned ID
                        const select = document.getElementById('medicine_id');
                        const option = Array.from(select.options).find(opt => parseInt(opt.value) === medicineId);

                        if (!option) {
                            statusElement.textContent = `Medicine not found for QR code: ${code.data}`;
                            setTimeout(() => {
                                if (scannerActive) {
                                    statusElement.textContent = 'Scanning for QR code...';
                                    requestAnimationFrame(tick);
                                }
                            }, 2000);
                            return;
                        }

                        selectedPrice = parseFloat(option.getAttribute('data-price')) || 0;
                        selectedStock = parseInt(option.getAttribute('data-stock')) || 0;
                        selectedName = option.getAttribute('data-name') || '';

                        statusElement.textContent = `Found medicine: ${selectedName}`;

                        // Set the quantity to 1 by default (user can adjust later in the cart)
                        const quantity = 1;

                        if (selectedStock <= 0) {
                            statusElement.textContent = 'Medicine is out of stock!';
                            setTimeout(() => {
                                if (scannerActive) {
                                    statusElement.textContent = 'Scanning for QR code...';
                                    requestAnimationFrame(tick);
                                }
                            }, 2000);
                            return;
                        }

                        if (quantity > selectedStock) {
                            statusElement.textContent = `Quantity exceeds available stock! Available: ${selectedStock}`;
                            setTimeout(() => {
                                if (scannerActive) {
                                    statusElement.textContent = 'Scanning for QR code...';
                                    requestAnimationFrame(tick);
                                }
                            }, 2000);
                            return;
                        }

                        const subtotal = selectedPrice * quantity;

                        // Check if medicine is already in cart
                        const existingItem = cart.find(item => item.medicineId === medicineId.toString());
                        if (existingItem) {
                            statusElement.textContent = 'This medicine is already in the cart. Update the quantity in the table.';
                            setTimeout(() => {
                                if (scannerActive) {
                                    statusElement.textContent = 'Scanning for QR code...';
                                    requestAnimationFrame(tick);
                                }
                            }, 2000);
                            return;
                        }

                        // Add to cart
                        cart.push({
                            medicineId: medicineId.toString(),
                            name: selectedName,
                            price: selectedPrice,
                            quantity,
                            subtotal,
                            stock: selectedStock
                        });

                        updateCartTable();
                        setTimeout(() => {
                            if (scannerActive) {
                                statusElement.textContent = 'Scanning for QR code...';
                                requestAnimationFrame(tick);
                            }
                        }, 1000);
                    }, 1000); // Simulated delay for searching
                    return;
                }
            }
            requestAnimationFrame(tick);
        }
        function checkHardResetAccess(role) {
            if (role == 'Admin') {
                                hardReset();

            }else{
                                alert('Access Denied: Only Admin can use the Hard Reset button.');

            }
        }
        // Hard Reset function to clear all data
        function hardReset() {
            if (!confirm('Are you sure you want to perform a hard reset? This will clear all data (medicines, sales, etc.) and cannot be undone.')) {
                return;
            }

            fetch('php/reset_all_data.php', {
                method: 'POST'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    alert('All data has been cleared successfully.');
                    // Reset the UI and reload the page
                    cart = [];
                    updateCartTable();
                    document.getElementById('sellingForm').reset();
                    document.getElementById('subtotal').value = '';
                    document.getElementById('bill_discount').value = '0';
                    document.getElementById('billPreview').style.display = 'none';
                    window.location.reload(); // Reload to reflect the cleared database state
                } else {
                    alert('Failed to reset data: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error resetting data:', error);
                alert('Error resetting data: ' + error.message);
            });
        }

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

        // Add medicine to cart table (manual selection)
        function addToCart() {
            const medicineId = document.getElementById('medicine_id').value;
            const quantity = parseInt(document.getElementById('quantity').value) || 0;
            const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;

            if (!medicineId || quantity <= 0) {
                alert('Please select a medicine and enter a valid quantity.');
                return;
            }

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

            const form = document.getElementById('cartForm');
            const inputs = `
                <input type="hidden" name="customer_name" value="${customerName}">
                <input type="hidden" name="customer_phone" value="${customerPhone}">
                <input type="hidden" name="doctor_name" value="${doctorName}">
            `;
            const existingInputs = form.querySelectorAll('input[name="customer_name"], input[name="customer_phone"], input[name="doctor_name"]');
            existingInputs.forEach(input => input.remove());
            form.insertAdjacentHTML('beforeend', inputs);

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
                    }, 500);
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

        window.onclick = function(event) {
            const personalModal = document.getElementById('personalDetailsModal');
            if (event.target === personalModal) {
                closePersonalDetailsModal();
            }
        };

        // Event listeners
        document.getElementById('medicine_id').addEventListener('change', updateSubtotal);
        document.getElementById('quantity').addEventListener('input', updateSubtotal);

        // Initialize QR scanner on page load
        window.addEventListener('load', initializeQRScanner);

        console.log('customer_selling.js script loaded');
    </script>
    <script src="js/script.js"></script>
</body>
</html>