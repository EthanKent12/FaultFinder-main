<?php
$host = '127.0.0.1';
$dbname = 'faultfinder';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

// Error handling
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
