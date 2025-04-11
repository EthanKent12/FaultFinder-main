<?php
// Include database connection
include "db.php";

// Check if the access_level table exists
$checkTableSql = "SHOW TABLES LIKE 'access_level'";
$tableResult = $conn->query($checkTableSql);

if ($tableResult->num_rows == 0) {
    // Create the access_level table if it doesn't exist
    $createTableSql = "CREATE TABLE IF NOT EXISTS `access_level` (
        `AccessID` int(11) NOT NULL AUTO_INCREMENT,
        `LevelName` varchar(50) NOT NULL,
        PRIMARY KEY (`AccessID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if ($conn->query($createTableSql) === TRUE) {
        echo "Table 'access_level' created successfully.<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

// Check if the access levels already exist
$checkLevelsSql = "SELECT * FROM access_level";
$levelsResult = $conn->query($checkLevelsSql);

if ($levelsResult->num_rows == 0) {
    // Insert the access levels
    $insertLevelsSql = "INSERT INTO `access_level` (`AccessID`, `LevelName`) VALUES
        (1, 'Admin'),
        (2, 'Developer'),
        (3, 'Guest');";
    
    if ($conn->query($insertLevelsSql) === TRUE) {
        echo "Access levels inserted successfully.<br>";
    } else {
        echo "Error inserting access levels: " . $conn->error . "<br>";
    }
} else {
    echo "Access levels already exist.<br>";
    
    // Display existing access levels
    echo "<h3>Existing Access Levels:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>AccessID</th><th>LevelName</th></tr>";
    
    while ($row = $levelsResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['AccessID'] . "</td>";
        echo "<td>" . $row['LevelName'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

// Check if the credential table exists
$checkCredentialTableSql = "SHOW TABLES LIKE 'credential'";
$credentialTableResult = $conn->query($checkCredentialTableSql);

if ($credentialTableResult->num_rows == 0) {
    // Create the credential table if it doesn't exist
    $createCredentialTableSql = "CREATE TABLE IF NOT EXISTS `credential` (
        `CredentialID` int(11) NOT NULL AUTO_INCREMENT,
        `Username` varchar(50) NOT NULL,
        `PasswordHash` varchar(255) NOT NULL,
        `AccessID` int(11) NOT NULL,
        PRIMARY KEY (`CredentialID`),
        FOREIGN KEY (`AccessID`) REFERENCES `access_level` (`AccessID`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if ($conn->query($createCredentialTableSql) === TRUE) {
        echo "Table 'credential' created successfully.<br>";
    } else {
        echo "Error creating credential table: " . $conn->error . "<br>";
    }
}

// Check if the admin user exists
$checkAdminSql = "SELECT * FROM credential WHERE Username = 'admin'";
$adminResult = $conn->query($checkAdminSql);

if ($adminResult->num_rows == 0) {
    // Create the admin user
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $insertAdminSql = "INSERT INTO `credential` (`Username`, `PasswordHash`, `AccessID`) VALUES
        ('admin', '$adminPassword', 1);";
    
    if ($conn->query($insertAdminSql) === TRUE) {
        echo "Admin user created successfully.<br>";
    } else {
        echo "Error creating admin user: " . $conn->error . "<br>";
    }
} else {
    echo "Admin user already exists.<br>";
}

// Check if the guest user exists
$checkGuestSql = "SELECT * FROM credential WHERE Username = 'guest'";
$guestResult = $conn->query($checkGuestSql);

if ($guestResult->num_rows == 0) {
    // Create the guest user
    $guestPassword = password_hash('guest123', PASSWORD_DEFAULT);
    $insertGuestSql = "INSERT INTO `credential` (`Username`, `PasswordHash`, `AccessID`) VALUES
        ('guest', '$guestPassword', 3);";
    
    if ($conn->query($insertGuestSql) === TRUE) {
        echo "Guest user created successfully.<br>";
    } else {
        echo "Error creating guest user: " . $conn->error . "<br>";
    }
} else {
    echo "Guest user already exists.<br>";
}

// Display all users
$allUsersSql = "SELECT c.CredentialID, c.Username, a.LevelName 
                FROM credential c
                JOIN access_level a ON c.AccessID = a.AccessID";
$allUsersResult = $conn->query($allUsersSql);

if ($allUsersResult->num_rows > 0) {
    echo "<h3>Existing Users:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Username</th><th>Role</th></tr>";
    
    while ($row = $allUsersResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['CredentialID'] . "</td>";
        echo "<td>" . $row['Username'] . "</td>";
        echo "<td>" . $row['LevelName'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

echo "<p>Database setup complete. <a href='login.html'>Go to login page</a></p>";

$conn->close();
?>
