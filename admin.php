<?php
session_start(); // Start the session

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['AccessID'] != 1) {
    echo "Access denied!";
    exit();
}

include "db.php"; // Include the database connection

// Handle form submission to add a new user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password']; // Password should not be sanitized
    $access_level = filter_input(INPUT_POST, 'access_level', FILTER_SANITIZE_NUMBER_INT);

    if ($username === false || empty($password) || $access_level === false) {
        $error_message = "Invalid input.";
        error_log("Invalid input in admin.php");
    } else {
        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $sql = "INSERT INTO credential (Username, PasswordHash, AccessID) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $hashed_password, $access_level);

        try {
            if ($stmt->execute()) {
                $success_message = "User created successfully!";
            } else {
                $error_message = "Error creating user.";
                error_log("Error creating user in admin.php");
            }
        } catch (Exception $e) {
            $error_message = "Database error: " . $e->getMessage();
            error_log("Database error in admin.php: " . $e->getMessage());
        }

        $stmt->close();
    }
}

// Fetch all users and roles (access levels)
$sql = "SELECT c.CredentialID, c.Username, a.LevelName FROM credential c
        JOIN access_level a ON c.AccessID = a.AccessID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e88e5',
                        'primary-dark': '#0c66b6',
                        secondary: '#0e3c65'
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        [x-cloak] { display: none !important; }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            .sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div x-data="{ sidebarOpen: false }">
        <!-- Mobile Menu Button -->
        <div class="md:hidden fixed top-4 left-4 z-50">
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-md bg-blue-600 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Sidebar -->
        <aside :class="{'open': sidebarOpen}" class="sidebar fixed top-0 left-0 h-full w-64 bg-white shadow-lg p-4 z-40">
            <div class="flex justify-between items-center mb-8">
                <img src="stratusLogo.png" alt="Stratus Logo" class="w-40">
                <button @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <ul class="space-y-2">
                <li>
                    <a href="dashboard.html" class="flex items-center space-x-2 py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                            </svg>
                            <span>Bots</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform" :class="{'rotate-180': open}" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <ul x-show="open" x-cloak class="mt-1 ml-6 space-y-1">
                        <li><a href="botdetails.html?bot=Pirate Bot" class="block py-2 px-4 text-gray-600 hover:text-blue-600 rounded-lg">Pirate Bot</a></li>
                        <li><a href="botdetails.html?bot=Tim Bot" class="block py-2 px-4 text-gray-600 hover:text-blue-600 rounded-lg">Tim Bot</a></li>
                        <li><a href="botdetails.html?bot=Phillip Bot" class="block py-2 px-4 text-gray-600 hover:text-blue-600 rounded-lg">Phillip Bot</a></li>
                        <li><a href="botdetails.html?bot=BluePeak IT AI" class="block py-2 px-4 text-gray-600 hover:text-blue-600 rounded-lg">BluePeak IT AI</a></li>
                        <li><a href="botdetails.html?bot=AI Assistant" class="block py-2 px-4 text-gray-600 hover:text-blue-600 rounded-lg">AI Assistant</a></li>
                        <li><a href="botdetails.html?bot=Nate Bot" class="block py-2 px-4 text-gray-600 hover:text-blue-600 rounded-lg">Nate Bot</a></li>
                    </ul>
                </li>
                <li>
                    <a href="admin.php" class="flex items-center space-x-2 py-2 px-4 bg-blue-50 text-blue-700 rounded-lg font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 005 10a6 6 0 0012 0c0-.35-.035-.691-.1-1.021A5 5 0 0010 11z" clip-rule="evenodd" />
                        </svg>
                        <span>Admin</span>
                    </a>
                </li>
                <li>
                    <a href="logout.php" class="flex items-center space-x-2 py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                        </svg>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
            <p class="absolute bottom-4 text-sm text-gray-500">© BLUEPEAK IT 2025</p>
        </aside>

        <!-- Main Content -->
        <main class="md:ml-64 min-h-screen transition-all duration-300" :class="{'ml-0': !sidebarOpen}">
            <!-- Header -->
            <header class="bg-white shadow-sm p-4 sticky top-0 z-30">
                <div class="container mx-auto">
                    <div class="flex justify-between items-center">
                        <h1 class="text-xl font-semibold text-gray-800">Admin Dashboard</h1>
                        <div class="flex items-center space-x-4">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                                    <img src="profilephoto.png" alt="User Avatar" class="w-10 h-10 rounded-full object-cover border-2 border-blue-500">
                                    <span class="hidden md:block text-gray-700"><?= $_SESSION['username']; ?></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                                    <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="container mx-auto p-4">
                <!-- Welcome Message -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Welcome, <?= $_SESSION['username']; ?></h2>
                    <p class="text-gray-600">Manage users and access levels from this admin panel.</p>
                </div>

                <!-- Notification Messages -->
                <?php if (isset($success_message)): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md">
                        <p><?= $success_message; ?></p>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md">
                        <p><?= $error_message; ?></p>
                    </div>
                <?php endif; ?>

                <!-- Create User Form -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Create New User</h2>
                    </div>
                    <div class="p-6">
                        <form action="admin.php" method="POST" class="space-y-4">
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                <input 
                                    type="text" 
                                    id="username"
                                    name="username" 
                                    placeholder="Enter Username" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                >
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input 
                                    type="password" 
                                    id="password"
                                    name="password" 
                                    placeholder="Enter Password" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                >
                            </div>
                            <div>
                                <label for="access_level" class="block text-sm font-medium text-gray-700 mb-1">Access Level</label>
                                <select 
                                    id="access_level"
                                    name="access_level" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                >
                                    <option value="">Select Access Level</option>
                                    <?php
                                    // Fetch access levels from the database
                                    $accessLevelsSql = "SELECT * FROM access_level ORDER BY AccessID";
                                    $accessLevelsResult = $conn->query($accessLevelsSql);
                                    
                                    while ($accessLevel = $accessLevelsResult->fetch_assoc()) {
                                        echo "<option value=\"" . $accessLevel['AccessID'] . "\">" . $accessLevel['LevelName'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <button 
                                    type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                                >
                                    Create User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- User List Table -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Existing Users</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $row['Username'] ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $row['LevelName'] ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <a href="update_role.php?id=<?= $row['CredentialID'] ?>" class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors mr-2">Change Role</a>
                                                <a href="manage_users.php?action=delete&id=<?= $row['CredentialID'] ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="px-3 py-1 bg-red-100 text-red-700 rounded-full hover:bg-red-200 transition-colors">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No users found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
