<?php
header('Content-Type: application/json');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Update exam session
    $stmt = $pdo->prepare("
        UPDATE exam_sessions 
        SET end_time = NOW(), 
            status = 'completed',
            tab_switches = ?,
            suspicious_events = ?
        WHERE student_id = ? AND status = 'active'
    ");
    $stmt->execute([
        $data['tab_switches'],
        $data['suspicious_events'],
        $data['student_id']
    ]);
    
    // Log submission
    $log = $pdo->prepare("INSERT INTO activity_logs (student_id, activity_type, activity_detail, ip_address, timestamp) VALUES (?, 'exam_submit', 'Exam submitted', ?, NOW())");
    $log->execute([$data['student_id'], $_SERVER['REMOTE_ADDR']]);
    
    echo json_encode(['success' => true]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
