<?php 
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $accessID = $_POST["accessID"];

    // Check if the username is empty or too short
    if (empty($username) || strlen($username) < 3) {
        echo "Username must be at least 3 characters long.";
        exit();
    }

    // Check if the password is empty or too short
    if (empty($password) || strlen($password) < 6) {
        echo "Password must be at least 6 characters long.";
        exit();
    }

    // Ensure that the accessID exists
    $stmt_check_accessID = $pdo->prepare("SELECT * FROM access_level WHERE AccessID = ?");
    $stmt_check_accessID->execute([$accessID]);

    if ($stmt_check_accessID->rowCount() == 0) {
        echo "Invalid AccessID.";
        exit();
    }

    // Check if the username already exists
    $stmt_check_username = $pdo->prepare("SELECT * FROM credential WHERE Username = ?");
    $stmt_check_username->execute([$username]);

    if ($stmt_check_username->rowCount() > 0) {
        echo "Username is already taken. Please choose another one.";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement to insert the new user into the database
    $stmt = $pdo->prepare("INSERT INTO credential (Username, PasswordHash, AccessID) VALUES (?, ?, ?)");
    
    // Execute the query
    if ($stmt->execute([$username, $hashed_password, $accessID])) {
        echo "User registered successfully.";
    } else {
        echo "Registration failed.";
    }
}
?>
