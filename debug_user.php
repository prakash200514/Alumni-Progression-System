<?php
require_once 'db_connect.php';
$reg_id = '23081271802111025';
$stmt = $conn->prepare("SELECT id, name, register_number, password_hash FROM students WHERE register_number = ?");
$stmt->bind_param("s", $reg_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Found user:\n";
    $row = $result->fetch_assoc();
    echo "ID: " . $row['id'] . "\n";
    echo "Register Number: " . $row['register_number'] . "\n";
    echo "Hash Length: " . strlen($row['password_hash']) . "\n";
    echo "Hash Start: " . substr($row['password_hash'], 0, 10) . "...\n";
} else {
    echo "User not found with register_number: $reg_id\n";
    
    // Check if it exists in email column just in case (though we know that column is likely null or not used for login)
    // But wait, the login query only checks register_number. So checking email column is irrelevant for why the query returned a row.
}
?>
