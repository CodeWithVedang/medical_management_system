<?php
session_start();

// Function to call the Python script
function callPythonScript($mode, $args) {
    $pythonPath = "python"; // Adjust this path
    $escaped_args = array_map('escapeshellarg', $args);
    $command = "$pythonPath ../python/symptom_checker.py $mode " . implode(" ", $escaped_args);
    $output = shell_exec($command . " 2>&1");
    error_log("Python Command: $command", 3, "../debug.log");
    error_log("Python Output: $output", 3, "../debug.log");
    $result = json_decode($output, true);
    error_log("Python Script Result: " . json_encode($result), 3, "../debug.log");
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'feedback') {
        $symptoms = $_POST['symptoms'] ?? '';
        $medicine_ids = $_POST['medicine_ids'] ?? [];
        $rating = intval($_POST['rating'] ?? 0);

        if ($rating < 1 || $rating > 5 || empty($medicine_ids)) {
            header('Location: ../symptom_checker.php?status=error');
            exit;
        }

        $medicine_ids_str = implode(",", $medicine_ids);
        $result = callPythonScript("feedback", [$symptoms, $medicine_ids_str, $rating]);

        if (isset($result['error'])) {
            header('Location: ../symptom_checker.php?status=error');
            exit;
        }

        header('Location: ../symptom_checker.php?status=feedback_success');
        exit;
    }
}

header('Location: ../symptom_checker.php?status=error');