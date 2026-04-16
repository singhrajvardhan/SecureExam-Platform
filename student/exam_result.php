<?php
require_once 'config.php';

// Check if student is logged in
if (!isset($_SESSION['student_logged_in']) && !isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

$session_id = $_GET['session_id'] ?? 0;

// Get exam result
$stmt = $pdo->prepare("SELECT * FROM exam_results WHERE session_id = ?");
$stmt->execute([$session_id]);
$result = $stmt->fetch();

if (!$result) {
    header('Location: student_dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Result - Online Exam System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .result-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .score-circle {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 30px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            font-weight: bold;
        }
        
        .result-details {
            text-align: left;
            margin: 20px 0;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .btn-dashboard {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        
        .warning {
            color: #dc3545;
            margin-top: 20px;
            padding: 10px;
            background: #fff3f3;
            border-radius: 8px;
        }
    </style>
<link rel="icon" type="image/png" href="raj.png">
</head>
<body>
    <div class="result-container">
        <h2><i class="fas fa-chart-line"></i> Exam Result</h2>
        
        <div class="score-circle">
            <?php echo round($result['score_percentage']); ?>%
        </div>
        
        <div class="result-details">
            <div class="detail-row">
                <span>Student Name:</span>
                <strong><?php echo htmlspecialchars($result['student_name']); ?></strong>
            </div>
            <div class="detail-row">
                <span>Roll Number:</span>
                <strong><?php echo htmlspecialchars($result['roll_number']); ?></strong>
            </div>
            <div class="detail-row">
                <span>Total Questions:</span>
                <strong><?php echo $result['total_questions']; ?></strong>
            </div>
            <div class="detail-row">
                <span>Correct Answers:</span>
                <strong style="color: #28a745;"><?php echo $result['correct_answers']; ?></strong>
            </div>
            <div class="detail-row">
                <span>Wrong Answers:</span>
                <strong style="color: #dc3545;"><?php echo $result['wrong_answers']; ?></strong>
            </div>
            <div class="detail-row">
                <span>Total Marks:</span>
                <strong><?php echo $result['total_marks']; ?></strong>
            </div>
            <div class="detail-row">
                <span>Obtained Marks:</span>
                <strong><?php echo $result['obtained_marks']; ?></strong>
            </div>
            <div class="detail-row">
                <span>Tab Switches:</span>
                <strong style="color: <?php echo $result['total_tab_switches'] > 0 ? '#dc3545' : '#28a745'; ?>">
                    <?php echo $result['total_tab_switches']; ?>
                </strong>
            </div>
        </div>
        
        <?php if ($result['total_tab_switches'] > 0): ?>
            <div class="warning">
                <i class="fas fa-exclamation-triangle"></i> 
                Warning: <?php echo $result['total_tab_switches']; ?> tab switch(es) detected during exam!
            </div>
        <?php endif; ?>
        
        <button class="btn-dashboard" onclick="window.location.href='student_dashboard.php'">
            <i class="fas fa-home"></i> Back to Dashboard
        </button>
    </div>
</body>
</html>
