<?php
require_once 'db_connect.php';
$result = $conn->query("SHOW COLUMNS FROM students LIKE 'password_hash'");
$row = $result->fetch_assoc();
echo "Type: " . $row['Type'];
?>
