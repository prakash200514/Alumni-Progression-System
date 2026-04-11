<?php
require_once 'db_connect.php';

// Function to check if column exists
function columnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

$messages = [];
$table = 'students';

// Add status_type column
if (!columnExists($conn, $table, 'status_type')) {
    if ($conn->query("ALTER TABLE `$table` ADD COLUMN `status_type` VARCHAR(50) DEFAULT NULL COMMENT 'Working, Higher Studies, Self or Business, Other'")) {
        $messages[] = "Added 'status_type' column.";
    } else {
        $messages[] = "Error adding 'status_type': " . $conn->error;
    }
} else {
    $messages[] = "'status_type' column already exists.";
}

// Add company_name column
if (!columnExists($conn, $table, 'company_name')) {
    if ($conn->query("ALTER TABLE `$table` ADD COLUMN `company_name` VARCHAR(255) DEFAULT NULL")) {
        $messages[] = "Added 'company_name' column.";
    } else {
        $messages[] = "Error adding 'company_name': " . $conn->error;
    }
} else {
    $messages[] = "'company_name' column already exists.";
}

// Add salary column
if (!columnExists($conn, $table, 'salary')) {
    if ($conn->query("ALTER TABLE `$table` ADD COLUMN `salary` VARCHAR(100) DEFAULT NULL")) {
        $messages[] = "Added 'salary' column.";
    } else {
        $messages[] = "Error adding 'salary': " . $conn->error;
    }
} else {
    $messages[] = "'salary' column already exists.";
}

// Add college_name column
if (!columnExists($conn, $table, 'college_name')) {
    if ($conn->query("ALTER TABLE `$table` ADD COLUMN `college_name` VARCHAR(255) DEFAULT NULL")) {
        $messages[] = "Added 'college_name' column.";
    } else {
        $messages[] = "Error adding 'college_name': " . $conn->error;
    }
} else {
    $messages[] = "'college_name' column already exists.";
}

// Add studies_name column
if (!columnExists($conn, $table, 'studies_name')) {
    if ($conn->query("ALTER TABLE `$table` ADD COLUMN `studies_name` VARCHAR(255) DEFAULT NULL")) {
        $messages[] = "Added 'studies_name' column.";
    } else {
        $messages[] = "Error adding 'studies_name': " . $conn->error;
    }
} else {
    $messages[] = "'studies_name' column already exists.";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Status Fields</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
    </style>
</head>
<body>
    <h2>Database Update Results</h2>
    <?php foreach ($messages as $msg): ?>
        <p class="<?php echo strpos($msg, 'Error') !== false ? 'error' : (strpos($msg, 'already exists') !== false ? 'info' : 'success'); ?>">
            <?php echo htmlspecialchars($msg); ?>
        </p>
    <?php endforeach; ?>
    <p><a href="student_dashboard.php">Go to Dashboard</a></p>
</body>
</html>
