<?php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo "Unauthorized access.";
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id === 0) {
    echo "Invalid Student ID.";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    echo "Student not found.";
    exit();
}

function getDriveDirectLink($url) {
    if (empty($url)) return '';
    if (strpos($url, 'drive.google.com') !== false) {
        if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $matches) || preg_match('/id=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return "https://drive.google.com/thumbnail?id=" . $matches[1] . "&sz=w500";
        }
    }
    return $url;
}

$photo_src = !empty($student['id_photo']) ? (getDriveDirectLink($student['id_photo']) ?: $student['id_photo']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progression Record - <?php echo htmlspecialchars($student['register_number']); ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; color: #333; }
        .print-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 40px; box-shadow: 0 0 10px rgba(0,0,0,0.1); border-top: 8px solid #1a365d; }
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 30px; }
        .college-info h1 { margin: 0 0 5px; color: #1a365d; font-size: 24px; }
        .college-info p { margin: 0; color: #64748b; }
        .title { text-align: center; margin-bottom: 30px; }
        .title h2 { margin: 0; color: #1e293b; text-transform: uppercase; font-size: 20px; letter-spacing: 1px; }
        
        .profile-section { display: flex; gap: 30px; margin-bottom: 40px; }
        .profile-photo { width: 120px; height: 120px; border-radius: 8px; object-fit: cover; border: 3px solid #e2e8f0; }
        .photo-placeholder { width: 120px; height: 120px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #94a3b8; font-weight: bold; border: 3px solid #e2e8f0; }
        
        .info-grid { flex-grow: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .info-item { margin-bottom: 5px; }
        .info-label { font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 2px; }
        .info-value { font-size: 16px; color: #0f172a; font-weight: 500; }
        
        .progression-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .progression-title { font-size: 16px; font-weight: 600; color: #1a365d; margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid #cbd5e1; padding-bottom: 10px; }
        .prog-row { display: flex; margin-bottom: 10px; }
        .prog-label { width: 160px; font-weight: 600; color: #475569; }
        .prog-value { flex: 1; color: #1e293b; }
        
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        
        .no-print { text-align: center; margin-bottom: 20px; }
        .btn-print { background: #10b981; color: white; border: none; padding: 10px 20px; font-size: 16px; border-radius: 5px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-weight: bold; font-family:inherit;}
        .btn-print:hover { background: #059669; }
        
        @media print {
            body { background: transparent; padding: 0; }
            .print-container { box-shadow: none; border-top: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
        <p style="color: #64748b; font-size: 14px;">Use the button above to download or print this record.</p>
    </div>

    <div class="print-container">
        <div class="header">
            <div class="college-info">
                <h1>St.John's College, Palayamkottai</h1>
                <p>Alumni Progression & Data Collection Office</p>
            </div>
            <div style="text-align: right; color: #64748b; font-size: 14px;">
                Date: <?php echo date('d M Y'); ?>
            </div>
        </div>
        
        <div class="title">
            <h2>Student Progression Record</h2>
        </div>
        
        <div class="profile-section">
            <div>
                <?php if ($photo_src): ?>
                    <img src="<?php echo htmlspecialchars($photo_src); ?>" class="profile-photo" alt="Photo">
                <?php else: ?>
                    <div class="photo-placeholder"><?php echo strtoupper(substr($student['name'], 0, 1)); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($student['name']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Register Number</div>
                    <div class="info-value"><?php echo htmlspecialchars($student['register_number']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Department</div>
                    <div class="info-value"><?php echo htmlspecialchars($student['department'] ?: 'N/A'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Batch Year</div>
                    <div class="info-value"><?php echo htmlspecialchars($student['batch_year'] ?: 'N/A'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email Address</div>
                    <div class="info-value"><?php echo htmlspecialchars($student['email'] ?: 'N/A'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value"><?php echo htmlspecialchars($student['phone'] ?: 'N/A'); ?></div>
                </div>
            </div>
        </div>
        
        <div class="progression-card">
            <h3 class="progression-title">Current Progression Details</h3>
            
            <div class="prog-row">
                <div class="prog-label">Progression Path</div>
                <div class="prog-value" style="font-weight: bold; color: #1a365d;">
                    <?php echo htmlspecialchars($student['status_type'] ?: 'Not Updated'); ?>
                </div>
            </div>
            
            <?php if ($student['status_type'] === 'Working'): ?>
                <div class="prog-row">
                    <div class="prog-label">Company Name</div>
                    <div class="prog-value"><?php echo htmlspecialchars($student['company_name'] ?: ($student['current_job'] ?: 'Not Provided')); ?></div>
                </div>
                <div class="prog-row">
                    <div class="prog-label">Salary Details</div>
                    <div class="prog-value"><?php echo htmlspecialchars($student['salary'] ?: 'Not Provided'); ?></div>
                </div>
            <?php elseif ($student['status_type'] === 'Higher Studies'): ?>
                <div class="prog-row">
                    <div class="prog-label">College/University</div>
                    <div class="prog-value"><?php echo htmlspecialchars($student['college_name'] ?: 'Not Provided'); ?></div>
                </div>
                <div class="prog-row">
                    <div class="prog-label">Course Name</div>
                    <div class="prog-value"><?php echo htmlspecialchars($student['studies_name'] ?: 'Not Provided'); ?></div>
                </div>
            <?php else: ?>
                <div class="prog-row">
                    <div class="prog-label">Additional Details</div>
                    <div class="prog-value"><?php echo htmlspecialchars($student['current_job'] ?: 'Not Provided'); ?></div>
                </div>
            <?php endif; ?>
            
            <div class="prog-row" style="margin-top: 15px;">
                <div class="prog-label">Contact Address</div>
                <div class="prog-value"><?php echo nl2br(htmlspecialchars($student['address'] ?: 'Not Provided')); ?></div>
            </div>
        </div>

        <?php if (!empty($student['working_proof'])): ?>
        <div style="font-size: 14px; color: #475569; background: #eef2ff; padding: 15px; border-radius: 6px; border-left: 4px solid #4f46e5;">
            <strong>Note:</strong> A verification proof document is attached to this profile (Available online: <?php echo htmlspecialchars($student['working_proof']); ?>)
        </div>
        <?php endif; ?>
        
        <div class="footer">
            Generated by St.John's College Ex-Student Data Collection System<br>
            Record ID #SJC-<?php echo str_pad($student['id'], 6, '0', STR_PAD_LEFT); ?>
        </div>
    </div>
</body>
</html>
