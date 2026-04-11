<?php
require_once 'db_connect.php';

$table = 'students';
$result = $conn->query("DESCRIBE $table");

$output = "Columns in '$table' table:\n";
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $output .= $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    $output .= "Error: " . $conn->error;
}
file_put_contents('db_schema.txt', $output);
echo "Done.";
$conn->close();
?>
