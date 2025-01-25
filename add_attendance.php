<?php
require_once 'config.php';
require_once 'header.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';
$attendance_records = [];



// Fetch employees for dropdown
$query = "SELECT id, name FROM employees ORDER BY name";
$stmt = $db->prepare($query);
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['employee_id']) || empty($_POST['date']) || empty($_POST['status'])) {
            throw new Exception('All fields are required');
        }

        // Check if attendance already exists for this employee and date
        $check_query = "SELECT id FROM attendance WHERE employee_id = :employee_id AND date = :date";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':employee_id', $_POST['employee_id']);
        $check_stmt->bindParam(':date', $_POST['date']);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            // Update existing attendance
            $query = "UPDATE attendance SET status = :status WHERE employee_id = :employee_id AND date = :date";
        } else {
            // Insert new attendance
            $query = "INSERT INTO attendance (employee_id, date, status) VALUES (:employee_id, :date, :status)";
        }

        $stmt = $db->prepare($query);
        $stmt->bindParam(':employee_id', $_POST['employee_id']);
        $stmt->bindParam(':date', $_POST['date']);
        $stmt->bindParam(':status', $_POST['status']);

        if ($stmt->execute()) {
            $message = 'Attendance recorded successfully!';
        } else {
            throw new Exception('Failed to record attendance');
        }

        // Fetch the last 7 attendance records for the selected employee after insertion or update
        $attendance_query = "SELECT * FROM attendance WHERE employee_id = :employee_id ORDER BY date DESC LIMIT 7";
        $attendance_stmt = $db->prepare($attendance_query);
        $attendance_stmt->bindParam(':employee_id', $_POST['employee_id']);
        $attendance_stmt->execute();
        $attendance_records = $attendance_stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

?>

<main class="container mx-auto mt-8 px-4">
    <h1 class="text-2xl font-bold mb-4">Add Attendance</h1>

    <?php if ($message): ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p class="ml-3 text-sm text-green-700"><?php echo $message; ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <p class="ml-3 text-sm text-red-700"><?php echo $error; ?></p>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6 max-w-md mx-auto p-6 bg-white rounded-lg shadow">
        <div class="space-y-4">

 <div>
                <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                <input
                    type="date"
                    id="date"
                    name="date"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    value="<?php echo date('Y-m-d'); ?>"
                >
            </div>


            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee</label>
                <select
                    id="employee_id"
                    name="employee_id"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                >
                    <option value="">Select employee</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?php echo $employee['id']; ?>" <?php echo (isset($_POST['employee_id']) && $_POST['employee_id'] == $employee['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($employee['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

                    <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select
                    id="status"
                    name="status"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                >
                    <option value="">Select status</option>
                    <option value="P">Present</option>
                    <option value="A">Absent</option>
                    <option value="L">Leave</option>
                    <option value="J">Joining</option>
                    <option value="R">Rest</option>
                </select>
            </div>
        </div>

        <button
            type="submit"
            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
            Save Attendance
        </button>
    </form>

    <!-- Display last 7 attendance records -->
    <?php if (count($attendance_records) > 0): ?>
        <h2 class="text-xl font-bold mt-8 mb-4">Last 7 Attendance Records</h2>
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="px-4 py-2 border">Date</th>
                    <th class="px-4 py-2 border">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_records as $attendance): ?>
                    <tr>
                        <td class="px-4 py-2 border"><?php echo $attendance['date']; ?></td>
                        <td class="px-4 py-2 border"><?php echo $attendance['status']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No attendance records found for this employee.</p>
    <?php endif; ?>
</main>

<?php require_once 'layout/footer.php'; ?>
