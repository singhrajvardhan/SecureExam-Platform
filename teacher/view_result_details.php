<?php
// teacher/view_result_details.php - COMPLETE FIXED VERSION
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database configuration
define('DB_HOST', '..');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');

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

$session_id = $_GET['session_id'] ?? 0;

if ($session_id == 0) {
    header('Location: dashboard.php?msg=invalid_session');
    exit();
}

// Get exam result with student details
$result_stmt = $pdo->prepare("
    SELECT er.*, s.full_name, s.email, s.phone, s.application_id, s.roll_number 
    FROM exam_results er 
    LEFT JOIN students s ON er.student_id = s.application_id 
    WHERE er.session_id = ?
");
$result_stmt->execute([$session_id]);
$result = $result_stmt->fetch();

if (!$result) {
    header('Location: dashboard.php?msg=notfound');
    exit();
}

// Get all answers for this exam with question details
$answers_stmt = $pdo->prepare("
    SELECT ea.*, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_answer, q.marks 
    FROM exam_answers ea 
    JOIN questions q ON ea.question_id = q.id 
    WHERE ea.session_id = ?
    ORDER BY ea.id
");
$answers_stmt->execute([$session_id]);
$answers = $answers_stmt->fetchAll();

// Get tab switch logs
$tab_logs = $pdo->prepare("SELECT * FROM tab_switch_logs WHERE session_id = ? ORDER BY switch_time");
$tab_logs->execute([$session_id]);
$tab_switches = $tab_logs->fetchAll();

// Get camera logs
$camera_logs = $pdo->prepare("SELECT * FROM camera_logs WHERE session_id = ? ORDER BY detection_time");
$camera_logs->execute([$session_id]);
$camera_events = $camera_logs->fetchAll();

// Calculate statistics
$total_questions = count($answers);
$correct_count = 0;
$wrong_count = 0;
$total_marks_obtained = 0;
foreach ($answers as $ans) {
    if ($ans['selected_answer'] == $ans['correct_answer']) {
        $correct_count++;
        $total_marks_obtained += $ans['marks'];
    } else {
        $wrong_count++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Result Details - Teacher Dashboard</title>
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
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .score-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 15px;
            text-align: center;
        }
        
        .score-badge h1 {
            font-size: 48px;
            margin: 0;
        }
        
        .score-badge p {
            margin: 5px 0 0;
            opacity: 0.9;
        }
        
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .info-item {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: transform 0.3s;
        }
        
        .info-item:hover {
            transform: translateX(5px);
        }
        
        .info-item label {
            font-size: 11px;
            color: #999;
            display: block;
            margin-bottom: 5px;
        }
        
        .info-item value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            display: block;
        }
        
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .stat-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: bold;
        }
        
        .stat-number.correct { color: #28a745; }
        .stat-number.wrong { color: #dc3545; }
        
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
        
        .correct {
            color: #28a745;
            font-weight: bold;
        }
        
        .wrong {
            color: #dc3545;
            font-weight: bold;
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
        
        .btn-print {
            background: #17a2b8;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            margin-left: 10px;
            transition: all 0.3s;
        }
        
        .btn-print:hover {
            background: #138496;
            transform: translateY(-2px);
        }
        
        .question-text {
            font-weight: 500;
            margin-bottom: 10px;
        }
        
        .options {
            margin-left: 20px;
            font-size: 13px;
            color: #666;
        }
        
        .selected-answer {
            background: #e7f3ff;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            th, td {
                padding: 8px;
                font-size: 12px;
            }
            
            .options {
                margin-left: 10px;
                font-size: 11px;
            }
        }
        
        @media print {
            .top-bar, .btn-back, .btn-print {
                display: none;
            }
            body {
                background: white;
                padding: 0;
            }
            .card {
                box-shadow: none;
                padding: 10px;
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
        <div>
            <button class="btn-back" onclick="window.history.back()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
        
        <div class="card">
            <div class="header">
                <div>
                    <h2><i class="fas fa-file-alt"></i> Exam Result Details</h2>
                    <p>Session ID: <?php echo htmlspecialchars($session_id); ?></p>
                    <p>Exam Date: <?php echo date('d M Y, h:i A', strtotime($result['completed_at'])); ?></p>
                </div>
                <div class="score-badge">
                    <h1><?php echo round($result['score_percentage']); ?>%</h1>
                    <p><?php echo $result['obtained_marks']; ?>/<?php echo $result['total_marks']; ?> marks</p>
                </div>
            </div>
            
            <div class="stats-summary">
                <div class="stat-box">
                    <div class="stat-number correct"><?php echo $correct_count; ?></div>
                    <div>Correct Answers</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number wrong"><?php echo $wrong_count; ?></div>
                    <div>Wrong Answers</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo $result['total_tab_switches']; ?></div>
                    <div>Tab Switches</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo $result['suspicious_activities']; ?></div>
                    <div>Suspicious Activities</div>
                </div>
            </div>
            
            <?php if ($result['total_tab_switches'] > 0 || $result['suspicious_activities'] > 0): ?>
            <div class="warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Security Warning:</strong> 
                <?php echo $result['total_tab_switches']; ?> tab switch(es) and 
                <?php echo $result['suspicious_activities']; ?> suspicious activities detected during this exam!
            </div>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h3><i class="fas fa-user"></i> Student Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label><i class="fas fa-user"></i> Student Name</label>
                    <value><?php echo htmlspecialchars($result['full_name']); ?></value>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-hashtag"></i> Roll Number</label>
                    <value><?php echo htmlspecialchars($result['roll_number']); ?></value>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-id-card"></i> Application ID</label>
                    <value><?php echo htmlspecialchars($result['application_id']); ?></value>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <value><?php echo htmlspecialchars($result['email']); ?></value>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-phone"></i> Phone</label>
                    <value><?php echo htmlspecialchars($result['phone']); ?></value>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-calendar-check"></i> Exam Completed</label>
                    <value><?php echo date('d M Y H:i:s', strtotime($result['completed_at'])); ?></value>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h3><i class="fas fa-question-circle"></i> Answer Details</h3>
            <div style="overflow-x: auto;">
                <?php if (empty($answers)): ?>
                    <div style="text-align: center; padding: 40px; color: #999;">
                        <i class="fas fa-inbox" style="font-size: 48px;"></i>
                        <p>No answers found for this exam</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th width="400">Question</th>
                                <th width="100">Selected</th>
                                <th width="100">Correct</th>
                                <th width="100">Result</th>
                                <th width="80">Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $q_num = 1; foreach ($answers as $answer): ?>
                            <tr>
                                <td><?php echo $q_num++; ?></td>
                                <td style="max-width: 400px;">
                                    <div class="question-text"><?php echo htmlspecialchars($answer['question_text']); ?></div>
                                    <div class="options">
                                        <small>A) <?php echo htmlspecialchars(substr($answer['option_a'], 0, 60)); ?></small><br>
                                        <small>B) <?php echo htmlspecialchars(substr($answer['option_b'], 0, 60)); ?></small><br>
                                        <small>C) <?php echo htmlspecialchars(substr($answer['option_c'], 0, 60)); ?></small><br>
                                        <small>D) <?php echo htmlspecialchars(substr($answer['option_d'], 0, 60)); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="selected-answer">
                                        <strong><?php echo $answer['selected_answer']; ?></strong>
                                    </span>
                                </td>
                                <td><strong><?php echo $answer['correct_answer']; ?></strong></td>
                                <td class="<?php echo $answer['selected_answer'] == $answer['correct_answer'] ? 'correct' : 'wrong'; ?>">
                                    <?php echo $answer['selected_answer'] == $answer['correct_answer'] ? '✓ Correct' : '✗ Wrong'; ?>
                                </td>
                                <td>
                                    <?php 
                                        if ($answer['selected_answer'] == $answer['correct_answer']) {
                                            echo '+ ' . $answer['marks'];
                                        } else {
                                            echo '0';
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (!empty($tab_switches)): ?>
        <div class="card">
            <h3><i class="fas fa-window-restore"></i> Tab Switch Logs</h3>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Switch Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tab_switches as $switch): ?>
                        <tr>
                            <td><?php echo date('d M Y H:i:s', strtotime($switch['switch_time'])); ?></td>
                            <td><?php echo $switch['switch_count']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($camera_events)): ?>
        <div class="card">
            <h3><i class="fas fa-camera"></i> Camera Detection Logs</h3>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Face Detected</th>
                            <th>Multiple Faces</th>
                            <th>No Face</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($camera_events as $event): ?>
                        <tr>
                            <td><?php echo date('d M Y H:i:s', strtotime($event['detection_time'])); ?></td>
                            <td class="<?php echo $event['face_detected'] ? 'correct' : 'wrong'; ?>">
                                <?php echo $event['face_detected'] ? '✓ Yes' : '✗ No'; ?>
                            </td>
                            <td><?php echo $event['multiple_faces'] ? '⚠️ Yes' : 'No'; ?></td>
                            <td><?php echo $event['no_face'] ? '⚠️ Yes' : 'No'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
