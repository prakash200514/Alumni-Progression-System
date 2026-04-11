<?php
require_once 'db_connect.php';

// Drop the unique index on email if it exists
$sql = "ALTER TABLE students DROP INDEX email";
if ($conn->query($sql)) {
    echo "Successfully removed the unique constraint on the email column. You can now save your profile!";
} else {
    // If the index might be named differently or doesn't exist
    echo "Result: " . $conn->error;
}
?>
