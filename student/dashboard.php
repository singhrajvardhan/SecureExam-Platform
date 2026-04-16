<?php
require_once 'config.php';

// Check if student is logged in
if (!isset($_SESSION['student_logged_in']) && !isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

// Get student details
$student_app_id = $_SESSION['student_application_id'] ?? null;
if (!$student_app_id && isset($_SESSION['student_id'])) {
    $stmt = $pdo->prepare("SELECT application_id FROM students WHERE id = ?");
    $stmt->execute([$_SESSION['student_id']]);
    $student_data = $stmt->fetch();
    if ($student_data) {
        $student_app_id = $student_data['application_id'];
        $_SESSION['student_application_id'] = $student_app_id;
    }
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE application_id = ?");
$stmt->execute([$student_app_id]);
$student = $stmt->fetch();

if (!$student) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Get exam statistics with proper calculations
$stats = $pdo->prepare("
    SELECT 
        COUNT(*) as total_exams,
        COALESCE(AVG(score_percentage), 0) as avg_score,
        COALESCE(SUM(total_tab_switches), 0) as total_tab_switches,
        MAX(score_percentage) as highest_score,
        MIN(score_percentage) as lowest_score
    FROM exam_results 
    WHERE student_id = ?
");
$stats->execute([$student['application_id']]);
$exam_stats = $stats->fetch();

$total_exams = $exam_stats['total_exams'] ?? 0;
$avg_score = $exam_stats['avg_score'] ?? 0;
$total_tab_switches = $exam_stats['total_tab_switches'] ?? 0;
$highest_score = $exam_stats['highest_score'] ?? 0;
$lowest_score = $exam_stats['lowest_score'] ?? 0;

// Get last exam result with details
$last_result = $pdo->prepare("
    SELECT * FROM exam_results 
    WHERE student_id = ? 
    ORDER BY completed_at DESC 
    LIMIT 1
");
$last_result->execute([$student['application_id']]);
$last_exam = $last_result->fetch();

// Get exam history with ranking
$exam_history = $pdo->prepare("
    SELECT 
        er.*,
        RANK() OVER (ORDER BY er.score_percentage DESC) as rank_position
    FROM exam_results er
    WHERE er.student_id = ?
    ORDER BY er.completed_at DESC
    LIMIT 5
");
$exam_history->execute([$student['application_id']]);
$recent_exams = $exam_history->fetchAll();

// Calculate performance trend (compare last 3 exams)
$trend = $pdo->prepare("
    SELECT 
        score_percentage,
        completed_at
    FROM exam_results 
    WHERE student_id = ? 
    ORDER BY completed_at DESC 
    LIMIT 3
");
$trend->execute([$student['application_id']]);
$performance_trend = $trend->fetchAll();
$trend_direction = 'stable';
if (count($performance_trend) >= 2) {
    if ($performance_trend[0]['score_percentage'] > $performance_trend[1]['score_percentage']) {
        $trend_direction = 'improving';
    } elseif ($performance_trend[0]['score_percentage'] < $performance_trend[1]['score_percentage']) {
        $trend_direction = 'declining';
    }
}

// Create uploads directory if not exists
if (!is_dir('uploads/students')) {
    mkdir('uploads/students', 0777, true);
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    
    // Handle photo upload
    $profile_photo = $student['profile_photo'] ?? '';
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'uploads/students/' . $student['application_id'] . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $new_filename)) {
                $profile_photo = $new_filename;
            }
        }
    }
    
    $update = $pdo->prepare("UPDATE students SET full_name = ?, phone = ?, email = ?, father_name = ?, mother_name = ?, profile_photo = ? WHERE application_id = ?");
    $update->execute([$full_name, $phone, $email, $father_name, $mother_name, $profile_photo, $student['application_id']]);
    
    $_SESSION['student_name'] = $full_name;
    header('Location: student_dashboard.php?success=1');
    exit();
}

// Get current date and time
date_default_timezone_set('Asia/Kolkata');
$current_date = date('l, d F Y');
$current_time = date('h:i A');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Online Exam System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .top-bar {
            background: rgba(26, 26, 46, 0.95);
            backdrop-filter: blur(10px);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }

        .datetime {
            display: flex;
            gap: 20px;
            font-size: 14px;
        }

        .datetime i {
            margin-right: 8px;
        }

        .logout-btn {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220,53,69,0.4);
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .welcome-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .welcome-card:hover {
            transform: translateY(-5px);
        }

        .welcome-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .welcome-header h2 {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .performance-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .performance-improving {
            background: #d4edda;
            color: #155724;
        }

        .performance-declining {
            background: #f8d7da;
            color: #721c24;
        }

        .performance-stable {
            background: #d1ecf1;
            color: #0c5460;
        }

        .profile-section {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .profile-image {
            text-align: center;
            cursor: pointer;
            position: relative;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #667eea;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: white;
        }

        .edit-icon {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #667eea;
            border-radius: 50%;
            padding: 10px;
            color: white;
            font-size: 14px;
            transition: all 0.3s;
        }

        .edit-icon:hover {
            transform: scale(1.1);
        }

        .profile-details {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .detail-item.editable {
            cursor: pointer;
        }

        .detail-item.editable:hover {
            background: linear-gradient(135deg, #667eea20, #764ba220);
            transform: translateX(5px);
        }

        .detail-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .detail-info {
            flex: 1;
        }

        .detail-info label {
            display: block;
            font-size: 11px;
            color: #999;
            margin-bottom: 3px;
        }

        .detail-info span {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(102,126,234,0.1), transparent);
            transition: left 0.5s;
        }

        .stat-card:hover::before {
            left: 100%;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .stat-icon {
            font-size: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
        }

        .exam-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            color: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: all 0.3s;
            margin-bottom: 30px;
        }

        .exam-card:hover {
            transform: scale(1.02);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        .exam-card h3 {
            font-size: 28px;
            margin-bottom: 15px;
        }

        .exam-info {
            background: rgba(255,255,255,0.2);
            border-radius: 15px;
            padding: 15px;
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .exam-info-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .start-exam-btn {
            width: 100%;
            max-width: 300px;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            background: white;
            color: #667eea;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .start-exam-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .history-section {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-top: 30px;
        }

        .history-section h3 {
            margin-bottom: 20px;
            color: #333;
        }

        .exam-history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .exam-history-table th,
        .exam-history-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .exam-history-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #667eea;
        }

        .exam-history-table tr:hover {
            background: #f8f9fa;
        }

        .score-high {
            color: #28a745;
            font-weight: bold;
        }

        .score-medium {
            color: #ffc107;
            font-weight: bold;
        }

        .score-low {
            color: #dc3545;
            font-weight: bold;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            position: relative;
            animation: slideDown 0.3s;
            max-height: 80vh;
            overflow-y: auto;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .close {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 28px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .close:hover {
            color: #dc3545;
            transform: rotate(90deg);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-save {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40,167,69,0.3);
        }

        .success-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            animation: slideInRight 0.5s;
            z-index: 1001;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 20px 15px;
            }
            
            .profile-img {
                width: 100px;
                height: 100px;
                font-size: 40px;
            }
            
            .exam-card h3 {
                font-size: 22px;
            }
            
            .exam-history-table {
                font-size: 12px;
            }
        }
    </style>
    <link rel="icon" type="image/png" href="raj.png">
</head>
<body>

<div class="top-bar">
    <div>
        <i class="fas fa-graduation-cap"></i> Online Exam System
    </div>
    <div class="datetime">
        <div><i class="fas fa-calendar-alt"></i> <?php echo $current_date; ?></div>
        <div><i class="fas fa-clock"></i> <span id="currentTime"><?php echo $current_time; ?></span></div>
    </div>
    <button class="logout-btn" onclick="logout()">
        <i class="fas fa-sign-out-alt"></i> Logout
    </button>
</div>

<div class="dashboard-container">
    <?php if (isset($_GET['success'])): ?>
        <div class="success-message" id="successMessage">
            <i class="fas fa-check-circle"></i> Profile updated successfully!
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('successMessage')?.remove();
            }, 3000);
        </script>
    <?php endif; ?>

    <div class="welcome-card">
        <div class="welcome-header">
            <h2><i class="fas fa-smile-wink"></i> Welcome back, <?php echo htmlspecialchars(explode(' ', $student['full_name'])[0]); ?>!</h2>
            <div class="performance-badge performance-<?php echo $trend_direction; ?>">
                <i class="fas fa-chart-line"></i> Performance: <?php echo ucfirst($trend_direction); ?>
            </div>
        </div>

        <div class="profile-section">
            <div class="profile-image" onclick="openEditModal()">
                <?php 
                $profile_photo_path = $student['profile_photo'] ?? '';
                $has_photo = !empty($profile_photo_path) && file_exists($profile_photo_path);
                ?>
                <?php if ($has_photo): ?>
                    <img src="<?php echo htmlspecialchars($profile_photo_path); ?>" class="profile-img" alt="Profile Photo">
                <?php else: ?>
                    <div class="profile-img">
                        <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <div class="edit-icon">
                    <i class="fas fa-camera"></i>
                </div>
            </div>
            <div class="profile-details">
                <div class="detail-item editable" onclick="openEditModal()">
                    <div class="detail-icon"><i class="fas fa-user"></i></div>
                    <div class="detail-info">
                        <label>Full Name</label>
                        <span><?php echo htmlspecialchars($student['full_name']); ?></span>
                    </div>
                </div>
                <div class="detail-item editable" onclick="openEditModal()">
                    <div class="detail-icon"><i class="fas fa-male"></i></div>
                    <div class="detail-info">
                        <label>Father's Name</label>
                        <span><?php echo htmlspecialchars($student['father_name'] ?? 'Not provided'); ?></span>
                    </div>
                </div>
                <div class="detail-item editable" onclick="openEditModal()">
                    <div class="detail-icon"><i class="fas fa-female"></i></div>
                    <div class="detail-info">
                        <label>Mother's Name</label>
                        <span><?php echo htmlspecialchars($student['mother_name'] ?? 'Not provided'); ?></span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon"><i class="fas fa-id-card"></i></div>
                    <div class="detail-info">
                        <label>Application ID</label>
                        <span><?php echo htmlspecialchars($student['application_id']); ?></span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon"><i class="fas fa-hashtag"></i></div>
                    <div class="detail-info">
                        <label>Roll Number</label>
                        <span><?php echo htmlspecialchars($student['roll_number']); ?></span>
                    </div>
                </div>
                <div class="detail-item editable" onclick="openEditModal()">
                    <div class="detail-icon"><i class="fas fa-envelope"></i></div>
                    <div class="detail-info">
                        <label>Email</label>
                        <span><?php echo htmlspecialchars($student['email']); ?></span>
                    </div>
                </div>
                <div class="detail-item editable" onclick="openEditModal()">
                    <div class="detail-icon"><i class="fas fa-phone"></i></div>
                    <div class="detail-info">
                        <label>Phone</label>
                        <span><?php echo htmlspecialchars($student['phone']); ?></span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="detail-info">
                        <label>Date of Birth</label>
                        <span><?php echo date('d M Y', strtotime($student['date_of_birth'])); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card" onclick="showExamHistory()">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-value"><?php echo round($avg_score, 1); ?>%</div>
            <div class="stat-label">Average Score</div>
        </div>
        <div class="stat-card" onclick="showExamHistory()">
            <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            <div class="stat-value"><?php echo $total_exams; ?></div>
            <div class="stat-label">Total Exams Taken</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-trophy"></i></div>
            <div class="stat-value"><?php echo round($highest_score, 1); ?>%</div>
            <div class="stat-label">Highest Score</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-value"><?php echo $total_tab_switches; ?></div>
            <div class="stat-label">Total Tab Switches</div>
        </div>
    </div>

    <div class="exam-card">
        <h3><i class="fas fa-file-alt"></i> Online Examination</h3>
        <div class="exam-info">
            <div class="exam-info-item"><i class="fas fa-question-circle"></i> 20 Questions</div>
            <div class="exam-info-item"><i class="fas fa-clock"></i> 40 Minutes</div>
            <div class="exam-info-item"><i class="fas fa-minus-circle"></i> Negative Marking</div>
        </div>
        <button class="start-exam-btn" onclick="startExam()">
            <i class="fas fa-play"></i> Start Examination
        </button>
        <p style="margin-top: 15px; font-size: 12px; opacity: 0.9;">
            <i class="fas fa-info-circle"></i> Make sure you have a stable internet connection and working camera
        </p>
    </div>

    <?php if ($last_exam): ?>
    <div class="history-section">
        <h3><i class="fas fa-history"></i> Last Exam Result</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px;">
            <div>
                <strong>Completed:</strong><br>
                <?php echo date('d M Y, h:i A', strtotime($last_exam['completed_at'])); ?>
            </div>
            <div>
                <strong>Score:</strong><br>
                <span class="<?php echo $last_exam['score_percentage'] >= 60 ? 'score-high' : ($last_exam['score_percentage'] >= 40 ? 'score-medium' : 'score-low'); ?>">
                    <?php echo round($last_exam['score_percentage'], 1); ?>%
                </span>
            </div>
            <div>
                <strong>Correct Answers:</strong><br>
                <?php echo $last_exam['correct_answers']; ?> / <?php echo $last_exam['total_questions']; ?>
            </div>
            <div>
                <strong>Tab Switches:</strong><br>
                <?php echo $last_exam['total_tab_switches']; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($recent_exams)): ?>
    <div class="history-section">
        <h3><i class="fas fa-chart-bar"></i> Recent Exam History</h3>
        <table class="exam-history-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Score</th>
                    <th>Correct/Total</th>
                    <th>Tab Switches</th>
                    <th>Rank</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_exams as $exam): ?>
                <tr>
                    <td><?php echo date('d M Y, h:i A', strtotime($exam['completed_at'])); ?></td>
                    <td class="<?php echo $exam['score_percentage'] >= 60 ? 'score-high' : ($exam['score_percentage'] >= 40 ? 'score-medium' : 'score-low'); ?>">
                        <?php echo round($exam['score_percentage'], 1); ?>%
                    </td>
                    <td><?php echo $exam['correct_answers']; ?> / <?php echo $exam['total_questions']; ?></td>
                    <td><?php echo $exam['total_tab_switches']; ?></td>
                    <td>#<?php echo $exam['rank_position']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Edit Profile Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2 style="margin-bottom: 20px;"><i class="fas fa-user-edit"></i> Edit Profile</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-male"></i> Father's Name</label>
                <input type="text" name="father_name" value="<?php echo htmlspecialchars($student['father_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-female"></i> Mother's Name</label>
                <input type="text" name="mother_name" value="<?php echo htmlspecialchars($student['mother_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-phone"></i> Phone</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>">
            </div>
          
            <button type="submit" name="update_profile" class="btn-save">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </form>
    </div>
</div>

<script>
    // Update time every second
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            second: '2-digit'
        });
        const timeElement = document.getElementById('currentTime');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }
    
    setInterval(updateTime, 1000);
    
    function startExam() {
        if (confirm('Are you ready to start the exam? Make sure you have a stable internet connection and working camera.')) {
            window.location.href = 'exam.php';
        }
    }
    
    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'logout.php';
        }
    }
    
    function openEditModal() {
        document.getElementById('editModal').style.display = 'block';
    }
    
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }
    
    function showExamHistory() {
        document.querySelector('.history-section').scrollIntoView({ 
            behavior: 'smooth' 
        });
    }
    
    window.onclick = function(event) {
        if (event.target == document.getElementById('editModal')) {
            closeEditModal();
        }
    }
    
    // Auto-hide success message
    setTimeout(() => {
        const successMsg = document.getElementById('successMessage');
        if (successMsg) {
            successMsg.style.opacity = '0';
            setTimeout(() => successMsg.remove(), 500);
        }
    }, 3000);
</script>
</body>
</html>
