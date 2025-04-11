<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $testLogID = filter_input(INPUT_POST, "testLogID", FILTER_SANITIZE_NUMBER_INT);
    $errorDetails = filter_input(INPUT_POST, "errorDetails", FILTER_SANITIZE_STRING);

    if ($testLogID === false || $errorDetails === false) {
        echo "Invalid input.";
        error_log("Invalid input in add_failure.php");
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO failure_log (TestLogID, ErrorDetails) VALUES (:testLogID, :errorDetails)");
    $stmt->bindParam(':testLogID', $testLogID, PDO::PARAM_INT);
    $stmt->bindParam(':errorDetails', $errorDetails, PDO::PARAM_STR);

    try {
        if ($stmt->execute()) {
            echo "Test failure recorded.";
        } else {
            echo "Failed to record failure.";
            error_log("Failed to record failure in add_failure.php");
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        error_log("Database error in add_failure.php: " . $e->getMessage());
    }
}
?>
