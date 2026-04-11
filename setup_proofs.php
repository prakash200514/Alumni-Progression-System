<?php
require_once 'db_connect.php';

// Drop existing if any, to match the exact schema requested by the user
$conn->query("DROP TABLE IF EXISTS student_proofs");

$sql = "CREATE TABLE student_proofs (
    proof_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    id_photo VARCHAR(255),
    signature VARCHAR(255),
    working_proof VARCHAR(255),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'latest'
)";

if ($conn->query($sql) === TRUE) {
    echo "Table student_proofs created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}
$conn->close();
?>
