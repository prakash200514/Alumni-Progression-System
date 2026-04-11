<?php
session_start();
require_once 'db_connect.php';

$error = '';

// Check for login submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_id']) && isset($_POST['password'])) {
    $register_id = trim($_POST['register_id']);
    $password = trim($_POST['password']);

    // Login with Register ID
    $stmt = $conn->prepare("SELECT id, name, password_hash, status FROM students WHERE register_number = ?");
    $stmt->bind_param("s", $register_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $password_hash, $status);
        $stmt->fetch();

        if (password_verify($password, $password_hash)) {
            $_SESSION['student_id'] = $id;
            $_SESSION['student_name'] = $name;
            header("Location: student_dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that Register ID.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login | St.John's College</title>
    <!-- Using same CSS as student_register.php -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-light">

    <!-- Navigation -->
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
                <a href="student_register.php" class="btn btn-accent"
                    style="padding: 5px 15px; font-size: 0.85rem;">Register</a>
            </div>
        </div>
    </nav>

    <div class="form-page-container">
        <div class="login-grid">
            <!-- Left Side: Illustration -->
            <div class="login-left">
                <div class="login-left-content">
                    <h2>Welcome Back!</h2>
                    <p class="mt-3">Access your alumni profile, update your career status, and stay connected with your
                        batchmates.</p>
                    <div style="margin-top: 40px; text-align: center;">
                        <span style="font-size: 4rem;">🎓</span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="login-right">
                <div class="login-header">
                    <h2>Student Login</h2>
                    <p class="text-light">Please login with your Register ID</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error" style="background: #ef4444; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" autocomplete="off">
                    <div class="form-group">
                        <label for="registerId" class="form-label">Register ID</label>
                        <input type="text" class="form-control" id="registerId" name="register_id" required
                            placeholder="Enter your Register ID" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required
                            placeholder="Enter your password">
                        <div class="mt-2 text-end">
                            <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility('password')">
                            <label for="showPassword" style="font-size: 0.9em; cursor: pointer;">Show Password</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mt-3">Login</button>

                    <div class="text-center mt-3">
                        <a href="#" class="text-light" style="font-size: 0.9rem;">Forgot Password?</a>
                    </div>

                    <hr class="mb-4 mt-4" style="border: 0; border-top: 1px solid var(--border-color);">

                    <div class="text-center">
                        <p class="mb-2">Don't have an account?</p>
                        <a href="student_register.php" class="btn btn-secondary btn-block">Create Details</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        // Check for success message from registration
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'registered') {
            alert("Registration successful! Please login with your credentials.");
        }

        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            if (field.type === "password") {
                field.type = "text";
            } else {
                field.type = "password";
            }
        }
    </script>
</body>

</html>
