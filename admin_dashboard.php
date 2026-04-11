<?php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$admin_role = $_SESSION['admin_role'] ?? 'staff';
$admin_dept = $_SESSION['admin_dept'] ?? '';

// Fetch stats and students
$queryParam = "";
if ($admin_role === 'staff' && !empty($admin_dept)) {
    $stats = $conn->query("SELECT status_type, COUNT(*) as count FROM students WHERE department = '$admin_dept' GROUP BY status_type")->fetch_all(MYSQLI_ASSOC);
    $total = $conn->query("SELECT COUNT(*) as count FROM students WHERE department = '$admin_dept'")->fetch_assoc()['count'];
    
    $stmt = $conn->prepare("SELECT * FROM students WHERE department = ? ORDER BY id DESC");
    $stmt->bind_param("s", $admin_dept);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $stats = $conn->query("SELECT status_type, COUNT(*) as count FROM students GROUP BY status_type")->fetch_all(MYSQLI_ASSOC);
    $total = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
    
    $sql = "SELECT * FROM students ORDER BY id DESC";
    $result = $conn->query($sql);
}

$working = 0;
$higher_studies = 0;
foreach($stats as $s) {
    if(strtolower($s['status_type']) == 'working') $working = $s['count'];
    if(strtolower($s['status_type']) == 'higher studies') $higher_studies = $s['count'];
}

function getDriveDirectLink($url) {
    if (empty($url)) return '';
    if (strpos($url, 'drive.google.com') !== false) {
        $fileId = '';
        if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $fileId = $matches[1];
        } elseif (preg_match('/id=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $fileId = $matches[1];
        }
        if ($fileId) return "https://drive.google.com/thumbnail?id=" . $fileId . "&sz=w300";
    }
    return $url;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $admin_role === 'admin' ? 'Admin' : 'Staff'; ?> Dashboard | St.John's College</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .table-responsive { overflow-x: auto; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .data-table { width: 100%; border-collapse: collapse; min-width: 800px; }
        .data-table th, .data-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; vertical-align: middle; }
        .data-table th { background-color: #f8fafc; color: #334155; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        .data-table tr:hover { background-color: #f1f5f9; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid var(--primary-color); }
        .stat-card.success { border-bottom-color: #10b981; }
        .stat-card.info { border-bottom-color: #3b82f6; }
        .stat-number { font-size: 2.5rem; font-weight: bold; margin: 10px 0; color: #1e293b; }
        .stat-label { color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; }
        .student-photo { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; cursor: pointer; transition: transform 0.2s; }
        .student-photo:hover { transform: scale(1.1); }
        .photo-placeholder { width: 45px; height: 45px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #64748b; font-size: 1.2rem; border: 2px solid #cbd5e1; }
        .btn-download { background: #3b82f6; color: white; border: none; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s; white-space: nowrap; }
        .btn-download:hover { background: #2563eb; color: white; text-decoration: none; }
    </style>
</head>
<body class="bg-light">

    <!-- Top Navigation -->
    <nav class="navbar" style="padding: 10px 0;">
        <div class="container navbar-container">
            <div class="logo-container">
                <a href="index.html" class="college-logo-placeholder" style="text-decoration: none;">SJC</a>
                <div class="college-name">
                    <h1>St.John's College, Palayamkottai</h1>
                    <span><?php echo $admin_role === 'admin' ? 'Admin Portal' : 'Staff Portal'; ?></span>
                </div>
            </div>
            <div class="nav-links">
                <a href="admin_logout.php" class="btn btn-secondary btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            <div class="mobile-menu-btn" id="sidebarToggle"><i class="fas fa-bars"></i></div>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="user-profile-mini">
                <div class="avatar-circle" style="background-color: var(--secondary-color); color: white;"><i class="fas fa-user-shield"></i></div>
                <div>
                    <div style="font-weight: 600;"><?php echo htmlspecialchars($admin_username); ?></div>
                    <div style="font-size: 0.8rem; opacity: 0.7;">
                        <?php echo $admin_role === 'admin' ? 'Administrator' : 'Staff - ' . ($admin_dept ?: 'All Depts'); ?>
                    </div>
                </div>
            </div>
            <div class="sidebar-menu">
                <a href="admin_dashboard.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header section -->
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div style="display:flex; align-items:center; gap: 10px;">
                    <h2 style="margin:0;">Overview Dashboard</h2>
                    <?php if(!empty($admin_dept)) echo "<span class='badge badge-success' style='padding:5px 12px; border-radius:20px; font-weight:600;'>" . htmlspecialchars($admin_dept) . "</span>"; ?>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="cards-grid mb-4" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                <div class="stat-card">
                    <div class="stat-label">Total Students</div>
                    <div class="stat-number"><?php echo $total; ?></div>
                </div>
                <div class="stat-card success">
                    <div class="stat-label">Working</div>
                    <div class="stat-number text-success" style="color: #10b981;"><?php echo $working; ?></div>
                </div>
                <div class="stat-card info">
                    <div class="stat-label">Higher Studies</div>
                    <div class="stat-number text-info" style="color: #3b82f6;"><?php echo $higher_studies; ?></div>
                </div>
            </div>

            <!-- Students List -->
            <div class="section-card" style="box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                <div class="card-header border-bottom">
                    <h3 style="margin: 0; padding: 5px 0;"><i class="fas fa-user-graduate" style="color:var(--primary-color); margin-right:8px;"></i> Student Progression Profiles</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="70">Photo</th>
                                    <th>Name & Info</th>
                                    <th>Dept / Batch</th>
                                    <th>Current Path</th>
                                    <th width="120" style="text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($row['id_photo'])): ?>
                                                    <a href="<?php echo htmlspecialchars($row['id_photo']); ?>" target="_blank" title="View Full Photo">
                                                        <img src="<?php echo htmlspecialchars(getDriveDirectLink($row['id_photo']) ?: $row['id_photo']); ?>" class="student-photo" alt="Photo">
                                                    </a>
                                                <?php else: ?>
                                                    <div class="photo-placeholder" title="No Photo"><?php echo strtoupper(substr($row['name'], 0, 1)); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div style="font-weight: 600; color: #1e293b; font-size: 1.05rem;"><?php echo htmlspecialchars($row['name']); ?></div>
                                                <div style="font-size: 0.85rem; color: #64748b; margin-top:2px;"><strong>Reg:</strong> <?php echo htmlspecialchars($row['register_number']); ?></div>
                                                <div style="font-size: 0.85rem; color: #64748b; margin-top:2px; display:flex; align-items:center; gap:5px;"><i class="fas fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" style="color:inherit;text-decoration:none;"><?php echo htmlspecialchars($row['email'] ?: 'No Email'); ?></a></div>
                                                <div style="font-size: 0.85rem; color: #64748b; margin-top:2px; display:flex; align-items:center; gap:5px;"><i class="fas fa-phone-alt"></i> <a href="tel:<?php echo htmlspecialchars($row['phone']); ?>" style="color:inherit;text-decoration:none;"><?php echo htmlspecialchars($row['phone'] ?: 'No Phone'); ?></a></div>
                                            </td>
                                            <td>
                                                <span style="font-weight: 600; color: #334155;"><?php echo htmlspecialchars($row['department']); ?></span><br>
                                                <div style="color: #64748b; font-size: 0.9rem; margin-top: 4px;"><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($row['batch_year'] ? $row['batch_year'] : 'N/A'); ?></div>
                                            </td>
                                            <td>
                                                <?php 
                                                $type = $row['status_type'] ?? 'Not Updated';
                                                
                                                // Icon based on type
                                                $icon = "fa-briefcase";
                                                if($type === 'Higher Studies') $icon = "fa-university";
                                                if($type === 'Not Updated') $icon = "fa-clock";
                                                
                                                echo "<div style='font-weight:700; color:var(--primary-color); font-size:0.95rem; margin-bottom:4px;'><i class='fas $icon' style='margin-right:5px; opacity:0.8;'></i>" . htmlspecialchars($type) . "</div>";
                                                
                                                $details = '';
                                                if ($type === 'Working') {
                                                    $details = $row['company_name'] ?? $row['current_job'] ?? '';
                                                } elseif ($type === 'Higher Studies') {
                                                    $details = $row['college_name'] ?? '';
                                                } else {
                                                    $details = $row['current_job'] ?? '';
                                                }
                                                
                                                if ($details) {
                                                    echo "<div style='color: #475569; font-size:0.9rem; line-height: 1.3;'>" . htmlspecialchars($details) . "</div>";
                                                }
                                                
                                                // Check for proofs
                                                if (!empty($row['working_proof'])) {
                                                    echo "<div style='margin-top: 6px;'><a href='".htmlspecialchars($row['working_proof'])."' target='_blank' style='font-size:0.8rem; color:#3b82f6; text-decoration:none; border:1px solid #bfdbfe; background:#eff6ff; padding:3px 8px; border-radius:12px; display:inline-flex; align-items:center; gap:4px; font-weight:500;'><i class='fas fa-paperclip'></i> View Proof</a></div>";
                                                }
                                                ?>
                                            </td>
                                            <td style="text-align:center;">
                                                <a href="download_student.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn-download" title="Download Print Profile">
                                                    <i class="fas fa-download"></i> Download Profile
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 50px 20px;">
                                            <i class="fas fa-folder-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom:15px; display:block;"></i>
                                            <div style="color: #64748b; font-size:1.1rem; font-weight:500;">No student records found.</div>
                                            <p style="color:#94a3b8; font-size:0.9rem; margin-top:5px;">Check back later when students update their complete profiles.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Script imports -->
    <script src="assets/js/script.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>
