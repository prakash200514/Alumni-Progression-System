<?php
require_once 'db_connect.php';
$res = $conn->query("SHOW INDEXES FROM students");
while ($row = $res->fetch_assoc()) {
    echo $row['Key_name'] . " - " . $row['Column_name'] . " - Non_unique: " . $row['Non_unique'] . "\n";
}
?>
