<?php
// teacher/student_results.php - COMPLETE FIXED VERSION
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database configuration
define('DB_HOST', '..');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '##@@');

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

$student_id = $_GET['id'] ?? '';

if (empty($student_id)) {
    header('Location: dashboard.php?msg=no_student');
    exit();
}

// Get student details
$stmt = $pdo->prepare("SELECT * FROM students WHERE application_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: dashboard.php?msg=notfound');
    exit();
}

// Get all exam results for this student
$results = $pdo->prepare("
    SELECT * FROM exam_results 
    WHERE student_id = ? 
    ORDER BY completed_at DESC
");
$results->execute([$student_id]);
$exam_results = $results->fetchAll();

// Get tab switch statistics
$tab_stats = $pdo->prepare("
    SELECT COUNT(*) as total_switches, 
           MIN(switch_time) as first_switch,
           MAX(switch_time) as last_switch
    FROM tab_switch_logs 
    WHERE student_id = ?
");
$tab_stats->execute([$student_id]);
$tab_data = $tab_stats->fetch();

// Get camera detection statistics
$camera_stats = $pdo->prepare("
    SELECT COUNT(*) as total_detections,
           SUM(CASE WHEN face_detected = 1 THEN 1 ELSE 0 END) as face_detected_count,
           SUM(CASE WHEN face_detected = 0 THEN 1 ELSE 0 END) as face_missing_count,
           SUM(CASE WHEN multiple_faces = 1 THEN 1 ELSE 0 END) as multiple_faces_count
    FROM camera_logs 
    WHERE student_id = ?
");
$camera_stats->execute([$student_id]);
$camera_data = $camera_stats->fetch();

// Calculate average score
$avg_score = 0;
if (count($exam_results) > 0) {
    $total_score = array_sum(array_column($exam_results, 'score_percentage'));
    $avg_score = round($total_score / count($exam_results), 1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Results - Teacher Dashboard</title>
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
        
        .top-bar {
            background: #1a1a2e;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .student-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .info-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            transition: transform 0.3s;
        }
        
        .info-card:hover {
            transform: translateX(5px);
        }
        
        .info-card label {
            font-size: 11px;
            color: #999;
            display: block;
            margin-bottom: 5px;
        }
        
        .info-card value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            display: block;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }
        
        .data-table {
            background: white;
            border-radius: 15px;
            overflow-x: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .btn-view {
            background: #007bff;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-view:hover {
            background: #0056b3;
        }
        
        .warning-badge {
            background: #dc3545;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            display: inline-block;
        }
        
        .score-high {
            color: #28a745;
            font-weight: bold;
        }
        
        .score-low {
            color: #dc3545;
            font-weight: bold;
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
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            th, td {
                padding: 8px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div>
            <i class="fas fa-chalkboard-teacher"></i> Teacher Dashboard | 
            Welcome, <?php echo htmlspecialchars($_SESSION['teacher_name'] ?? 'Teacher'); ?>
        </div>
        <button class="logout-btn" onclick="window.location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </div>
    
    <div class="container">
        <button class="btn-back" onclick="window.location.href='dashboard.php'">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </button>
        
        <div class="header">
            <h2><i class="fas fa-user-graduate"></i> Student Results</h2>
            
            <div class="student-info">
                <div class="info-card">
                    <label><i class="fas fa-id-card"></i> Application ID</label>
                    <value><?php echo htmlspecialchars($student['application_id']); ?></value>
                </div>
                <div class="info-card">
                    <label><i class="fas fa-hashtag"></i> Roll Number</label>
                    <value><?php echo htmlspecialchars($student['roll_number']); ?></value>
                </div>
                <div class="info-card">
                    <label><i class="fas fa-user"></i> Full Name</label>
                    <value><?php echo htmlspecialchars($student['full_name']); ?></value>
                </div>
                <div class="info-card">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <value><?php echo htmlspecialchars($student['email']); ?></value>
                </div>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo count($exam_results); ?></div>
                <div>Total Exams Taken</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $tab_data['total_switches'] ?? 0; ?></div>
                <div>Total Tab Switches</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $camera_data['face_missing_count'] ?? 0; ?></div>
                <div>Camera Issues</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $avg_score; ?>%</div>
                <div>Average Score</div>
            </div>
        </div>
        
        <div class="data-table">
            <h3 style="padding: 15px;"><i class="fas fa-history"></i> Exam History</h3>
            <?php if (empty($exam_results)): ?>
                <div class="empty-state">
                    <i class="fas fa-chart-line"></i>
                    <p>No exam results found for this student</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Session ID</th>
                            <th>Date & Time</th>
                            <th>Score</th>
                            <th>Correct/Wrong</th>
                            <th>Tab Switches</th>
                            <th>Suspicious</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exam_results as $result): ?>
                        <tr>
                            <td><?php echo $result['session_id']; ?></td>
                            <td><?php echo date('d M Y H:i:s', strtotime($result['completed_at'])); ?></td>
                            <td class="<?php echo $result['score_percentage'] >= 40 ? 'score-high' : 'score-low'; ?>">
                                <strong><?php echo round($result['score_percentage']); ?>%</strong>
                                <br>
                                <small><?php echo $result['obtained_marks']; ?>/<?php echo $result['total_marks']; ?></small>
                            </td>
                            <td>
                                <span style="color: #28a745;">✓ <?php echo $result['correct_answers']; ?></span> / 
                                <span style="color: #dc3545;">✗ <?php echo $result['wrong_answers']; ?></span>
                            </td>
                            <td>
                                <?php echo $result['total_tab_switches']; ?>
                                <?php if ($result['total_tab_switches'] > 0): ?>
                                    <span class="warning-badge"><i class="fas fa-exclamation-triangle"></i></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $result['suspicious_activities']; ?></td>
                            <td>
                                <button class="btn-view" onclick="viewDetails(<?php echo $result['session_id']; ?>)">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function viewDetails(sessionId) {
            window.location.href = `view_result_details.php?session_id=${sessionId}`;
        }
    </script>
</body>
</html>
