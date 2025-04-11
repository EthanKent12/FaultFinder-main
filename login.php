<?php
session_start(); // Start the session

require 'db.php'; // Ensure this file is correctly set up

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Admin login bypass (for testing)
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['username'] = 'admin';
        $_SESSION['role'] = 'admin';
        $_SESSION['AccessID'] = 1; // Set AccessID for admin
        header("Location: admin.php"); // Redirect to admin page
        exit();
    }

    // Guest login bypass (for testing)
    if ($username === 'guest' && $password === 'guest123') {
        $_SESSION['username'] = 'guest';
        $_SESSION['role'] = 'guest';
        header("Location: dashboard.html"); // Redirect to dashboard page
        exit();
    }

    // Check if the username exists in the database
    $query = "SELECT * FROM credential WHERE Username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // User exists, verify the password
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['PasswordHash'])) {
            // Set session variables
            $_SESSION['username'] = $user['Username'];
            $_SESSION['role'] = $user['AccessID']; // Store AccessID in session
            $_SESSION['AccessID'] = $user['AccessID']; // Ensure correct AccessID

            // Redirect based on role
            if ($user['AccessID'] == 1) { // Admin role
                header("Location: admin.php"); // Admin page
            } else {
                header("Location: dashboard.html"); // Regular user redirect
            }
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User does not exist.";
    }

    $stmt->close();
    $conn->close();
}
?>
