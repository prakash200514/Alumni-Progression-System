<?php
require_once 'db_connect.php';
$res = $conn->query("SELECT id FROM students WHERE register_number='2038'");
$row = $res->fetch_assoc();
if (!$row) {
    die("User 2038 not found");
}
$id = $row['id'];
echo "User ID: $id\n";

$sql = "UPDATE students SET email='saranya28@gmail.com' WHERE id=$id";
if ($conn->query($sql)) {
    echo "Update successful!";
} else {
    echo "Error: " . $conn->error;
}
?>
