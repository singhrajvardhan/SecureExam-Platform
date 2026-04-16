<?php
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No data received']);
    exit();
}

$session_id = $data['session_id'];
$student_id = $data['student_id'];
$student_name = $data['student_name'];
$roll_number = $data['roll_number'];
$student_email = $data['student_email'];
$answers = $data['answers'];
$tab_switches = $data['tab_switches'] ?? 0;
$face_fails = $data['face_fails'] ?? 0;
$copy_attempts = $data['copy_attempts'] ?? 0;

try {
    // Create 2027 directory if not exists
    $year_folder = '2027';
    if (!file_exists($year_folder)) {
        mkdir($year_folder, 0777, true);
    }
    
    // Create student subfolder with roll number and name
    $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $student_name);
    $student_folder = $year_folder . '/' . $roll_number . '_' . $safe_name;
    if (!file_exists($student_folder)) {
        mkdir($student_folder, 0777, true);
    }
    
    // Get all questions with correct answers
    $stmt = $pdo->query("SELECT id, question_text, option_a, option_b, option_c, option_d, correct_answer FROM questions");
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate score
    $score = 0;
    $total_questions = count($questions);
    $question_results = [];
    
    foreach ($questions as $question) {
        $qid = $question['id'];
        $selected = isset($answers[$qid]) ? $answers[$qid] : null;
        $is_correct = ($selected && $selected == $question['correct_answer']) ? true : false;
        
        if ($is_correct) {
            $score++;
        }
        
        $question_results[] = [
            'question_id' => $qid,
            'question_text' => $question['question_text'],
            'options' => [
                'A' => $question['option_a'],
                'B' => $question['option_b'],
                'C' => $question['option_c'],
                'D' => $question['option_d']
            ],
            'correct_answer' => $question['correct_answer'],
            'selected_answer' => $selected,
            'is_correct' => $is_correct
        ];
    }
    
    $percentage = ($total_questions > 0) ? ($score / $total_questions) * 100 : 0;
    $status = ($percentage >= 40) ? 'passed' : 'failed';
    
    // Prepare exam data for JSON
    $exam_data = [
        'exam_info' => [
            'session_id' => $session_id,
            'student_id' => $student_id,
            'student_name' => $student_name,
            'roll_number' => $roll_number,
            'student_email' => $student_email,
            'submission_date' => date('Y-m-d H:i:s'),
            'year' => '2027'
        ],
        'exam_statistics' => [
            'total_questions' => $total_questions,
            'correct_answers' => $score,
            'wrong_answers' => $total_questions - $score,
            'percentage' => round($percentage, 2),
            'status' => $status,
            'tab_switches_detected' => $tab_switches,
            'face_detection_fails' => $face_fails,
            'copy_attempts' => $copy_attempts
        ],
        'answers' => $question_results,
        'raw_answers' => $answers
    ];
    
    // Save to JSON file in student folder
    $filename = $student_folder . '/exam_' . $session_id . '_' . date('Y-m-d_H-i-s') . '.json';
    file_put_contents($filename, json_encode($exam_data, JSON_PRETTY_PRINT));
    
    // Also save a copy in the main 2027 folder with roll number
    $main_copy = $year_folder . '/' . $roll_number . '_exam_' . $session_id . '.json';
    file_put_contents($main_copy, json_encode($exam_data, JSON_PRETTY_PRINT));
    
    // Update database
    if ($pdo) {
        $update = $pdo->prepare("
            UPDATE exam_sessions 
            SET end_time = NOW(), 
                status = 'completed',
                score = ?,
                total_questions = ?,
                percentage = ?,
                result_status = ?,
                tab_switch_count = ?,
                face_detection_fails = ?,
                copy_attempts = ?
            WHERE id = ? AND student_id = ?
        ");
        
        $update->execute([
            $score, 
            $total_questions, 
            $percentage, 
            $status,
            $tab_switches,
            $face_fails,
            $copy_attempts,
            $session_id, 
            $student_id
        ]);
        
        // Save individual answers to database
        foreach ($answers as $question_id => $selected_answer) {
            $is_correct = 0;
            foreach ($questions as $q) {
                if ($q['id'] == $question_id && $q['correct_answer'] == $selected_answer) {
                    $is_correct = 1;
                    break;
                }
            }
            
            $save_answer = $pdo->prepare("
                INSERT INTO student_answers (session_id, student_id, question_id, selected_answer, is_correct) 
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE selected_answer = ?, is_correct = ?
            ");
            $save_answer->execute([$session_id, $student_id, $question_id, $selected_answer, $is_correct, $selected_answer, $is_correct]);
        }
    }
    
    echo json_encode([
        'success' => true,
        'score' => $score,
        'total' => $total_questions,
        'percentage' => $percentage,
        'status' => $status,
        'json_file' => $filename,
        'message' => 'Exam submitted successfully!'
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
