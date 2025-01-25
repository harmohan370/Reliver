<?php
require_once("config.php");
require_once 'header.php';

// Set the current date or the selected date
$current_month = date('m');
$current_year = date('Y');
$days_in_month = date('t');
$month_name = date('F Y');

// Create database connection
$database = new Database();
$db = $database->getConnection();

$employee_data = null;
$attendance_data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['employee_name'])) {
    try {
        // Search for employee
        $stmt = $db->prepare("
            SELECT id, name, father_name, post, neis 
            FROM employees 
            WHERE name LIKE :name
        ");
        $search_term = '%' . $_POST['employee_name'] . '%';
        $stmt->bindParam(":name", $search_term);
        $stmt->execute();
        $employee_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (isset($_POST['employee_id'])) {
            // Get attendance for selected employee with vehicle allocation details
            $stmt = $db->prepare("
                SELECT 
                    a.date, 
                    a.status,
                    v.vehicle_number,
                    v.name AS vehicle_name
                FROM attendance a
                LEFT JOIN vehicle_allocations va ON a.employee_id = va.employee_id 
                    AND DATE(va.allocation_date) = DATE(a.date)
                LEFT JOIN vehicles v ON va.vehicle_id = v.id
                WHERE a.employee_id = :employee_id 
                AND MONTH(a.date) = :month 
                AND YEAR(a.date) = :year
                ORDER BY a.date
            ");
            $stmt->bindParam(":employee_id", $_POST['employee_id']);
            $stmt->bindParam(":month", $current_month);
            $stmt->bindParam(":year", $current_year);
            $stmt->execute();
            $attendance_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch(PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Function to get status label with vehicle info
function getStatusLabel($status, $vehicle_number = null, $vehicle_name = null) {
    $labels = [
        'P' => '<span class="badge bg-success">Present</span>',
        'A' => '<span class="badge bg-danger">Absent</span>',
        'L' => '<span class="badge bg-info">Leave</span>',
        'J' => '<span class="badge bg-primary">Joining</span>',
        'R' => '<span class="badge bg-warning">Rest</span>'
    ];
    
    $status_label = $labels[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
    
    if ($vehicle_number && ($status == 'P' || $status == 'J')) {
        $vehicle_info = "<div class='small text-muted mt-1'>$vehicle_name ($vehicle_number)</div>";
        return $status_label . $vehicle_info;
    }
    
    return $status_label;
}

// Rest of your HTML remains the same until the calendar view section
?>

<!DOCTYPE html>
<!-- Previous HTML code remains the same until the calendar view section -->

<!-- Update the calendar view section to include vehicle information -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Monthly Attendance Calendar</h5>
    </div>
    <div class="card-body">
        <div class="attendance-calendar">
            <?php
            $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            foreach ($days as $day) {
                echo "<div class='calendar-header'>$day</div>";
            }

            $first_day = date('w', strtotime("$current_year-$current_month-01"));
            
            // Add empty cells for days before the 1st
            for ($i = 0; $i < $first_day; $i++) {
                echo "<div class='calendar-day'></div>";
            }

            // Add days of the month
            for ($day = 1; $day <= $days_in_month; $day++) {
                $date_key = sprintf('%02d', $day);
                $attendance_record = null;
                
                // Find attendance record for this day
                if ($attendance_data) {
                    foreach ($attendance_data as $record) {
                        if (date('d', strtotime($record['date'])) == $date_key) {
                            $attendance_record = $record;
                            break;
                        }
                    }
                }
                
                $status = $attendance_record ? getStatusLabel(
                    $attendance_record['status'],
                    $attendance_record['vehicle_number'],
                    $attendance_record['vehicle_name']
                ) : '';
                
                echo "<div class='calendar-day'>
                    <div class='fw-bold'>$day</div>
                    <div>$status</div>
                </div>";
            }
            ?>
        </div>
    </div>
</div>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employee Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
            text-align: center;
        }
        .attendance-calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            margin-top: 20px;
        }
        .calendar-day {
            padding: 10px;
            border: 1px solid #dee2e6;
            text-align: center;
            background-color: #f8f9fa;
        }
        .calendar-header {
            font-weight: bold;
            background-color: #e9ecef;
        }
        .employee-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .stats-box {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
        }
        .stats-number {
            font-size: 1.5em;
            font-weight: bold;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <?php include 'layout/header.php'; ?>

    <div class="custom-container">
        <div class="header">
            <h2>View Employee Attendance</h2>
            <div class="text-muted"><?php echo $month_name; ?></div>
        </div>

        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-8">
                        <label for="employee_name" class="form-label">Employee Name</label>
                        <input type="text" class="form-control" id="employee_name" name="employee_name" 
                               value="<?php echo isset($_POST['employee_name']) ? htmlspecialchars($_POST['employee_name']) : ''; ?>" 
                               required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($employee_data && count($employee_data) > 0): ?>
            <!-- Employee Selection -->
            <?php if (count($employee_data) > 1 && !isset($_POST['employee_id'])): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Select Employee</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="employee_name" value="<?php echo htmlspecialchars($_POST['employee_name']); ?>">
                            <div class="list-group">
                                <?php foreach ($employee_data as $employee): ?>
                                    <button type="submit" name="employee_id" value="<?php echo $employee['id']; ?>" 
                                            class="list-group-item list-group-item-action">
                                        <?php echo htmlspecialchars($employee['name']); ?> - 
                                        <?php echo htmlspecialchars($employee['post']); ?> 
                                        (NEIS: <?php echo htmlspecialchars($employee['neis']); ?>)
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_POST['employee_id'])): ?>
                <?php 
                $selected_employee = array_filter($employee_data, function($emp) {
                    return $emp['id'] == $_POST['employee_id'];
                });
                $selected_employee = reset($selected_employee);
                
                // Calculate attendance statistics
                $total_present = 0;
                $total_absent = 0;
                $total_leave = 0;
                $attendance_map = [];
                
                if ($attendance_data) {
                    foreach ($attendance_data as $record) {
                        $attendance_map[date('j', strtotime($record['date']))] = $record['status'];
                        switch ($record['status']) {
                            case 'P': $total_present++; break;
                            case 'A': $total_absent++; break;
                            case 'L': $total_leave++; break;
                        }
                    }
                }
                ?>

                <!-- Employee Information -->
                <div class="employee-info">
                    <h4><?php echo htmlspecialchars($selected_employee['name']); ?></h4>
                    <p class="mb-1"><strong>Father's Name:</strong> <?php echo htmlspecialchars($selected_employee['father_name']); ?></p>
                    <p class="mb-1"><strong>Post:</strong> <?php echo htmlspecialchars($selected_employee['post']); ?></p>
                    <p class="mb-0"><strong>NEIS:</strong> <?php echo htmlspecialchars($selected_employee['neis']); ?></p>
                </div>

                <!-- Attendance Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-box">
                            <div class="stats-number"><?php echo $total_present; ?></div>
                            <div>Present Days</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-box">
                            <div class="stats-number"><?php echo $total_absent; ?></div>
                            <div>Absent Days</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-box">
                            <div class="stats-number"><?php echo $total_leave; ?></div>
                            <div>Leave Days</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-box">
                            <div class="stats-number"><?php echo $days_in_month; ?></div>
                            <div>Total Days</div>
                        </div>
                    </div>
                </div>

                <!-- Calendar View -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Monthly Attendance Calendar</h5>
                    </div>
                    <div class="card-body">
                        <div class="attendance-calendar">
                            <?php
                            $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                            foreach ($days as $day) {
                                echo "<div class='calendar-header'>$day</div>";
                            }

                            $first_day = date('w', strtotime("$current_year-$current_month-01"));
                            
                            // Add empty cells for days before the 1st
                            for ($i = 0; $i < $first_day; $i++) {
                                echo "<div class='calendar-day'></div>";
                            }

                            // Add days of the month
                            for ($day = 1; $day <= $days_in_month; $day++) {
                                $status = isset($attendance_map[$day]) ? getStatusLabel($attendance_map[$day]) : '';
                                echo "<div class='calendar-day'>
                                    <div class='fw-bold'>$day</div>
                                    <div>$status</div>
                                </div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php elseif (isset($_POST['employee_name'])): ?>
            <div class="alert alert-info">No employees found matching your search.</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>