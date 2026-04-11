<?php
require_once 'db_connect.php';

$sql = "CREATE TABLE IF NOT EXISTS student_proofs (
    proof_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    company_name VARCHAR(255),
    salary VARCHAR(100),
    proof_drive_link TEXT,
    signature_drive_link TEXT,
    profile_photo VARCHAR(255),
    address TEXT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_current TINYINT(1) DEFAULT 1,
    FOREIGN KEY (student_id) REFERENCES students(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table student_proofs created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}
$conn->close();
?>
