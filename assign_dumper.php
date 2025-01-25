<?php
require_once 'config.php';
require_once 'header.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';

// Get present employees for today
$query = "SELECT e.id, e.name 
          FROM employees e 
          INNER JOIN attendance a ON e.id = a.employee_id 
          WHERE a.date = CURDATE() AND a.status = 'P'
          ORDER BY e.name";
$stmt = $db->prepare($query);
$stmt->execute();
$present_employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get available vehicles (not allocated today)
$query = "SELECT v.* 
          FROM vehicles v 
          LEFT JOIN vehicle_allocations va ON v.id = va.vehicle_id 
          AND va.allocation_date = CURDATE() 
          WHERE va.id IS NULL
          ORDER BY v.name";
$stmt = $db->prepare($query);
$stmt->execute();
$available_vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['employee_id']) || empty($_POST['vehicle_id'])) {
            throw new Exception('All fields are required');
        }

        // Check if vehicle is already allocated for today
        $check_query = "SELECT id FROM vehicle_allocations 
                       WHERE vehicle_id = :vehicle_id AND allocation_date = CURDATE()";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':vehicle_id', $_POST['vehicle_id']);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            throw new Exception('Vehicle is already allocated for today');
        }

        $query = "INSERT INTO vehicle_allocations (employee_id, vehicle_id, allocation_date) 
                  VALUES (:employee_id, :vehicle_id, CURDATE())";
        $stmt = $db->prepare($query);

        $stmt->bindParam(':employee_id', $_POST['employee_id']);
        $stmt->bindParam(':vehicle_id', $_POST['vehicle_id']);

        if ($stmt->execute()) {
            $message = 'Vehicle allocated successfully!';
        } else {
            throw new Exception('Failed to allocate vehicle');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<main class="container mx-auto mt-8 px-4">
    <h1 class="text-2xl font-bold mb-4">Allocate Vehicle</h1>

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

    <?php if (empty($present_employees)): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <p class="ml-3 text-sm text-yellow-700">No employees are present today.</p>
            </div>
        </div>
    <?php elseif (empty($available_vehicles)): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <p class="ml-3 text-sm text-yellow-700">No vehicles are available for allocation.</p>
            </div>
        </div>
    <?php else: ?>
        <form method="POST" class="space-y-6 max-w-md mx-auto p-6 bg-white rounded-lg shadow">
            <div class="space-y-4">
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee</label>
                    <select
                        id="employee_id"
                        name="employee_id"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    >
                        <option value="">Select employee</option>
                        <?php foreach ($present_employees as $employee): ?>
                            <option value="<?php echo $employee['id']; ?>">
                                <?php echo htmlspecialchars($employee['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Vehicle</label>
                    <select
                        id="vehicle_id"
                        name="vehicle_id"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    >
                        <option value="">Select vehicle</option>
                        <?php foreach ($available_vehicles as $vehicle): ?>
                            <option value="<?php echo $vehicle['id']; ?>">
                                <?php echo htmlspecialchars($vehicle['name'] . ' (' . $vehicle['vehicle_number'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <button
                type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Allocate Vehicle
            </button>
        </form>
    <?php endif; ?>
</main>

<?php require_once 'layout/footer.php'; ?>