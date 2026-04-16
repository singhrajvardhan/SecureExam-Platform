<?php
header('Content-Type: application/json');

$host = '..';
$dbname = '';
$username = '';
$password = '##@@';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Create camera_logs table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS camera_logs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        student_id VARCHAR(100),
        photo_data LONGTEXT,
        capture_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $stmt = $pdo->prepare("INSERT INTO camera_logs (student_id, photo_data) VALUES (?, ?)");
    $stmt->execute([$data['student_id'], $data['photo']]);
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
