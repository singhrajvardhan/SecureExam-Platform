<?php
session_start();
header('Content-Type: application/json');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$student_id = $data['student_id'] ?? $_SESSION['student_application_id'] ?? '';

if (!$student_id) {
    echo json_encode(['success' => false, 'message' => 'Student not identified']);
    exit();
}

// Get active exam (for simplicity, get the first active exam)
$stmt = $pdo->prepare("SELECT * FROM exams WHERE status = 'active' AND exam_date <= NOW() LIMIT 1");
$stmt->execute();
$exam = $stmt->fetch();

if (!$exam) {
    echo json_encode(['success' => false, 'message' => 'No active exam available']);
    exit();
}

// Check if session already exists
$check = $pdo->prepare("SELECT * FROM exam_sessions WHERE student_id = ? AND exam_id = ? AND status = 'active'");
$check->execute([$student_id, $exam['id']]);
$existing = $check->fetch();

if (!$existing) {
    $insert = $pdo->prepare("INSERT INTO exam_sessions (student_id, exam_id, start_time, status) VALUES (?, ?, NOW(), 'active')");
    $insert->execute([$student_id, $exam['id']]);
    $_SESSION['exam_session_id'] = $pdo->lastInsertId();
    $_SESSION['current_exam_id'] = $exam['id'];
} else {
    $_SESSION['exam_session_id'] = $existing['id'];
    $_SESSION['current_exam_id'] = $existing['exam_id'];
}

$_SESSION['exam_started'] = true;

echo json_encode(['success' => true, 'message' => 'Exam ready', 'exam_id' => $exam['id']]);
?>