<?php
require_once 'db_connect.php';
$res = $conn->query("SHOW CREATE TABLE students");
$row = $res->fetch_row();
echo $row[1];
?>
