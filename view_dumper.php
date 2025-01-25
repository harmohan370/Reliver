<?php
require_once 'config.php'; // Include database connection configuration
require_once 'header.php';

// Establish database connection
$database = new Database();
$db = $database->getConnection();

// Initialize variables
$message = '';
$error = '';
$vehicles = []; // Updated variable name

try {
    // Fetch vehicles with status 'Ready'
    $query = "SELECT id, vehicle_number, name, status FROM vehicles WHERE status = 'Ready' ORDER BY name ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();

    // Fetch the results
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($vehicles)) {
        $message = "No vehicles are currently ready.";
    }
} catch (Exception $e) {
    $error = "Failed to fetch vehicles: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ready Vehicles</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional: Link to a CSS file for styling -->
</head>
<body>
    <div class="container">
        <h1 class="title">Ready Dumper</h1>

        <?php if ($message): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($vehicles)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Dumper No.</th>
                        <th>Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($vehicle['vehicle_number']); ?></td>
                            <td><?php echo htmlspecialchars($vehicle['name']); ?></td>
                            <td><?php echo htmlspecialchars($vehicle['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
