<?php
require_once 'config.php';
require_once 'header.php';


// Establish connection
$database = new Database();
$db = $database->getConnection();

$date = $_GET['date'];  // Get the date from the URL or form

// Query to retrieve OT data for a specific date
$query = "SELECT e.name, e.neis, o.ot_hours, o.ot_date 
          FROM employee_ot o
          JOIN employees e ON o.employee_id = e.id
          WHERE o.ot_date = :ot_date";

$stmt = $db->prepare($query);
$stmt->bindParam(':ot_date', $date);
$stmt->execute();

echo "<h2>OT Data for " . date('F j, Y', strtotime($date)) . "</h2>";
echo "<table border='1'>
        <tr>
            <th>Employee Name</th>
            <th>NEIS</th>
            <th>OT Hours</th>
            <th>OT Date</th>
        </tr>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['neis']) . "</td>
            <td>" . htmlspecialchars($row['ot_hours']) . "</td>
            <td>" . htmlspecialchars($row['ot_date']) . "</td>
          </tr>";
}

echo "</table>";
?>
