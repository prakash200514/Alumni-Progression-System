<?php
require_once 'db_connect.php';

$reg_no = '23081271802111025';
$new_pass = '123456';
$new_hash = password_hash($new_pass, PASSWORD_DEFAULT);

echo "<h3>Password Reset Tool</h3>";

// Check if user exists
$stmt = $conn->prepare("SELECT id FROM students WHERE register_number = ?");
$stmt->bind_param("s", $reg_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Found user with Register ID: $reg_no<br>";
    
    // Update password
    $update = $conn->prepare("UPDATE students SET password_hash = ? WHERE register_number = ?");
    $update->bind_param("ss", $new_hash, $reg_no);
    if ($update->execute()) {
        echo "<p style='color: green;'>Password has been successfully reset to: <strong>123456</strong></p>"; 
        echo "Please try logging in with <strong>Register ID: $reg_no</strong> and <strong>Password: 123456</strong>";
    } else {
         echo "<p style='color: red;'>Failed to update password: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: red;'>User with Register ID $reg_no not found.</p>";
    
    // List all users to help
    $all = $conn->query("SELECT register_number FROM students");
    echo "Available Users:<br>";
    while($row = $all->fetch_assoc()) {
        echo $row['register_number'] . "<br>";
    }
}
?>
