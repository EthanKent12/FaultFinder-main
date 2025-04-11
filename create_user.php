<?php
session_start();  // Start the session

// Ensure only admin can access
if ($_SESSION['AccessID'] != 1) {
    echo "Access denied!";
    exit();
}

include "db.php";  // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $access_level = $_POST['access_level'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute the INSERT query to create a new user
    $sql = "INSERT INTO credential (Username, PasswordHash, AccessID) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $hashed_password, $access_level);

    if ($stmt->execute()) {
        // Redirect to the admin page after creating the user
        header("Location: admin.php");
        exit();
    } else {
        echo "Error creating user.";
    }
}
?>
