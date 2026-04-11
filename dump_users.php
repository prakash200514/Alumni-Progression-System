<?php
require_once 'db_connect.php';
$result = $conn->query("SELECT id, register_number, password_hash FROM students");
echo "USERS DUMP:\n";
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . "\n";
    echo "RegNo: " . $row['register_number'] . "\n";
    echo "Hash: " . substr($row['password_hash'], 0, 15) . "...\n";
    echo "------------------\n";
}
?>
