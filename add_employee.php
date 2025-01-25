<?php
require_once 'config.php';
require_once 'header.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        if (empty($_POST['name']) || empty($_POST['father_name']) || empty($_POST['neis']) || empty($_POST['post'])) {
            throw new Exception('All fields are required');
        }

        // Check if NEIS already exists
        $check_query = "SELECT id FROM employees WHERE neis = :neis";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':neis', $_POST['neis']);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            throw new Exception('Employee with this NEIS already exists');
        }

        // Insert new employee
        $query = "INSERT INTO employees (name, father_name, neis, post) VALUES (:name, :father_name, :neis, :post)";
        $stmt = $db->prepare($query);

        $stmt->bindParam(':name', $_POST['name']);
        $stmt->bindParam(':father_name', $_POST['father_name']);
        $stmt->bindParam(':neis', $_POST['neis']);
        $stmt->bindParam(':post', $_POST['post']); // Bind post parameter

        if ($stmt->execute()) {
            $message = 'Employee added successfully!';
        } else {
            throw new Exception('Failed to add employee');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<main class="container mx-auto mt-8 px-4">
    <h1 class="text-2xl font-bold mb-4">Add Employee</h1>

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
                <label for="name" class="block text-sm font-medium text-gray-700">Employee Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    placeholder="Enter employee name"
                >
            </div>

            <div>
                <label for="father_name" class="block text-sm font-medium text-gray-700">Father's Name</label>
                <input
                    type="text"
                    id="father_name"
                    name="father_name"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    placeholder="Enter father's name"
                >
            </div>

            <div>
                <label for="neis" class="block text-sm font-medium text-gray-700">NEIS Number</label>
                <input
                    type="text"
                    id="neis"
                    name="neis"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    placeholder="Enter NEIS number"
                >
            </div>

            <div>
                <label for="post" class="block text-sm font-medium text-gray-700">Post</label>
                <input
                    type="text"
                    id="post"
                    name="post"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    placeholder="Enter employee post"
                >
            </div>
        </div>

        <button
            type="submit"
            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
            Add Employee
        </button>
    </form>
</main>

<?php require_once 'layout/footer.php'; ?>
