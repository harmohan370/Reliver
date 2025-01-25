<?php
require_once("config.php");
require_once 'header.php';

// Set the current date or the selected date
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Retrieve attendance records along with vehicle allocation details
$sql = "SELECT 
            e.id AS employee_id,
            e.name AS employee_name,
            v.vehicle_number AS vehicle_number,
	    v.name AS name,
            a.status
        FROM employees e
        LEFT JOIN attendance a ON e.id = a.employee_id AND DATE(a.date) = :selected_date
        LEFT JOIN vehicle_allocations va ON e.id = va.employee_id AND va.allocation_date <= :selected_date
        LEFT JOIN vehicles v ON va.vehicle_id = v.id
        WHERE a.status IN ('P', 'J')
        ORDER BY e.id";

try {
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':selected_date', $date);
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
    <title>Employee Attendance with Vehicles</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .present {
            color: green;
            font-weight: bold;
        }
        .joining {
            color: brown;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Attendance for <?php echo htmlspecialchars(date('F j, Y', strtotime($date))); ?></h2>

    <!-- Date Picker Form -->
    <form method="GET" action="">
        <label for="date">Select Date:</label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
        <button type="submit">Submit</button>
    </form>

    <!-- Attendance Table -->
    <?php if (empty($attendance_records)): ?>
        <p>No records found for the selected date.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Status</th>
                    <th>Dumper Number</th>
                    <th>Make</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_records as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['employee_id']); ?></td>
                        <td><?php echo htmlspecialchars($record['employee_name']); ?></td>
                        <td class="<?php echo $record['status'] === 'P' ? 'present' : 'joining'; ?>">
                            <?php echo $record['status'] === 'P' ? 'Present' : 'Joining'; ?>
                        </td>
                       <td><?php echo htmlspecialchars($record['vehicle_number'] ?: 'N/A'); ?></td>
                       <td><?php echo htmlspecialchars($record['name'] ?: 'N/A'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="index.php"><button type="button">Back</button></a>
</body>
</html>

<?php require_once 'layout/footer.php'; ?>
