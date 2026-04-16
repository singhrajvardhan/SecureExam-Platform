<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $session_id = $data['session_id'];
    $student_id = $data['student_id'];
    $answers = $data['answers'];
    
    try {
        // Delete existing answers for this session
        $delete = $pdo->prepare("DELETE FROM exam_answers WHERE session_id = ?");
        $delete->execute([$session_id]);
        
        // Save new answers
        $stmt = $pdo->prepare("INSERT INTO exam_answers (session_id, student_id, question_id, selected_answer) VALUES (?, ?, ?, ?)");
        
        foreach ($answers as $question_id => $answer) {
            $stmt->execute([$session_id, $student_id, $question_id, $answer]);
        }
        
        echo json_encode(['success' => true]);
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $session_id = $_GET['session_id'];
    
    try {
        $stmt = $pdo->prepare("SELECT question_id, selected_answer FROM exam_answers WHERE session_id = ?");
        $stmt->execute([$session_id]);
        $answers = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        echo json_encode(['success' => true, 'answers' => $answers]);
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
