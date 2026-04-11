<?php
require_once 'db_connect.php';

$sql1 = "ALTER TABLE staff ADD COLUMN role ENUM('admin', 'staff') DEFAULT 'staff'";
$conn->query($sql1);

$sql2 = "ALTER TABLE staff ADD COLUMN department VARCHAR(100) DEFAULT NULL";
$conn->query($sql2);

$sql3 = "UPDATE staff SET role = 'admin' WHERE username = 'admin'";
$conn->query($sql3);

echo "Staff table updated with role and department successfully.";
$conn->close();
?>
