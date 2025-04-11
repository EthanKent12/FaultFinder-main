<?php
session_start();  // Start the session

if ($_SESSION['AccessID'] != 1) {  // Ensure only admin can access
    echo "Access denied!";
    exit();
}

include "db.php";  // Database connection

// Get user details
$userId = $_GET['id'];
$userSql = "SELECT Username FROM credential WHERE CredentialID = ?";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();
$username = $userData['Username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['userId'];
    $newRole = $_POST['accessId'];

    // Update the user's access level
    $sql = "UPDATE credential SET AccessID = ? WHERE CredentialID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $newRole, $userId);

    if ($stmt->execute()) {
        $success_message = "Role updated successfully!";
        // Redirect after a short delay
        header("refresh:2;url=admin.php");
    } else {
        $error_message = "Error updating role.";
    }
}

// Get all access levels
$sql = "SELECT * FROM access_level";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Role</title>
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
                        <h1 class="text-xl font-semibold text-gray-800">Update User Role</h1>
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
                <!-- Notification Messages -->
                <?php if (isset($success_message)): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md">
                        <p><?= $success_message; ?></p>
                        <p class="mt-2 text-sm">Redirecting to admin page...</p>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md">
                        <p><?= $error_message; ?></p>
                    </div>
                <?php endif; ?>

                <!-- Update Role Form -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Update Role for User: <?= $username ?></h2>
                    </div>
                    <div class="p-6">
                        <form action="update_role.php?id=<?= $userId ?>" method="POST" class="space-y-4">
                            <input type="hidden" name="userId" value="<?= $userId ?>">
                            <div>
                                <label for="accessId" class="block text-sm font-medium text-gray-700 mb-1">Select New Role</label>
                                <select 
                                    id="accessId"
                                    name="accessId" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                >
                                    <?php 
                                    // Reset the result pointer
                                    $result->data_seek(0);
                                    
                                    // Get the current user's access level
                                    $currentAccessSql = "SELECT AccessID FROM credential WHERE CredentialID = ?";
                                    $currentAccessStmt = $conn->prepare($currentAccessSql);
                                    $currentAccessStmt->bind_param("i", $userId);
                                    $currentAccessStmt->execute();
                                    $currentAccessResult = $currentAccessStmt->get_result();
                                    $currentAccess = $currentAccessResult->fetch_assoc()['AccessID'];
                                    
                                    while ($row = $result->fetch_assoc()): 
                                        $selected = ($row['AccessID'] == $currentAccess) ? 'selected' : '';
                                    ?>
                                        <option value="<?= $row['AccessID'] ?>" <?= $selected ?>><?= $row['LevelName'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="flex space-x-4">
                                <button 
                                    type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                                >
                                    Update Role
                                </button>
                                <a 
                                    href="admin.php" 
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                                >
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
