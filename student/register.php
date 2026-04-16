<?php
session_start();
include '../config/database.php';
require_once '../raj2026/smtp_email.php';  // Function is loaded from here

$error = '';
$success = '';
$application_id = '';
$roll_number = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    
    // Generate Application ID (4 digits)
    $application_id = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    
    // Generate Roll Number (5 digits + year)
    $year = date('y');
    $random = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
    $roll_number = $year . $random;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO students (application_id, roll_number, full_name, father_name, mother_name, email, phone, address, date_of_birth, register_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$application_id, $roll_number, $full_name, $father_name, $mother_name, $email, $phone, $address, $dob]);
        
        // Send email with credentials (using function from smtp_email.php)
        $email_sent = sendRegistrationEmail($email, $full_name, $application_id, $roll_number);
        
        $success = true;
        
    } catch(PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Online Exam System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" type="image/png" href="raj.png">
    <style>
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .success-box {
            background: #d4edda;
            color: #155724;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
        }
        .success-box h2 {
            color: #155724;
            margin-top: 0;
        }
        .credentials-display {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .credential-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            margin: 10px 0;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #28a745;
        }
        .credential-label {
            font-weight: bold;
            font-size: 16px;
            color: #333;
        }
        .credential-value {
            font-size: 18px;
            color: #28a745;
            font-weight: bold;
            font-family: monospace;
        }
        .copy-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 10px;
        }
        .copy-btn:hover {
            background: #0056b3;
        }
        .email-sent {
            background: #e7f3ff;
            padding: 12px;
            border-radius: 5px;
            margin: 15px 0;
            color: #004085;
            font-size: 14px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        .btn-primary {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
        .btn-secondary {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
        .btn-primary:hover, .btn-secondary:hover {
            opacity: 0.9;
        }
        .error-box {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-box">
            <h2>Student Registration</h2>
            
            <?php if($error): ?>
                <div class="error-box">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($success) && $success === true): ?>
                <div class="success-box">
                    <h2>✓ Registration Successful!</h2>
                    
                    <div class="credentials-display">
                        <h3>Your Login Credentials</h3>
                        
                        <div class="credential-item">
                            <span class="credential-label">Application ID:</span>
                            <div>
                                <span class="credential-value" id="app_id"><?php echo $application_id; ?></span>
                                <button class="copy-btn" onclick="copyToClipboard('app_id')">Copy</button>
                            </div>
                        </div>
                        
                        <div class="credential-item">
                            <span class="credential-label">Roll Number:</span>
                            <div>
                                <span class="credential-value" id="roll_num"><?php echo $roll_number; ?></span>
                                <button class="copy-btn" onclick="copyToClipboard('roll_num')">Copy</button>
                            </div>
                        </div>
                        
                        <div class="email-sent">
                            📧 An email with these credentials has been sent to your registered email address.
                        </div>
                        
                        <div class="btn-group">
                            <a href="login.php" class="btn-primary">Go to Login</a>
                            <button onclick="window.print()" class="btn-secondary">Print Credentials</button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Father's Name *</label>
                            <input type="text" name="father_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Mother's Name *</label>
                            <input type="text" name="mother_name" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required>
                        <small style="color: #666;">Your credentials will be sent to this email</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Address *</label>
                        <textarea name="address" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Date of Birth *</label>
                        <input type="date" name="dob" required>
                    </div>
                    
                    <button type="submit" class="btn">Register</button>
                </form>
                <p style="margin-top: 20px; text-align: center;">Already have an account? <a href="login.php">Login here</a></p>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function copyToClipboard(elementId) {
            var text = document.getElementById(elementId).innerText;
            var textarea = document.createElement("textarea");
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand("copy");
            document.body.removeChild(textarea);
            
            // Show temporary notification
            var btn = event.target;
            var originalText = btn.innerText;
            btn.innerText = "Copied!";
            setTimeout(function() {
                btn.innerText = originalText;
            }, 2000);
        }
    </script>
</body>
</html>
