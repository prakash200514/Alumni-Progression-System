<?php
require_once 'db_connect.php';

$sql = "DELETE FROM students";
if ($conn->query($sql) === TRUE) {
    echo "All users deleted successfully. You can now register as a new user.";
} else {
    echo "Error deleting record: " . $conn->error;
}
?>
