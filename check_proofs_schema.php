<?php
require 'db_connect.php';
$table = 'student_proofs';
$res = $conn->query("SHOW TABLES LIKE '$table'");
if ($res->num_rows == 0) {
    echo "Table '$table' does not exist.\n";
} else {
    echo "Table '$table' exists. Columns:\n";
    $res = $conn->query("EXPLAIN $table");
    while($row = $res->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
}
?>
