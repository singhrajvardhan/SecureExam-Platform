<?php
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $pdo->prepare("INSERT INTO camera_logs (session_id, student_id, face_detected, multiple_faces, no_face, detection_time) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $data['session_id'],
        $data['student_id'],
        $data['face_detected'],
        $data['multiple_faces'] ?? false,
        $data['no_face'] ?? false
    ]);
    
    if (!$data['face_detected']) {
        $update = $pdo->prepare("UPDATE exam_sessions SET face_detection_fails = face_detection_fails + 1 WHERE id = ?");
        $update->execute([$data['session_id']]);
    }
    
    echo json_encode(['success' => true]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
