<?php
session_start();
require_once 'db_connect.php';

$error = '';

function normalizeName($name) {
    $name = trim($name);
    $name = preg_replace('/\s+/', ' ', $name);
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($name, 'UTF-8');
    }
    return strtolower($name);
}

function getTableColumns($conn, $tableName) {
    $columns = [];
    $result = $conn->query("SHOW COLUMNS FROM `$tableName`");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $columns[$row['Field']] = $row; // includes Type, Null, Default, etc.
        }
        $result->free();
    }
    return $columns;
}

function bindParamsDynamic($stmt, $types, &$values) {
    $bind = [];
    $bind[] = $types;
    foreach ($values as $i => $val) {
        $bind[] = &$values[$i];
    }
    return call_user_func_array([$stmt, 'bind_param'], $bind);
}

// Check for login submission - Register Number + Name only (auto-create if not found)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_id']) && isset($_POST['full_name'])) {
    $register_number = trim($_POST['register_id'] ?? '');
    $name = trim($_POST['full_name'] ?? '');

    if ($register_number === '' || $name === '') {
        $error = "Please enter both Register Number and Full Name.";
    } else {
        // 1) Try to find by register number
        $stmt = $conn->prepare("SELECT id, name FROM students WHERE register_number = ? LIMIT 1");
        $stmt->bind_param("s", $register_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Allow login based on register number without strict name matching
            $_SESSION['student_id'] = (int)$row['id'];
            $_SESSION['student_name'] = $row['name'];
            header("Location: student_dashboard.php");
            exit();
        } else {
            // 2) Not found -> auto-create a minimal student profile and login
            $cols = getTableColumns($conn, 'students');

            $cleanReg = preg_replace('/[^A-Za-z0-9]/', '', $register_number);
            if ($cleanReg === '') {
                $cleanReg = 'USER' . time();
            }

            $placeholders = [
                'department' => 'UNKNOWN',
                'batch_year' => 0,
                'email' => $cleanReg . '@example.invalid',
                'phone' => '',
                'current_job' => null,
                'address' => null,
                'photo' => null,
                'id_photo' => null,
                'working_proof' => null,
                'signature' => null,
                'password_hash' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
                'status' => 'Pending',
            ];

            $insertFields = [];
            $insertValues = [];
            $types = '';

            // Always include these
            if (!isset($cols['register_number']) || !isset($cols['name'])) {
                $error = "Database table is missing required columns (register_number/name).";
            } else {
                $insertFields[] = 'register_number';
                $insertValues[] = $register_number;
                $types .= 's';

                $insertFields[] = 'name';
                $insertValues[] = $name;
                $types .= 's';

                // Add optional fields only if they exist in the actual table
                foreach ($placeholders as $field => $value) {
                    if (!isset($cols[$field])) continue;

                    $colType = strtolower($cols[$field]['Type'] ?? '');
                    if (is_int($value) || (strpos($colType, 'int') !== false && $field === 'batch_year')) {
                        $insertFields[] = $field;
                        $insertValues[] = (int)$value;
                        $types .= 'i';
                    } else {
                        $insertFields[] = $field;
                        $insertValues[] = $value;
                        $types .= 's';
                    }
                }

                // If phone/email are required but empty in placeholders, set safe defaults
                if (isset($cols['phone'])) {
                    $phoneIndex = array_search('phone', $insertFields, true);
                    if ($phoneIndex !== false && ($insertValues[$phoneIndex] === '' || $insertValues[$phoneIndex] === null)) {
                        $insertValues[$phoneIndex] = '0000000000';
                    }
                }

                if (isset($cols['email'])) {
                    $emailIndex = array_search('email', $insertFields, true);
                    if ($emailIndex !== false && ($insertValues[$emailIndex] === '' || $insertValues[$emailIndex] === null)) {
                        $insertValues[$emailIndex] = $cleanReg . '@example.invalid';
                    }
                }

                $fieldsSql = implode(', ', array_map(fn($f) => "`$f`", $insertFields));
                $paramsSql = implode(', ', array_fill(0, count($insertFields), '?'));
                $sql = "INSERT INTO students ($fieldsSql) VALUES ($paramsSql)";

                $insertStmt = $conn->prepare($sql);
                if (!$insertStmt) {
                    $error = "Could not create new profile. Please try again. (" . $conn->error . ")";
                } else {
                    bindParamsDynamic($insertStmt, $types, $insertValues);
                    if ($insertStmt->execute()) {
                        $newId = (int)$insertStmt->insert_id;
                        $_SESSION['student_id'] = $newId;
                        $_SESSION['student_name'] = $name;
                        header("Location: student_dashboard.php");
                        exit();
                    } else {
                        $error = "Could not create new profile. Please try again. (" . $insertStmt->error . ")";
                    }
                    $insertStmt->close();
                }
            }
        }

        if ($stmt) $stmt->close();
    }
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
                    <p class="text-light">Enter your Register Number and Name to access your dashboard</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error" style="background: #ef4444; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" autocomplete="off">
                    <div class="form-group">
                        <label for="registerId" class="form-label">Register Number</label>
                        <input type="text" class="form-control" id="registerId" name="register_id" required
                            placeholder="Enter your Register Number" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" name="full_name" required
                            placeholder="Enter your name as per college records">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mt-3">Login</button>
                </form>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>

</html>
