<?php
require_once 'db_connect.php';

// List of columns to add
$alter_statements = [
    "ALTER TABLE students ADD COLUMN current_job VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE students ADD COLUMN address TEXT DEFAULT NULL",
    "ALTER TABLE students ADD COLUMN photo VARCHAR(255) DEFAULT NULL"
];

echo "<h3>Updating Database Schema...</h3>";

foreach ($alter_statements as $sql) {
    try {
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color: green;'>Successfully executed: $sql</p>";
        } else {
            // Check if error is because column already exists (Error 1060)
            if ($conn->errno == 1060) {
                 echo "<p style='color: orange;'>Column already exists: $sql</p>";
            } else {
                 echo "<p style='color: red;'>Error executing: $sql - " . $conn->error . "</p>";
            }
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Exception: " . $e->getMessage() . "</p>";
    }
}

echo "<h3>Update Complete.</h3>";
echo "<p><a href='student_dashboard.php'>Go back to Dashboard</a></p>";
?>
