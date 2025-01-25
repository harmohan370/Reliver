<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>

<?php
require_once 'config.php';
require_once 'header.php';

$database = new Database();
$db = $database->getConnection();

// Get today's attendance
$query = "SELECT 
    e.name AS employee_name,
    a.date,
    a.status
FROM employees e
LEFT JOIN attendance a ON e.id = a.employee_id 
    AND DATE(a.date) = CURDATE()
ORDER BY e.name";

try {
    $stmt = $db->prepare($query);
    $stmt->execute();
    $attendance_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $attendance_records = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reliver Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script>
        // Toggle dropdown menu
        function toggleDropdown(id) {
            document.getElementById(id).classList.toggle('hidden');
        }
    </script>
</head>
<body class="bg-gray-100 text-gray-900">
    <!-- Top Header -->
    <header class="bg-blue-800 text-white py-4">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold">Reliver Management System</h1>
        </div>
    </header>

    <!-- Navigation Menu -->
    <nav class="bg-gray-900 text-white py-3">
        <div class="container mx-auto px-4">
            <ul class="flex space-x-4">
                <!-- Attendance Menu -->
                <li class="relative group">
                    <button onclick="toggleDropdown('attendance-menu')" class="hover:text-yellow-300 focus:outline-none">
                        Attendance
                    </button>
                    <ul id="attendance-menu" class="hidden absolute bg-white text-gray-900 rounded-lg mt-1 shadow-lg">
                        <li><a href="add_attendance.php" class="block px-4 py-2 hover:bg-gray-200">Add Attendance</a></li>
                        <li><a href="view_attendance.php" class="block px-4 py-2 hover:bg-gray-200">View Attendance</a></li>
                    </ul>
                </li>

                <!-- Reliver Menu -->
                <li class="relative group">
                    <button onclick="toggleDropdown('reliver-menu')" class="hover:text-yellow-300 focus:outline-none">
                        Reliver
                    </button>
                    <ul id="reliver-menu" class="hidden absolute bg-white text-gray-900 rounded-lg mt-1 shadow-lg">
                        <li><a href="add_today_list.php" class="block px-4 py-2 hover:bg-gray-200">Add Today List</a></li>
                        <li><a href="reliver_list.php" class="block px-4 py-2 hover:bg-gray-200">Reliver List</a></li>
                    </ul>
                </li>

                

                <!-- Admin Menu -->
                <li class="relative group">
                    <button onclick="toggleDropdown('admin-menu')" class="hover:text-yellow-300 focus:outline-none">
                        Admin
                    </button>
                    <ul id="admin-menu" class="hidden absolute bg-white text-gray-900 rounded-lg mt-1 shadow-lg">
                        <li><a href="add_employee.php" class="block px-4 py-2 hover:bg-gray-200">Add Employee</a></li>
                        <li><a href="add_dumper.php" class="block px-4 py-2 hover:bg-gray-200">Add Dumper</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    

    <?php 
    // Include present.php at the end
    require_once 'present.php'; 
    ?>

</body>
</html>

