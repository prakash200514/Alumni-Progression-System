<?php
require_once 'db_connect.php';

echo "<h3>Testing Registration and Login Flow...</h3>";

$reg_no = 'TestUser123';
$password = 'TestPass';

// Step 1: Cleanup
$conn->query("DELETE FROM students WHERE register_number = '$reg_no'");

// Step 2: Register
echo "Attempting to register user: $reg_no with pass: $password<br>";
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO students (register_number, name, department, batch_year, password_hash) VALUES (?, 'Test Name', 'CSE', '2024', ?)");
$stmt->bind_param("ss", $reg_no, $hash);
if ($stmt->execute()) {
    echo "<p style='color: green;'>Registration Successful!</p>";
} else {
    echo "<p style='color: red;'>Registration Failed: " . $stmt->error . "</p>";
    exit();
}

// Step 3: Verify Login
echo "Attempting to login...<br>";
$stmt = $conn->prepare("SELECT password_hash FROM students WHERE register_number = ?");
$stmt->bind_param("s", $reg_no);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $db_hash = $row['password_hash'];
    
    if (password_verify($password, $db_hash)) {
         echo "<p style='color: green;'>LOGIN SUCCESSFUL! Password verify matches.</p>";
    } else {
         echo "<p style='color: red;'>LOGIN FAILED! Password verify does not match.</p>";
         echo "Debug Info: <br>";
         echo "Input Pass: '$password' <br>";
         echo "DB Hash: '$db_hash' <br>";
         echo "Hash Length: " . strlen($db_hash) . "<br>";
    }
} else {
    echo "<p style='color: red;'>User not found in Login step.</p>";
}

// Cleanup again
$conn->query("DELETE FROM students WHERE register_number = '$reg_no'");
?>
