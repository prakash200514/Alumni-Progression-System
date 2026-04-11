<?php
require_once 'db_connect.php';

// Check if student_proofs table exists
$result = $conn->query("SHOW TABLES LIKE 'student_proofs'");
if ($result && $result->num_rows > 0) {
    echo "Table 'student_proofs' exists.\n";
} else {
    echo "Table 'student_proofs' DOES NOT EXIST.\n";
    exit;
}

// Check its columns
$result = $conn->query("DESCRIBE student_proofs");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
}
echo "Verification complete.\n";
?>
