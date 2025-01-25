<?php 
require_once("config.php");
require_once 'header.php';

// Get the selected month and year (default to current)
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Get the number of days in the selected month
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Retrieve attendance records for the selected month and year
$sql = "SELECT 
    e.id,
    e.name,
    a.date,
    a.status
FROM employees e
LEFT JOIN attendance a ON e.id = a.employee_id 
    AND MONTH(a.date) = :month 
    AND YEAR(a.date) = :year
ORDER BY e.id, a.date";

$attendance_data = [];
try {
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':month', $month);
    $stmt->bindParam(':year', $year);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organize data by employee
    foreach ($results as $row) {
        if (!isset($attendance_data[$row['id']])) {
            $attendance_data[$row['id']] = [
                'name' => $row['name'],
                'dates' => []
            ];
        }
        if ($row['date']) {
            $attendance_data[$row['id']]['dates'][date('j', strtotime($row['date']))] = $row['status'];
        }
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    $attendance_data = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Attendance</title>
    <style>
        table {
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 1px;
            text-align: center;
        }
        .present {
            color: green;
        }
        .absent {
            color: red;
        }
        .rest {
            color: black;
        }
        .joining {
            color: brown;
        }
        .Leave {
            color: blue;
        }
        .sunday {
            background-color: yellow; /* Set background color for Sunday cells */
        }

        /* Make the P, J, R, and L letters bold */
        .present, .absent, .joining, .rest, .Leave {
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Attendance for month <?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?></h2>

<table>
    <thead>
        <tr>
            <th>Sl.No.</th>
            <th>Name</th>
            <?php for ($day = 1; $day <= $days_in_month; $day++): ?>
                <?php
                $date = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
                $is_sunday = date('w', strtotime($date)) == 0;
                ?>
                <th class="<?php echo $is_sunday ? 'sunday' : ''; ?>">
                    <?php echo $day; ?><br>
                    <?php echo date('D', strtotime($date)); ?>
                </th>
            <?php endfor; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($attendance_data as $employee_id => $employee): ?>
            <tr>
                <td><?php echo $employee_id; ?></td>
                <td><?php echo htmlspecialchars($employee['name']); ?></td>
                <?php for ($day = 1; $day <= $days_in_month; $day++): ?>
                    <?php
                    $status = isset($employee['dates'][$day]) ? $employee['dates'][$day] : '-';
                    $status_class = '';
                    switch ($status) {
                        case 'P':
                            $status_class = 'present';
                            break;
                        case 'A':
                            $status_class = 'absent';
                            break;
                        case 'L':
                            $status_class = 'Leave';
                            break;
                        case 'R':
                            $status_class = 'rest';
                            break;
                        case 'J':
                            $status_class = 'joining';
                            break;
                        default:
                            $status_class = '';
                    }
                    $is_sunday = date('w', strtotime(date('Y-m-d', mktime(0, 0, 0, $month, $day, $year)))) == 0;
                    ?>
                    <td class="<?php echo $status_class; ?> <?php echo $is_sunday ? 'sunday' : ''; ?>">
                        <?php echo $status; ?>
                    </td>
                <?php endfor; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="index.php"><button type="button">Back</button></a>

</body>
</html>

<?php require_once 'layout/footer.php'; ?>
