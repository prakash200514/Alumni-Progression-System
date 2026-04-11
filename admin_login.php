<?php
session_start();
require_once 'db_connect.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username === '' || $password === '') {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password_hash, role, department FROM staff WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $row['username'];
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_role'] = $row['role'];
                $_SESSION['admin_dept'] = $row['department'];
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | St.John's College</title>
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
            </div>
        </div>
    </nav>

    <div class="form-page-container">
        <div class="login-grid">
            <div class="login-left">
                <div class="login-left-content">
                    <h2>Admin Portal</h2>
                    <p class="mt-3">Login to view student update progressions and manage verified alumni records.</p>
                    <div style="margin-top: 40px; text-align: center;">
                        <span style="font-size: 4rem;">🛡️</span>
                    </div>
                </div>
            </div>

            <div class="login-right">
                <div class="login-header">
                    <h2>Admin Login</h2>
                    <p class="text-light">Enter your admin credentials</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error" style="background: #ef4444; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" autocomplete="off">
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required
                            placeholder="Enter Admin Username" autocomplete="off" value="admin">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required
                            placeholder="Enter Password" value="admin123">
                        <small style="color: #666; display: block; margin-top: 5px;">(Default password is admin123)</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mt-3">Login</button>
                </form>
            </div>
        </div>
    </div>
    <script src="js/main.js"></script>
</body>
</html>
