<?php
require_once 'db_connect.php';
$result = $conn->query("SELECT id, register_number, email FROM students LIMIT 20");
echo "ID | Register Number | Email\n";
while($row = $result->fetch_assoc()) {
    echo $row['id'] . " | " . $row['register_number'] . " | " . ($row['email'] ? $row['email'] : 'NULL') . "\n";
}
?>
