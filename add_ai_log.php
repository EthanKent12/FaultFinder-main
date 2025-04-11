<?php
require 'db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productID = filter_input(INPUT_POST, "productID", FILTER_SANITIZE_NUMBER_INT);
    $interactionDetails = filter_input(INPUT_POST, "interactionDetails", FILTER_SANITIZE_STRING);

    if ($productID === false || $interactionDetails === false) {
        echo "Invalid input.";
        error_log("Invalid input in add_ai_log.php");
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO ai_interaction_log (ProductID, InteractionDetails) VALUES (:productID, :interactionDetails)");
    $stmt->bindParam(':productID', $productID, PDO::PARAM_INT);
    $stmt->bindParam(':interactionDetails', $interactionDetails, PDO::PARAM_STR);

    try {
        if ($stmt->execute()) {
            echo "AI interaction logged successfully.";
        } else {
            echo "Failed to log AI interaction.";
            error_log("Failed to log AI interaction in add_ai_log.php");
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        error_log("Database error in add_ai_log.php: " . $e->getMessage());
    }
}
?>
