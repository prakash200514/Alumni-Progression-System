<?php
require_once 'db_connect.php';

$sql = "CREATE TABLE IF NOT EXISTS staff (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'staff' created successfully.\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}

// Insert default admin
$adminUser = 'admin';
$adminPass = password_hash('admin123', PASSWORD_DEFAULT);

$check = $conn->query("SELECT id FROM staff WHERE username = '$adminUser'");
if ($check->num_rows == 0) {
    $insert = $conn->query("INSERT INTO staff (username, password_hash) VALUES ('$adminUser', '$adminPass')");
    if ($insert) {
        echo "Default admin user inserted successfully.\n";
    } else {
        echo "Error inserting admin user: " . $conn->error . "\n";
    }
} else {
    echo "Admin user already exists.\n";
}

$conn->close();
?>
