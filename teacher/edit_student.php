<?php
// teacher/edit_student.php - COMPLETE FIXED VERSION
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database configuration
define('DB_HOST', '..');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', ');

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

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $roll_number = trim($_POST['roll_number']);
    $date_of_birth = $_POST['date_of_birth'];
    
    // Validation
    if (empty($full_name) || empty($email) || empty($roll_number)) {
        $error = "Please fill all required fields.";
    } else {
        try {
            // Handle password update if provided
            if (!empty($_POST['password'])) {
                $password = md5($_POST['password']);
                $update = $pdo->prepare("UPDATE students SET full_name = ?, email = ?, phone = ?, roll_number = ?, date_of_birth = ?, password = ? WHERE application_id = ?");
                $result = $update->execute([$full_name, $email, $phone, $roll_number, $date_of_birth, $password, $student_id]);
            } else {
                $update = $pdo->prepare("UPDATE students SET full_name = ?, email = ?, phone = ?, roll_number = ?, date_of_birth = ? WHERE application_id = ?");
                $result = $update->execute([$full_name, $email, $phone, $roll_number, $date_of_birth, $student_id]);
            }
            
            if ($result) {
                $success = "Student record updated successfully!";
                // Refresh student data
                $stmt = $pdo->prepare("SELECT * FROM students WHERE application_id = ?");
                $stmt->execute([$student_id]);
                $student = $stmt->fetch();
            } else {
                $error = "Failed to update student record!";
            }
        } catch(PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Teacher Dashboard</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
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
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        h2 {
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        
        .form-group label .required {
            color: #dc3545;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }
        
        .form-group input:disabled,
        .form-group input[readonly] {
            background: #f8f9fa;
            color: #666;
            cursor: not-allowed;
        }
        
        .btn-save {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
            transition: all 0.3s;
        }
        
        .btn-save:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        
        .photo-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-top: 10px;
            border: 3px solid #667eea;
        }
        
        .info-text {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        
        .button-group {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn-save, .btn-cancel {
                width: 100%;
                text-align: center;
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
        <h2><i class="fas fa-user-edit"></i> Edit Student</h2>
        
        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Application ID</label>
                <input type="text" value="<?php echo htmlspecialchars($student['application_id']); ?>" readonly disabled>
                <div class="info-text">This field cannot be changed</div>
            </div>
            
            <div class="form-group">
                <label>Roll Number <span class="required">*</span></label>
                <input type="text" name="roll_number" value="<?php echo htmlspecialchars($student['roll_number']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Full Name <span class="required">*</span></label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email <span class="required">*</span></label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" placeholder="Enter phone number">
            </div>
            
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" value="<?php echo $student['date_of_birth']; ?>">
            </div>
    
            
           
            
            <div class="form-group">
                <label>Registered On</label>
                <input type="text" value="<?php echo date('d M Y H:i:s', strtotime($student['register_date'])); ?>" readonly disabled>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="dashboard.php" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</body>
</html>
