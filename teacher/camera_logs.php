<?php
// teacher/camera_logs.php - COMPLETE FIXED VERSION
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database configuration
define('DB_HOST', 'sql100.byetcluster.com');
define('DB_NAME', 'ezyro_41610737_exam_system');
define('DB_USER', 'ezyro_41610737');
define('DB_PASS', 'fcfd18633bda2##@@');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if teacher is logged in
if (!isset($_SESSION['teacher_logged_in'])) {
    header('Location: login.php');
    exit();
}

$student_id = $_GET['student_id'] ?? '';

if (empty($student_id)) {
    header('Location: dashboard.php?error=no_student');
    exit();
}

// Get student details
$stmt = $pdo->prepare("SELECT * FROM students WHERE application_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: dashboard.php?error=student_not_found');
    exit();
}

// Get all camera logs for this student
$camera_logs = $pdo->prepare("
    SELECT cl.*, es.start_time, es.end_time 
    FROM camera_logs cl 
    LEFT JOIN exam_sessions es ON cl.session_id = es.id 
    WHERE cl.student_id = ? 
    ORDER BY cl.detection_time DESC
");
$camera_logs->execute([$student_id]);
$logs = $camera_logs->fetchAll();

// Get statistics
$stats = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN face_detected = 1 THEN 1 ELSE 0 END) as face_detected,
        SUM(CASE WHEN face_detected = 0 THEN 1 ELSE 0 END) as face_missing,
        SUM(CASE WHEN multiple_faces = 1 THEN 1 ELSE 0 END) as multiple_faces
    FROM camera_logs 
    WHERE student_id = ?
");
$stats->execute([$student_id]);
$overall_stats = $stats->fetch();

// Get daily statistics
$daily_stats = $pdo->prepare("
    SELECT 
        DATE(detection_time) as date,
        COUNT(*) as total,
        SUM(CASE WHEN face_detected = 1 THEN 1 ELSE 0 END) as face_detected,
        SUM(CASE WHEN face_detected = 0 THEN 1 ELSE 0 END) as face_missing,
        SUM(CASE WHEN multiple_faces = 1 THEN 1 ELSE 0 END) as multiple_faces
    FROM camera_logs 
    WHERE student_id = ?
    GROUP BY DATE(detection_time)
    ORDER BY date DESC
");
$daily_stats->execute([$student_id]);
$daily_stats_data = $daily_stats->fetchAll();

$total_logs = $overall_stats['total'] ?? 0;
$face_detected = $overall_stats['face_detected'] ?? 0;
$face_missing = $overall_stats['face_missing'] ?? 0;
$multiple_faces = $overall_stats['multiple_faces'] ?? 0;
$compliance_rate = $total_logs > 0 ? round(($face_detected / $total_logs) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camera Logs - Teacher Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="raj.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .top-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
        }
        
        .stat-label {
            font-size: 14px;
            margin-top: 5px;
            opacity: 0.9;
        }
        
        .stat-card.compliance {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .face-detected {
            color: #28a745;
            font-weight: bold;
        }
        
        .face-missing {
            color: #dc3545;
            font-weight: bold;
        }
        
        .warning-badge {
            background: #ffc107;
            color: #333;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            display: inline-block;
        }
        
        .student-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
        }
        
        .student-info p {
            margin: 5px 0;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            th, td {
                padding: 8px;
                font-size: 12px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <div>
                <i class="fas fa-chalkboard-teacher"></i> Teacher Dashboard | 
                Welcome, <?php echo htmlspecialchars($_SESSION['teacher_name'] ?? 'Teacher'); ?>
            </div>
            <button class="logout-btn" onclick="window.location.href='logout.php'">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
        
        <button class="btn-back" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </button>
        
        <div class="card">
            <div class="header">
                <div>
                    <h2><i class="fas fa-camera"></i> Camera Detection Logs</h2>
                    <div class="student-info">
                        <p><strong><i class="fas fa-user"></i> Student:</strong> <?php echo htmlspecialchars($student['full_name']); ?></p>
                        <p><strong><i class="fas fa-id-card"></i> Application ID:</strong> <?php echo htmlspecialchars($student['application_id']); ?></p>
                        <p><strong><i class="fas fa-hashtag"></i> Roll Number:</strong> <?php echo htmlspecialchars($student['roll_number']); ?></p>
                        <p><strong><i class="fas fa-envelope"></i> Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $total_logs; ?></div>
                    <div class="stat-label">Total Detections</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $face_detected; ?></div>
                    <div class="stat-label">Face Detected</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $face_missing; ?></div>
                    <div class="stat-label">Face Missing</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $multiple_faces; ?></div>
                    <div class="stat-label">Multiple Faces</div>
                </div>
            </div>
            
            <?php if ($total_logs > 0): ?>
                <div class="stats-grid">
                    <div class="stat-card compliance">
                        <div class="stat-value"><?php echo $compliance_rate; ?>%</div>
                        <div class="stat-label">Compliance Rate</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($daily_stats_data)): ?>
        <div class="card">
            <h3><i class="fas fa-calendar-alt"></i> Daily Summary</h3>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Total Detections</th>
                            <th>Face Detected</th>
                            <th>Face Missing</th>
                            <th>Multiple Faces</th>
                            <th>Compliance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daily_stats_data as $stat): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($stat['date'])); ?></td>
                            <td><?php echo $stat['total']; ?></td>
                            <td class="face-detected"><?php echo $stat['face_detected']; ?></td>
                            <td class="face-missing"><?php echo $stat['face_missing']; ?></td>
                            <td><?php echo $stat['multiple_faces']; ?></td>
                            <td>
                                <?php 
                                    $daily_compliance = $stat['total'] > 0 ? round(($stat['face_detected'] / $stat['total']) * 100, 1) : 0;
                                    $compliance_class = $daily_compliance >= 80 ? 'face-detected' : 'face-missing';
                                ?>
                                <span class="<?php echo $compliance_class; ?>"><?php echo $daily_compliance; ?>%</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <h3><i class="fas fa-list"></i> Detailed Camera Logs</h3>
            <div style="overflow-x: auto;">
                <?php if (empty($logs)): ?>
                    <div class="empty-state">
                        <i class="fas fa-camera"></i>
                        <p>No camera logs found for this student</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Session ID</th>
                                <th>Face Detection</th>
                                <th>Multiple Faces</th>
                                <th>No Face</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?php echo date('d M Y H:i:s', strtotime($log['detection_time'])); ?></td>
                                <td><?php echo $log['session_id']; ?></td>
                                <td class="<?php echo $log['face_detected'] ? 'face-detected' : 'face-missing'; ?>">
                                    <?php echo $log['face_detected'] ? '<i class="fas fa-check-circle"></i> Detected' : '<i class="fas fa-times-circle"></i> Not Detected'; ?>
                                </td>
                                <td><?php echo $log['multiple_faces'] ? '<i class="fas fa-exclamation-triangle"></i> Yes' : 'No'; ?></td>
                                <td><?php echo $log['no_face'] ? '<i class="fas fa-exclamation-triangle"></i> Yes' : 'No'; ?></td>
                                <td>
                                    <?php if (!$log['face_detected']): ?>
                                        <span class="warning-badge"><i class="fas fa-ban"></i> Violation</span>
                                    <?php elseif ($log['multiple_faces']): ?>
                                        <span class="warning-badge"><i class="fas fa-users"></i> Multiple Faces</span>
                                    <?php else: ?>
                                        <span style="color: #28a745;"><i class="fas fa-check-circle"></i> Compliant</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
