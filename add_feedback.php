<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $logID = filter_input(INPUT_POST, "logID", FILTER_SANITIZE_NUMBER_INT);
    $inputDetails = filter_input(INPUT_POST, "inputDetails", FILTER_SANITIZE_STRING);

    if ($logID === false || $inputDetails === false) {
        echo "Invalid input.";
        error_log("Invalid input in add_feedback.php");
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO user_input (LogID, InputDetails) VALUES (:logID, :inputDetails)");
    $stmt->bindParam(':logID', $logID, PDO::PARAM_INT);
    $stmt->bindParam(':inputDetails', $inputDetails, PDO::PARAM_STR);

    try {
        if ($stmt->execute()) {
            echo "Feedback added.";
        } else {
            echo "Failed to add feedback.";
            error_log("Failed to add feedback in add_feedback.php");
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        error_log("Database error in add_feedback.php: " . $e->getMessage());
    }
}
?>
