<?php
session_start();
include '../config/database.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $application_id = $_POST['application_id'];
    $roll_number = $_POST['roll_number'];
    $dob = $_POST['dob'];
    
    $stmt = $pdo->prepare("SELECT * FROM students WHERE application_id = ? AND roll_number = ? AND date_of_birth = ? AND is_active = 1");
    $stmt->execute([$application_id, $roll_number, $dob]);
    $student = $stmt->fetch();
    
    if ($student) {
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_name'] = $student['full_name'];
        $_SESSION['student_type'] = 'student';
        
        // Log login activity
        $log = $pdo->prepare("INSERT INTO activity_logs (student_id, activity_type, activity_detail, ip_address, timestamp) VALUES (?, ?, ?, ?, NOW())");
        $log->execute([$student['id'], 'login', 'Student logged in', $_SERVER['REMOTE_ADDR']]);
        
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid credentials or account not active";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - Online Exam System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2>Student Login</h2>
            
            <?php if($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Application ID</label>
                    <input type="text" name="application_id" required placeholder="4-digit ID">
                </div>
                
                <div class="form-group">
                    <label>Roll Number</label>
                    <input type="text" name="roll_number" required placeholder="Year + 5 digits">
                </div>
                
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" required>
                </div>
                
                <button type="submit" class="btn">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
