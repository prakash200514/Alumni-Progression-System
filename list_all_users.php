<?php
require_once 'db_connect.php';
$sql = "SELECT id, register_number, name, email, password_hash FROM students";
$result = $conn->query($sql);

echo "<h3>Registered Users</h3>";
if ($result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Register Number</th><th>Name</th><th>Email</th><th>Pass Hash Hash</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . htmlspecialchars($row["register_number"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["email"] ? $row["email"] : 'NULL') . "</td>";
        echo "<td>" . substr($row["password_hash"], 0, 10) . "...</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No registered users found.";
}
?>
