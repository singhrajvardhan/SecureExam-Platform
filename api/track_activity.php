<?php
header('Content-Type: application/json');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $pdo->prepare("
    INSERT INTO activity_logs (student_id, activity_type, activity_detail, ip_address, timestamp) 
    VALUES (?, ?, ?, ?, ?)
");
$stmt->execute([
    $data['student_id'],
    $data['activity_type'],
    $data['activity_detail'],
    $_SERVER['REMOTE_ADDR'],
    $data['timestamp']
]);

// Update exam session suspicious count
if(in_array($data['activity_type'], ['tab_switch', 'no_face_detected', 'multiple_faces', 'copy_attempt', 'paste_attempt'])) {
    $update = $pdo->prepare("UPDATE exam_sessions SET suspicious_events = suspicious_events + 1, tab_switches = tab_switches + 1 WHERE student_id = ? AND status = 'active'");
    $update->execute([$data['student_id']]);
}

echo json_encode(['success' => true]);
?>
