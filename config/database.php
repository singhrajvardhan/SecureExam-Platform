<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Database configuration for ezyro hosting
$host = "sql100..com";
$username = "";
$password = "##@@";
$dbname = "";

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables if not exists
    $sql = "
    CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        application_id VARCHAR(10) UNIQUE NOT NULL,
        roll_number VARCHAR(15) UNIQUE NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(15) NOT NULL,
        address TEXT,
        date_of_birth DATE NOT NULL,
        photo_path VARCHAR(255),
        password VARCHAR(255) NOT NULL,
        is_active BOOLEAN DEFAULT TRUE,
        register_date DATETIME NOT NULL,
        last_logout DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    $sql = "
    CREATE TABLE IF NOT EXISTS exam_questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        section INT NOT NULL,
        question_text TEXT NOT NULL,
        option_a VARCHAR(255),
        option_b VARCHAR(255),
        option_c VARCHAR(255),
        option_d VARCHAR(255),
        correct_answer CHAR(1) NOT NULL,
        marks INT DEFAULT 1
    )";
    $pdo->exec($sql);
    
    $sql = "
    CREATE TABLE IF NOT EXISTS student_answers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        question_id INT NOT NULL,
        selected_answer CHAR(1),
        is_correct BOOLEAN,
        answer_time DATETIME NOT NULL,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (question_id) REFERENCES exam_questions(id) ON DELETE CASCADE,
        UNIQUE KEY unique_answer (student_id, question_id)
    )";
    $pdo->exec($sql);
    
    $sql = "
    CREATE TABLE IF NOT EXISTS activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        activity_type VARCHAR(50),
        activity_detail TEXT,
        ip_address VARCHAR(45),
        timestamp DATETIME NOT NULL,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    
    $sql = "
    CREATE TABLE IF NOT EXISTS exam_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        start_time DATETIME,
        end_time DATETIME,
        status VARCHAR(20) DEFAULT 'active',
        tab_switches INT DEFAULT 0,
        face_verified BOOLEAN DEFAULT FALSE,
        suspicious_events INT DEFAULT 0,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    
    // Insert sample questions if empty
    $check = $pdo->query("SELECT COUNT(*) FROM exam_questions")->fetchColumn();
    if ($check == 0) {
        for ($section = 1; $section <= 2; $section++) {
            for ($i = 1; $i <= 50; $i++) {
                $correct = ['A', 'B', 'C', 'D'][rand(0, 3)];
                $stmt = $pdo->prepare("INSERT INTO exam_questions (section, question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $section,
                    "Sample Question {$i} for Section {$section}: What is the correct answer?",
                    "Option A for Q{$i}",
                    "Option B for Q{$i}",
                    "Option C for Q{$i}",
                    "Option D for Q{$i}",
                    $correct
                ]);
            }
        }
    }
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
