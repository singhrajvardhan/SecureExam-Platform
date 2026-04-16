<?php
header('Content-Type: application/json');
session_start();

$host = '..m';
$dbname = '';
$username = '';
$password = '##@@;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("INSERT INTO exam_logs (student_id, event_type, event_detail, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $data['student_id'],
        $data['event_type'],
        $data['event_detail'],
        $_SERVER['REMOTE_ADDR']
    ]);
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
