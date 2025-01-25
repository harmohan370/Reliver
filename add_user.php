<?php
session_start();
require_once 'config.php'; // Include the database configuration
require_once 'header.php';

// Create a Database instance and get the connection
$database = new Database();
$db = $database->getConnection();

// Add user functionality
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user into the database
    $query = "INSERT INTO users (username, password) VALUES (:username, :password)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

    try {
        if ($stmt->execute()) {
            $success_message = "User added successfully!";
        } else {
            $error_message = "Error adding user.";
        }
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}

// Fetch all users for displaying in the table
$query = "SELECT * FROM users";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
</head>
<body>
    <h2>Add New User</h2>

    <?php if (isset($success_message)) { echo "<p style='color:green;'>$success_message</p>"; } ?>
    <?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>

    <form action="add_user.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit" name="submit">Add User</button>
    </form>

    <h3>Users List</h3>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
