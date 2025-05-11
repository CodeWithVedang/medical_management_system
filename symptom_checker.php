<?php
session_start();

// Function to call the Python script
function callPythonScript($mode, $args) {
    // Adjust the Python path based on your system
    $pythonPath = "python"; // Use "python3" on Linux/Mac, or full path on Windows (e.g., "C:\\Python39\\python.exe")
    $escaped_args = array_map('escapeshellarg', $args);
    $command = "$pythonPath python/symptom_checker.py $mode " . implode(" ", $escaped_args);
    $output = shell_exec($command . " 2>&1");
    error_log("Python Command: $command", 3, "debug.log");
    error_log("Python Output: $output", 3, "debug.log");
    $result = json_decode($output, true);
    error_log("Python Script Result: " . json_encode($result), 3, "debug.log");
    return $result;
}

$status = '';
$diseases = [];
$suggestions = [];
$selected_symptoms = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    $selected_symptoms = $_POST['selected_symptoms'] ?? '';
    error_log("Submitted Symptoms: $selected_symptoms", 3, "debug.log");

    if (empty($selected_symptoms)) {
        $status = 'error';
        error_log("Error: No symptoms provided", 3, "debug.log");
    } else {
        $result = callPythonScript("predict", [$selected_symptoms]);
        if (isset($result['error']) || $result === null) {
            $status = 'python_error';
            $error_message = $result['error'] ?? 'Invalid JSON output';
            error_log("Error in Python Script: $error_message", 3, "debug.log");
        } else {
            $diseases = $result['diseases'] ?? [];
            $suggestions = $result['medicines'] ?? [];

            if (empty($suggestions)) {
                $status = 'no_suggestions';
                error_log("No medicines found for diseases: " . json_encode($diseases), 3, "debug.log");
            } else {
                $status = 'success';
                error_log("Results - Diseases: " . json_encode($diseases) . ", Medicines: " . json_encode($suggestions), 3, "debug.log");
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'feedback') {
    header('Location: php/process_symptom.php');
    exit;
}

// List of predefined symptoms for the dropdown
$available_symptoms = [
    'headache',
    'fever',
    'body pain',
    'runny nose',
    'sneezing',
    'cough',
    'sore throat',
    'nasal congestion'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical System - Symptom Checker</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <!-- Navbar -->
        <nav class="navbar">
            <div class="logo">
                <h2>Medical System</h2>
            </div>
            <button class="menu-toggle">☰</button>
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="index.php">Medicine Management</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="customer_selling.php">Customer Selling</a></li>
                <li><a href="sales_history.php">Sales History</a></li>
                <li><a href="symptom_checker.php" class="active">Symptom Checker</a></li>
            </ul>
        </nav>

        <!-- Status Messages -->
        <?php
        if ($status == 'success') {
            echo '<p class="status-message success">Symptoms analyzed successfully! See diagnosis and suggestions below.</p>';
        } elseif ($status == 'error') {
            echo '<p class="status-message error">Please select at least one symptom to proceed.</p>';
        } elseif ($status == 'no_suggestions') {
            echo '<p class="status-message error">No medicines found for the predicted diseases.</p>';
        } elseif ($status == 'python_error') {
            echo '<p class="status-message error">An error occurred while processing symptoms: ' . htmlspecialchars($error_message) . '</p>';
        }
        ?>

        <!-- Symptom Checker Form -->
        <section class="form-section symptom-checker-form">
            <h2>Symptom Checker</h2>
            <form id="symptomForm" method="POST">
                <div class="form-group symptom-selector">
                    <label for="symptom_select">Select Your Symptoms:</label>
                    <div class="symptom-input-group">
                        <select id="symptom_select" name="symptom_select">
                            <option value="">-- Select a Symptom --</option>
                            <?php foreach ($available_symptoms as $symptom): ?>
                            <option value="<?php echo htmlspecialchars($symptom); ?>">
                                <?php echo htmlspecialchars(ucfirst($symptom)); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" onclick="addSymptom()" class="add-symptom-btn">Add</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="selected_symptoms">Selected Symptoms:</label>
                    <input type="text" id="selected_symptoms" name="selected_symptoms" readonly placeholder="e.g., headache, cough" value="<?php echo htmlspecialchars($selected_symptoms); ?>">
                    <button type="button" onclick="clearSymptoms()" class="clear-btn">Clear</button>
                </div>
                <button type="submit" class="submit-btn">Check Symptoms</button>
            </form>
        </section>

        <!-- Diagnosis and Suggestions Section -->
        <?php if ($status == 'success' && !empty($suggestions)): ?>
        <section class="result-section">
            <h2>Diagnosis and Recommendations</h2>
            <div class="diagnosis-card">
                <h3>Possible Diseases</h3>
                <p><?php echo htmlspecialchars(implode(", ", $diseases)); ?></p>
            </div>
            <div class="suggestions-card">
                <h3>Recommended Medicines</h3>
                <table class="medicine-table">
                    <thead>
                        <tr>
                            <th>Medicine Name</th>
                            <th>Price (₹)</th>
                            <th>Stock Available</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suggestions as $medicine): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($medicine['name']); ?></td>
                            <td><?php echo number_format($medicine['price'], 2); ?></td>
                            <td><?php echo $medicine['stock']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Feedback Form -->
            <div class="feedback-card">
                <h3>Rate the Recommendation</h3>
                <form id="feedbackForm" action="php/process_symptom.php" method="POST">
                    <input type="hidden" name="action" value="feedback">
                    <input type="hidden" name="symptoms" value="<?php echo htmlspecialchars($selected_symptoms); ?>">
                    <?php foreach ($suggestions as $medicine): ?>
                    <input type="hidden" name="medicine_ids[]" value="<?php echo $medicine['id']; ?>">
                    <?php endforeach; ?>
                    <div class="form-group">
                        <label for="rating">How helpful was this recommendation? (1 = Not Helpful, 5 = Very Helpful):</label>
                        <select id="rating" name="rating" required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <button type="submit" class="submit-btn">Submit Feedback</button>
                </form>
            </div>
        </section>
        <?php endif; ?>
    </div>

    <script>
        // Add selected symptom to the input box
        function addSymptom() {
            const select = document.getElementById('symptom_select');
            const symptom = select.value;
            if (!symptom) {
                alert('Please select a symptom to add.');
                return;
            }

            const input = document.getElementById('selected_symptoms');
            let currentSymptoms = input.value.split(',').map(s => s.trim()).filter(s => s);

            // Avoid duplicates
            if (currentSymptoms.includes(symptom)) {
                alert('This symptom is already added.');
                return;
            }

            currentSymptoms.push(symptom);
            input.value = currentSymptoms.join(', ');
            select.value = ''; // Reset dropdown
        }

        // Clear all symptoms from the input box
        function clearSymptoms() {
            document.getElementById('selected_symptoms').value = '';
        }
    </script>
    <script src="js/script.js"></script>
</body>
</html>