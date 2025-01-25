<?php
require_once 'config.php';
require_once 'header.php';


// Establish connection
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Check if employee exists using their name
        $employee_name = $_POST['employee_name'];
        $query = "SELECT id FROM employees WHERE name = :name";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $employee_name);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new Exception('Employee not found.');
        }

        // Get the employee_id
        $employee_id = $stmt->fetchColumn();

        // Insert OT data for the employee
        $ot_hours = $_POST['ot_hours'];
        $insert_query = "INSERT INTO employee_ot (employee_id, ot_hours) VALUES (:employee_id, :ot_hours)";
        $insert_stmt = $db->prepare($insert_query);
        $insert_stmt->bindParam(':employee_id', $employee_id);
        $insert_stmt->bindParam(':ot_hours', $ot_hours);

        if ($insert_stmt->execute()) {
            echo "OT Assigned Successfully!";
        } else {
            throw new Exception('Failed to assign OT.');
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<form method="POST">
    <label for="employee_name">Employee Name</label>
    <input type="text" name="employee_name" required placeholder="Enter employee name" />
    
    <label for="ot_hours">OT Hours</label>
    <input type="number" step="0.1" name="ot_hours" required placeholder="Enter OT hours" />
    
    <button type="submit">Assign OT</button>
</form>

