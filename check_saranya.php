<?php
require_once 'db_connect.php';
$res = $conn->query("SELECT id, email, register_number FROM students WHERE email='saranya28@gmail.com'");
while($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " - Reg: " . $row['register_number'] . " - Email: " . $row['email'] . "\n";
}
?>
