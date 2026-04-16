<?php
header('Content-Type: application/json');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

// Get correct answer
$stmt = $pdo->prepare("SELECT correct_answer FROM exam_questions WHERE id = ?");
$stmt->execute([$data['question_id']]);
$correct = $stmt->fetchColumn();

$is_correct = ($data['answer'] == $correct) ? 1 : 0;

// Save or update answer
$stmt = $pdo->prepare("
    INSERT INTO student_answers (student_id, question_id, selected_answer, is_correct, answer_time) 
    VALUES (?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE 
    selected_answer = ?, is_correct = ?, answer_time = ?
");
$stmt->execute([
    $data['student_id'], 
    $data['question_id'], 
    $data['answer'], 
    $is_correct, 
    $data['timestamp'],
    $data['answer'],
    $is_correct,
    $data['timestamp']
]);

echo json_encode(['success' => true]);
?>
