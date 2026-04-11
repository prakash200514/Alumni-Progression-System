<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

// Helper to convert Drive link to direct view link
function getDriveDirectLink($url) {
    if (empty($url)) return '';
    
    // Check if it's a drive link
    if (strpos($url, 'drive.google.com') !== false) {
        $fileId = '';
        // Try to extract ID from /d/ID/view pattern
        if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $fileId = $matches[1];
        } 
        // Try to extract ID from id=ID pattern
        elseif (preg_match('/id=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $fileId = $matches[1];
        }

        if ($fileId) {
            return "https://drive.google.com/thumbnail?id=" . $fileId . "&sz=w300";
        }
    }
    
    return $url; // Return original if not a drive link (or failed to parse)
}

function bindParamsDynamic($stmt, $types, &$values) {
    $bind = [];
    $bind[] = $types;
    foreach ($values as $i => $val) {
        $bind[] = &$values[$i];
    }
    return call_user_func_array([$stmt, 'bind_param'], $bind);
}

function columnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

$student_id = $_SESSION['student_id'];
$message = '';
// ... (rest of code)


// Handle Form Submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action == 'update_profile') {
        $metaStmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
        $metaStmt->bind_param("i", $student_id);
        $metaStmt->execute();
        $metaResult = $metaStmt->get_result();
        $meta = $metaResult ? $metaResult->fetch_assoc() : null;
        $metaStmt->close();

        $existingDept = trim($meta['department'] ?? '');
        $existingBatch = (int)($meta['batch_year'] ?? 0);
        $status = $meta['status'] ?? '';
        $needsAcademicUpdate = ($existingDept === '' || strcasecmp($existingDept, 'UNKNOWN') === 0 || $existingBatch === 0);
        $canUpdateAcademic = ($status !== 'Approved') && $needsAcademicUpdate;

        $department = trim($_POST['department'] ?? '');
        $batch_year = trim($_POST['batch_year'] ?? '');

        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $status_type = trim($_POST['status_type'] ?? '');
        $current_job = trim($_POST['current_job'] ?? '');
        $address = trim($_POST['address']);
        $signature = trim($_POST['signature']);
        
        // Status-specific fields
        $company_name = trim($_POST['company_name'] ?? '');
        $salary = trim($_POST['salary'] ?? '');
        $working_proof = trim($_POST['working_proof'] ?? '');
        $college_name = trim($_POST['college_name'] ?? '');
        $studies_name = trim($_POST['studies_name'] ?? '');

        // Handle File Upload for Profile Photo
        $id_photo = null; // Only update if new file is uploaded
        if (isset($_FILES['profile_upload']) && $_FILES['profile_upload']['error'] == 0) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES["profile_upload"]["name"], PATHINFO_EXTENSION);
            // Simple validation
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($file_extension), $allowed_types)) {
                $new_filename = "profile_" . $student_id . "_" . time() . "." . $file_extension;
                $target_file = $target_dir . $new_filename;
                
                if (move_uploaded_file($_FILES["profile_upload"]["tmp_name"], $target_file)) {
                    $id_photo = $target_file; // Set new file path
                }
            }
        }
        
        $setParts = [
            "phone = ?",
            "email = ?",
            "current_job = ?",
            "address = ?",
            "signature = ?",
        ];
        $types = "sssss";
        $values = [$phone, $email, $current_job, $address, $signature];
        
        // Only update id_photo if a new file was uploaded
        if ($id_photo !== null) {
            $setParts[] = "id_photo = ?";
            $types .= "s";
            $values[] = $id_photo;
        }
        
        // Add status_type if column exists
        if (columnExists($conn, 'students', 'status_type')) {
            $setParts[] = "status_type = ?";
            $types .= "s";
            $values[] = $status_type;
        }
        
        // Add working-related fields if columns exist
        if (columnExists($conn, 'students', 'company_name')) {
            $setParts[] = "company_name = ?";
            $types .= "s";
            $values[] = $company_name;
        }
        if (columnExists($conn, 'students', 'salary')) {
            $setParts[] = "salary = ?";
            $types .= "s";
            $values[] = $salary;
        }
        if (columnExists($conn, 'students', 'working_proof')) {
            $setParts[] = "working_proof = ?";
            $types .= "s";
            $values[] = $working_proof;
        }
        
        // Add higher studies fields if columns exist
        if (columnExists($conn, 'students', 'college_name')) {
            $setParts[] = "college_name = ?";
            $types .= "s";
            $values[] = $college_name;
        }
        if (columnExists($conn, 'students', 'studies_name')) {
            $setParts[] = "studies_name = ?";
            $types .= "s";
            $values[] = $studies_name;
        }

        if ($canUpdateAcademic) {
            if ($department !== '') {
                $setParts[] = "department = ?";
                $types .= "s";
                $values[] = $department;
            }

            if ($batch_year !== '') {
                $setParts[] = "batch_year = ?";
                $types .= "s";
                $values[] = $batch_year;
            }
        }

        $sql = "UPDATE students SET " . implode(", ", $setParts) . " WHERE id = ?";
        $types .= "i";
        $values[] = $student_id;

        $update_stmt = $conn->prepare($sql);
        bindParamsDynamic($update_stmt, $types, $values);
        
        if ($update_stmt->execute()) {
            // Check if any proof fields changed
            $existing_working_proof = trim($meta['working_proof'] ?? '');
            $existing_signature = trim($meta['signature'] ?? '');
            $existing_id_photo = trim($meta['id_photo'] ?? '');

            // The new values
            $new_working_proof = $working_proof;
            $new_signature = $signature;
            $new_id_photo = $id_photo !== null ? $id_photo : $existing_id_photo;

            // Notice if there is at least one non-empty proof field and if something changed
            $has_proofs = ($new_working_proof !== '' || $new_signature !== '' || $new_id_photo !== '');
            $proofs_changed = ($existing_working_proof !== $new_working_proof ||
                $existing_signature !== $new_signature ||
                $existing_id_photo !== $new_id_photo);

            if ($has_proofs && $proofs_changed) {
                // Fields changed, insert into student_proofs
                // 1. Mark existing as not current
                $update_proofs_stmt = $conn->prepare("UPDATE student_proofs SET status = 'previous' WHERE student_id = ?");
                $update_proofs_stmt->bind_param("i", $student_id);
                $update_proofs_stmt->execute();
                $update_proofs_stmt->close();
                
                // 2. Insert new proof
                $insert_proof_stmt = $conn->prepare("INSERT INTO student_proofs (student_id, id_photo, signature, working_proof, status) VALUES (?, ?, ?, ?, 'latest')");
                $insert_proof_stmt->bind_param("isss", $student_id, $new_id_photo, $new_signature, $new_working_proof);
                $insert_proof_stmt->execute();
                $insert_proof_stmt->close();
            }

            $message = "Profile details updated successfully!";
            header("Refresh:0");
        } else {
            $message = "Error updating profile: " . $conn->error;
        }
        $update_stmt->close();
    } elseif ($action == 'update_password') {
        // Password logic...
    }
}

// Fetch Student Data
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// Fetch Proof History
$history_stmt = $conn->prepare("SELECT * FROM student_proofs WHERE student_id = ? AND status = 'previous' ORDER BY uploaded_at DESC");
$history_stmt->bind_param("i", $student_id);
$history_stmt->execute();
$proof_history = $history_stmt->get_result();
$history_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | St.John's College</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Top Navigation -->
    <nav class="navbar" style="padding: 10px 0;">
        <div class="container navbar-container">
            <div class="logo-container">
                <a href="index.html" class="college-logo-placeholder" style="text-decoration: none;">SJC</a>
                <div class="college-name">
                    <h1>St.John's College, Palayamkottai</h1>
                    <span>Student Dashboard</span>
                </div>
            </div>
            <div class="nav-links">
                <span style="margin-right: 15px; color: var(--primary-color);">Welcome, <?php echo htmlspecialchars($student['name']); ?></span>
                <a href="logout.php" class="btn btn-secondary btn-sm">Logout</a>
            </div>
            <div class="mobile-menu-btn" id="sidebarToggle"><i class="fas fa-bars"></i></div>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="user-profile-mini">
                <?php if (!empty($student['id_photo'])): ?>
                    <img src="<?php echo getDriveDirectLink($student['id_photo']); ?>" alt="Profile" class="avatar-circle" style="object-fit: cover;">
                <?php else: ?>
                    <div class="avatar-circle"><?php echo strtoupper(substr($student['name'], 0, 1) . substr(strrchr($student['name'], " "), 1, 1)); ?></div>
                <?php endif; ?>
                <div>
                    <div style="font-weight: 600;"><?php echo htmlspecialchars($student['name']); ?></div>
                    <div style="font-size: 0.8rem; opacity: 0.7;">
                        <?php
                            $dept = trim($student['department'] ?? '');
                            $batch = (int)($student['batch_year'] ?? 0);
                            $showPlaceholder = ($dept === '' || strcasecmp($dept, 'UNKNOWN') === 0) && $batch === 0;
                            echo $showPlaceholder ? "Profile incomplete" : (htmlspecialchars($student['department']) . " - " . htmlspecialchars($student['batch_year']));
                        ?>
                    </div>
                </div>
            </div>
            <div class="sidebar-menu">
                <a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="#"><i class="fas fa-user"></i> My Profile</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h2>My Dashboard</h2>
                <?php if ($student['status'] == 'Approved'): ?>
                    <span class="badge badge-success">Verified Profile</span>
                <?php endif; ?>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="cards-grid mb-4">
                <div class="info-card" style="border-top-color: var(--secondary-color);">
                    <h3>Current Status</h3>
                    <p class="mt-2 text-center" style="font-size: 1.1rem; font-weight: bold; color: var(--primary-color);">
                        <?php
                            $statusType = $student['status_type'] ?? '';
                            if (!empty($statusType)) {
                                if ($statusType === 'Working' && !empty($student['company_name'])) {
                                    echo htmlspecialchars($statusType . ' at ' . $student['company_name']);
                                } elseif ($statusType === 'Higher Studies' && !empty($student['college_name'])) {
                                    echo htmlspecialchars($statusType . ' - ' . $student['college_name']);
                                } elseif (!empty($student['current_job'])) {
                                    echo htmlspecialchars($student['current_job']);
                                } else {
                                    echo htmlspecialchars($statusType);
                                }
                            } elseif (!empty($student['current_job'])) {
                                echo htmlspecialchars($student['current_job']);
                            } else {
                                echo 'Not Updated';
                            }
                        ?>
                    </p>
                    <hr>
                    <h5 class="text-center mt-3">Latest Proofs</h5>
                    <div class="text-center mt-2">
                        <?php if (!empty($student['id_photo'])): ?>
                            <div style="margin-bottom: 10px;">
                                <small><strong>ID Photo</strong></small><br>
                                <a href="<?php echo htmlspecialchars($student['id_photo']); ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                    <i class="fas fa-image"></i> View Photo
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($student['signature'])): ?>
                            <div style="margin-top: 10px; margin-bottom: 10px;">
                                <small><strong>Signature</strong></small><br>
                                <a href="<?php echo htmlspecialchars($student['signature']); ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                    <i class="fas fa-file-signature"></i> View Signature
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($student['working_proof'])): ?>
                            <div style="margin-top: 10px;">
                                <small><strong>Working Proof</strong></small><br>
                                <a href="<?php echo htmlspecialchars($student['working_proof']); ?>" target="_blank" class="btn btn-sm btn-info mt-1" style="font-size: 0.8rem; padding: 5px 10px;">
                                    <i class="fas fa-external-link-alt"></i> View Proof
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (empty($student['id_photo']) && empty($student['signature']) && empty($student['working_proof'])): ?>
                            <p class="text-muted small">No proofs uploaded yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="info-card" style="border-top-color: var(--warning);">
                    <h3>Verification Docs (History)</h3>
                    <div class="mt-2 text-center" style="max-height: 400px; overflow-y: auto;">
                        <?php if (isset($proof_history) && $proof_history->num_rows > 0): ?>
                            <?php while($row = $proof_history->fetch_assoc()): ?>
                                <div class="history-item p-2 mb-3 border rounded" style="background: #f8f9fa;">
                                    <small class="text-muted d-block mb-2">Uploaded: <?php echo date('d M Y, h:i A', strtotime($row['uploaded_at'])); ?></small>
                                    
                                    <?php if (!empty($row['id_photo'])): ?>
                                        <a href="<?php echo htmlspecialchars($row['id_photo']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary mt-1" title="View ID Photo">
                                            <i class="fas fa-image"></i> Photo
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($row['signature'])): ?>
                                        <a href="<?php echo htmlspecialchars($row['signature']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary mt-1" title="View Signature">
                                            <i class="fas fa-file-signature"></i> Sig
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($row['working_proof'])): ?>
                                        <a href="<?php echo htmlspecialchars($row['working_proof']); ?>" target="_blank" class="btn btn-sm btn-outline-info mt-1" title="View Working Proof">
                                            <i class="fas fa-external-link-alt"></i> Proof
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted mt-3">No previous proofs found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Profile Details Section -->
            <div class="section-card">
                <div class="card-header">
                    <h3>Profile Information</h3>
                </div>
                <div class="card-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" disabled>
                            </div>
                            <div class="form-group">
                                <label>Register ID</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['register_number']); ?>" disabled>
                            </div>
                        </div>

                        <?php
                            $dept = trim($student['department'] ?? '');
                            $batch = (int)($student['batch_year'] ?? 0);
                            $needsAcademicUpdate = ($dept === '' || strcasecmp($dept, 'UNKNOWN') === 0 || $batch === 0);
                        ?>

                        <?php if ($needsAcademicUpdate): ?>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Department</label>
                                    <select class="form-control" name="department" required>
                                        <option value="" disabled selected>Select Department</option>
                                        <option value="CSE">Computer Science</option>
                                        <option value="IT">Information Technology</option>
                                        <option value="ECE">Electronics & Comm.</option>
                                        <option value="MECH">Mechanical Engg.</option>
                                        <option value="CIVIL">Civil Engg.</option>
                                        <option value="ARTS">Arts & Humanities</option>
                                    </select>
                                    <small class="text-muted">Please update your department to complete your profile.</small>
                                </div>
                                <div class="form-group">
                                    <label>Batch Year</label>
                                    <input type="text" name="batch_year" class="form-control" placeholder="Ex: 2021-2025" required>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-row">
                             <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($student['phone']); ?>" required>
                            </div>
                        </div>
                        
                        <!-- Current Status Dropdown -->
                        <div class="form-group">
                            <label>Current Status <span style="color: red;">*</span></label>
                            <select name="status_type" id="status_type" class="form-control" required onchange="toggleStatusFields()">
                                <option value="">Select Status</option>
                                <option value="Working" <?php echo (isset($student['status_type']) && $student['status_type'] == 'Working') ? 'selected' : ''; ?>>Working</option>
                                <option value="Higher Studies" <?php echo (isset($student['status_type']) && $student['status_type'] == 'Higher Studies') ? 'selected' : ''; ?>>Higher Studies</option>
                                <option value="Self or Business" <?php echo (isset($student['status_type']) && $student['status_type'] == 'Self or Business') ? 'selected' : ''; ?>>Self or Business</option>
                                <option value="Other" <?php echo (isset($student['status_type']) && $student['status_type'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <!-- Working Fields -->
                        <div id="working_fields" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Company Name <span style="color: red;">*</span></label>
                                    <input type="text" name="company_name" id="company_name" class="form-control" value="<?php echo htmlspecialchars($student['company_name'] ?? ''); ?>" placeholder="Enter company name">
                                </div>
                                <div class="form-group">
                                    <label>Salary</label>
                                    <input type="text" name="salary" id="salary" class="form-control" value="<?php echo htmlspecialchars($student['salary'] ?? ''); ?>" placeholder="E.g. ₹50,000/month">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>ID Card or Offer Letter (Drive Link) <span style="color: red;">*</span></label>
                                <input type="text" name="working_proof" id="working_proof" class="form-control" value="<?php echo htmlspecialchars($student['working_proof'] ?? ''); ?>" placeholder="Google Drive Link">
                                <small class="text-muted">Upload your ID card or offer letter as proof</small>
                            </div>
                        </div>

                        <!-- Higher Studies Fields -->
                        <div id="higher_studies_fields" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>College/University Name <span style="color: red;">*</span></label>
                                    <input type="text" name="college_name" id="college_name" class="form-control" value="<?php echo htmlspecialchars($student['college_name'] ?? ''); ?>" placeholder="Enter college/university name">
                                </div>
                                <div class="form-group">
                                    <label>Course/Program Name <span style="color: red;">*</span></label>
                                    <input type="text" name="studies_name" id="studies_name" class="form-control" value="<?php echo htmlspecialchars($student['studies_name'] ?? ''); ?>" placeholder="E.g. M.Tech Computer Science">
                                </div>
                            </div>
                        </div>

                        <!-- Self or Business / Other Fields -->
                        <div id="other_status_fields" style="display: none;">
                            <div class="form-group">
                                <label>Details <span style="color: red;">*</span></label>
                                <input type="text" name="current_job" id="current_job" class="form-control" value="<?php echo htmlspecialchars($student['current_job'] ?? ''); ?>" placeholder="Please provide details about your status">
                            </div>
                        </div>
                        
                        <div class="form-group">
                             <label>Address</label>
                            <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($student['address']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Upload Profile Photo (Jpg/Png)</label>
                            <div class="custom-file-upload">
                                <input type="file" name="profile_upload" class="form-control" accept="image/*">
                            </div>
                            <small class="text-muted">Upload your profile photo</small>
                        </div>
                        
                        <div class="form-row">
                             <div class="form-group">
                                <label>Signature (Drive Link)</label>
                                <input type="text" name="signature" class="form-control" value="<?php echo htmlspecialchars($student['signature']); ?>" placeholder="Google Drive Link">
                            </div>
                        </div>
                         
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        function toggleStatusFields() {
            const statusType = document.getElementById('status_type').value;
            const workingFields = document.getElementById('working_fields');
            const higherStudiesFields = document.getElementById('higher_studies_fields');
            const otherStatusFields = document.getElementById('other_status_fields');
            
            // Hide all fields first
            workingFields.style.display = 'none';
            higherStudiesFields.style.display = 'none';
            otherStatusFields.style.display = 'none';
            
            // Remove required attributes
            document.getElementById('company_name').removeAttribute('required');
            document.getElementById('working_proof').removeAttribute('required');
            document.getElementById('college_name').removeAttribute('required');
            document.getElementById('studies_name').removeAttribute('required');
            document.getElementById('current_job').removeAttribute('required');
            
            // Show relevant fields based on selection
            if (statusType === 'Working') {
                workingFields.style.display = 'block';
                document.getElementById('company_name').setAttribute('required', 'required');
                document.getElementById('working_proof').setAttribute('required', 'required');
            } else if (statusType === 'Higher Studies') {
                higherStudiesFields.style.display = 'block';
                document.getElementById('college_name').setAttribute('required', 'required');
                document.getElementById('studies_name').setAttribute('required', 'required');
            } else if (statusType === 'Self or Business' || statusType === 'Other') {
                otherStatusFields.style.display = 'block';
                document.getElementById('current_job').setAttribute('required', 'required');
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleStatusFields();
        });
    </script>
</body>
</html>
