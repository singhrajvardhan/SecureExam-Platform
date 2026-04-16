<?php
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $pdo->prepare("INSERT INTO tab_switch_logs (session_id, student_id, switch_time, switch_count) VALUES (?, ?, NOW(), ?)");
    $stmt->execute([$data['session_id'], $data['student_id'], $data['switch_count']]);
    
    // Update exam session
    $update = $pdo->prepare("UPDATE exam_sessions SET total_tab_switches = total_tab_switches + 1 WHERE id = ?");
    $update->execute([$data['session_id']]);
    
    echo json_encode(['success' => true]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
