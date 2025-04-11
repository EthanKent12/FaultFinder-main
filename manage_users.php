<?php
include "session.php";
include "db.php";

// Ensure only admin can access
if ($_SESSION['AccessID'] != 1) {
    echo "Access denied!";
    exit();
}

// Check if the action is delete and id is provided
if ($_GET['action'] == "delete" && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the DELETE query
    $sql = "DELETE FROM credential WHERE CredentialID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect immediately after successful deletion
        header("Location: admin.php");
        exit();
    } else {
        // Error message is printed only if something fails
        echo "Error deleting user.";
    }
}
?>
