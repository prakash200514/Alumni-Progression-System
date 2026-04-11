<?php
require_once 'db_connect.php';

echo "Field | Type | Null | Key | Default | Extra\n";
$result = $conn->query("DESCRIBE students");

while($row = $result->fetch_assoc()) {
    echo $row["Field"] . " | " . $row["Type"] . " | " . $row["Null"] . " | " . $row["Key"] . " | " . $row["Default"] . " | " . $row["Extra"] . "\n";
}
?>
