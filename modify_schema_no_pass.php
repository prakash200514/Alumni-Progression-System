<?php
require_once 'db_connect.php';

$sql = "ALTER TABLE students MODIFY COLUMN password_hash VARCHAR(255) NULL DEFAULT NULL";
if ($conn->query($sql) === TRUE) {
    echo "Password column successfully modified to allow NULL values.";
} else {
    echo "Error updating table: " . $conn->error;
}
?>
