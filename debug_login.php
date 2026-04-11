<?php
session_start();
require_once 'db_connect.php';

$debug_output = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $raw_input_password = $_POST['password'];
    $trimmed_password = trim($_POST['password']);
    $register_id = trim($_POST['register_id']);
    
    $debug_output .= "Input Register ID: " . htmlspecialchars($register_id) . "<br>";
    $debug_output .= "Raw Password Length: " . strlen($raw_input_password) . "<br>";
    $debug_output .= "Trimmed Password Length: " . strlen($trimmed_password) . "<br>";
    
    // Check DB
    $stmt = $conn->prepare("SELECT id, name, password_hash, status FROM students WHERE register_number = ?");
    $stmt->bind_param("s", $register_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $debug_output .= "User Found in DB.<br>";
        $debug_output .= "Stored Hash: " . htmlspecialchars($row['password_hash']) . "<br>";
        
        if (password_verify($trimmed_password, $row['password_hash'])) {
            $debug_output .= "<strong style='color:green'>LOGIN SUCCESS! Password matches.</strong><br>";
        } else {
            $debug_output .= "<strong style='color:red'>LOGIN FAILED! Password mismatch.</strong><br>";
            // Creating a new hash of input to show comparison (salt will differ but length should resemble)
            $new_hash = password_hash($trimmed_password, PASSWORD_DEFAULT);
            $debug_output .= "New Hash of Input: " . $new_hash . "<br>";
        }
    } else {
        $debug_output .= "<strong style='color:red'>User NOT FOUND in DB with this Register ID.</strong><br>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Login</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .debug-box { background: #f0f0f0; border: 1px solid #ccc; padding: 15px; margin-top: 20px; word-break: break-all; }
    </style>
</head>
<body>

<h2>Debug Login Tool</h2>
<p>Use this page to check why login is failing. It will show you exactly what the system sees.</p>

<form method="POST">
    <div style="margin-bottom: 10px;">
        <label>Register ID:</label><br>
        <input type="text" name="register_id" required value="<?php echo isset($_POST['register_id']) ? htmlspecialchars($_POST['register_id']) : ''; ?>">
    </div>
    <div style="margin-bottom: 10px;">
        <label>Password:</label><br>
        <input type="text" name="password" required> (Visible for debugging)
    </div>
    <button type="submit">Test Login</button>
</form>

<?php if ($debug_output): ?>
    <div class="debug-box">
        <h3>Debug Results:</h3>
        <?php echo $debug_output; ?>
    </div>
<?php endif; ?>

</body>
</html>
