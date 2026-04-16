<?php
// teacher/login.php - Fixed version
require_once 'config.php';  // Changed from ../config.php to config.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password']));
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM teachers WHERE (username = ? OR email = ?) AND password = ?");
        $stmt->execute([$username, $username, $password]);
        $teacher = $stmt->fetch();
        
        if ($teacher) {
            $_SESSION['teacher_logged_in'] = true;
            $_SESSION['teacher_id'] = $teacher['id'];
            $_SESSION['teacher_name'] = $teacher['full_name'];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Invalid username/email or password!";
        }
    } catch(PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login - Online Exam System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="raj.png">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .info {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
        }
        
        .debug-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 11px;
            color: #666;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2><i class="fas fa-chalkboard-teacher"></i> Teacher Login</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username or Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>
        
        <div class="info">
            <i class="fas fa-info-circle"></i> Contact Developer: 9770289936
        </div>
        
        <?php
        // Debug: Check if teachers table has any records
        try {
            $check = $pdo->query("SELECT COUNT(*) as count FROM teachers");
            $teacher_count = $check->fetch()['count'];
            if ($teacher_count == 0) {
                echo '<div class="debug-info">
                        <strong>⚠️ No teachers found in database!</strong><br>
                        Please run this SQL to add a teacher:<br>
                        <code style="font-size: 10px;">INSERT INTO teachers (username, email, password, full_name) VALUES ("admin", "admin@examsystem.com", MD5("admin123"), "Administrator");</code>
                      </div>';
            }
        } catch(Exception $e) {
            echo '<div class="debug-info">Table check failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>
</body>
</html>
