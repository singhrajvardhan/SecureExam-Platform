<?php
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $pdo->prepare("INSERT INTO suspicious_activities (session_id, student_id, activity_type, detected_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$data['session_id'], $data['student_id'], $data['activity']]);
    
    $update = $pdo->prepare("UPDATE exam_sessions SET suspicious_activities = suspicious_activities + 1 WHERE id = ?");
    $update->execute([$data['session_id']]);
    
    echo json_encode(['success' => true]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
