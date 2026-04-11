<?php
require_once 'db_connect.php';

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Exact column mapping from original file
    $register_number = trim($_POST['register_id'] ?? '');
    $name = trim($_POST['full_name'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $batch_year = trim($_POST['batch'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $address = null;
    $current_job = null;
    $working_proof = null;

    // Basic Validation
    if (empty($register_number) || empty($name) || empty($department) || empty($batch_year) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($password !== $confirm_password) {
        $error = "Password and Confirm Password do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if already exists
        $check_stmt = $conn->prepare("SELECT id FROM students WHERE register_number = ? OR email = ?");
        $check_stmt->bind_param("ss", $register_number, $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "A student with this Register Number or Email already exists.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $status = 'Pending';
            // Collect drive links
            // Verification documents removed from registration
            $id_photo = '';

            $signature = '';

            if (!$error) {
                $insert_stmt = $conn->prepare("INSERT INTO students (register_number, name, department, batch_year, email, phone, current_job, address, id_photo, working_proof, signature, password_hash, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $insert_stmt->bind_param("sssssssssssss", $register_number, $name, $department, $batch_year, $email, $phone, $current_job, $address, $id_photo, $working_proof, $signature, $password_hash, $status);

                if ($insert_stmt->execute()) {
                     header("Location: student_login.php?status=registered");
                     exit();
                } else {
                    $error = "Error: " . $insert_stmt->error;
                }
                $insert_stmt->close();
            }
        }
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Registration | St.John's College</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container navbar-container">
            <div class="logo-container">
                <a href="index.html" class="college-logo-placeholder" style="text-decoration: none;">SJC</a>
                <div class="college-name">
                    <h1>St.John's College, Palayamkottai</h1>
                    <span>Ex-Student Data Collection Portal</span>
                </div>
            </div>
            <div class="nav-links">
                <a href="index.html">Home</a>
            </div>
        </div>
    </nav>

    <!-- Registration Form Container -->
    <div class="container">
        <div class="register-container">
            <div class="text-center mb-4">
                <h2>🎓 Alumni Registration</h2>
                <p class="text-light">Join our official verified alumni network</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success" style="background: #10b981; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
                    <h3>🎉 <?php echo $message; ?></h3>
                    <p>Thank you for registering. You can now close this page.</p>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error" style="background: #ef4444; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="student_register.php" method="POST" enctype="multipart/form-data" novalidate>

                <!-- Section A: Personal Details -->
                <div class="form-section">
                    <div class="section-title">
                        <span>1</span> Personal Details
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="full_name" required
                                placeholder="As per college records" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="registerId" class="form-label">Register ID / Roll No</label>
                            <input type="text" class="form-control" id="registerId" name="register_id" required
                                placeholder="Ex: 2020ABC001" value="<?php echo isset($_POST['register_id']) ? htmlspecialchars($_POST['register_id']) : ''; ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="department" class="form-label">Department</label>
                            <select class="form-control" id="department" name="department" required>
                                <option value="" disabled selected>Select Department</option>
                                <option value="CSE" <?php echo (isset($_POST['department']) && $_POST['department'] == 'CSE') ? 'selected' : ''; ?>>Computer Science</option>
                                <option value="IT" <?php echo (isset($_POST['department']) && $_POST['department'] == 'IT') ? 'selected' : ''; ?>>Information Technology</option>
                                <option value="ECE" <?php echo (isset($_POST['department']) && $_POST['department'] == 'ECE') ? 'selected' : ''; ?>>Electronics & Comm.</option>
                                <option value="MECH" <?php echo (isset($_POST['department']) && $_POST['department'] == 'MECH') ? 'selected' : ''; ?>>Mechanical Engg.</option>
                                <option value="CIVIL" <?php echo (isset($_POST['department']) && $_POST['department'] == 'CIVIL') ? 'selected' : ''; ?>>Civil Engg.</option>
                                <option value="ARTS" <?php echo (isset($_POST['department']) && $_POST['department'] == 'ARTS') ? 'selected' : ''; ?>>Arts & Humanities</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="batch" class="form-label">Batch (Year to Year)</label>
                            <input type="text" class="form-control" id="batch" name="batch"
                                required placeholder="Ex: 2021-2025" value="<?php echo isset($_POST['batch']) ? htmlspecialchars($_POST['batch']) : ''; ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email" class="form-label">Email ID</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                placeholder="Ex: you@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone" class="form-label">Mobile Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" required
                                placeholder="Ex: 9876543210" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>
                    </div>
                </div>

                <!-- Section B: Login Credentials -->
                <div class="form-section">
                    <div class="section-title">
                        <span>2</span> Login Credentials
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required
                                placeholder="Create a password (min 6 characters)">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required
                                placeholder="Re-enter your password">
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary btn-lg">Submit Registration</button>
                </div>

            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer style="margin-top: 0;">
        <div class="container text-center">
            <p>&copy; 2026 St.John's College, Palayamkottai</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
    <script src="js/main.js"></script>

</body>

</html>
