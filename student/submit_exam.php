<?php
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $session_id = $data['session_id'];
    $student_id = $data['student_id'];
    $answers = $data['answers'];
    $tab_switches = $data['tab_switches'] ?? 0;
    $copy_attempts = $data['copy_attempts'] ?? 0;
    $face_fails = $data['face_fails'] ?? 0;
    
    // Get all questions
    $questions_stmt = $pdo->query("SELECT * FROM questions");
    $questions = $questions_stmt->fetchAll();
    
    $total_questions = count($questions);
    $correct_answers = 0;
    $total_marks = 0;
    $obtained_marks = 0;
    
    // Calculate results
    foreach ($questions as $question) {
        $total_marks += $question['marks'];
        if (isset($answers[$question['id']])) {
            if ($answers[$question['id']] == $question['correct_answer']) {
                $correct_answers++;
                $obtained_marks += $question['marks'];
            }
        }
    }
    
    $wrong_answers = $total_questions - $correct_answers;
    $score_percentage = ($total_marks > 0) ? ($obtained_marks / $total_marks) * 100 : 0;
    
    // Get student details
    $student_stmt = $pdo->prepare("SELECT full_name, roll_number FROM students WHERE application_id = ?");
    $student_stmt->execute([$student_id]);
    $student = $student_stmt->fetch();
    
    // Check if result already exists
    $check_stmt = $pdo->prepare("SELECT id FROM exam_results WHERE session_id = ?");
    $check_stmt->execute([$session_id]);
    
    if ($check_stmt->fetch()) {
        // Update existing result
        $result_stmt = $pdo->prepare("UPDATE exam_results SET total_questions = ?, correct_answers = ?, wrong_answers = ?, total_marks = ?, obtained_marks = ?, score_percentage = ?, total_tab_switches = ?, suspicious_activities = ?, completed_at = NOW() WHERE session_id = ?");
        $result_stmt->execute([
            $total_questions,
            $correct_answers,
            $wrong_answers,
            $total_marks,
            $obtained_marks,
            $score_percentage,
            $tab_switches,
            $face_fails,
            $session_id
        ]);
    } else {
        // Insert new result
        $result_stmt = $pdo->prepare("INSERT INTO exam_results (session_id, student_id, roll_number, student_name, total_questions, correct_answers, wrong_answers, total_marks, obtained_marks, score_percentage, total_tab_switches, suspicious_activities, completed_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $result_stmt->execute([
            $session_id,
            $student_id,
            $student['roll_number'],
            $student['full_name'],
            $total_questions,
            $correct_answers,
            $wrong_answers,
            $total_marks,
            $obtained_marks,
            $score_percentage,
            $tab_switches,
            $face_fails
        ]);
    }
    
    // Update exam session status
    $update_session = $pdo->prepare("UPDATE exam_sessions SET status = 'completed', end_time = NOW(), score = ?, total_questions = ?, correct_answers = ?, percentage = ? WHERE id = ?");
    $update_session->execute([$obtained_marks, $total_questions, $correct_answers, $score_percentage, $session_id]);
    
    // Save JSON backup
    $backup_dir = __DIR__ . '/json_backups/';
    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, 0777, true);
    }
    
    $backup_data = [
        'session_id' => $session_id,
        'student_id' => $student_id,
        'student_name' => $student['full_name'],
        'roll_number' => $student['roll_number'],
        'submission_time' => date('Y-m-d H:i:s'),
        'answers' => $answers,
        'results' => [
            'total_questions' => $total_questions,
            'correct_answers' => $correct_answers,
            'wrong_answers' => $wrong_answers,
            'total_marks' => $total_marks,
            'obtained_marks' => $obtained_marks,
            'percentage' => $score_percentage
        ],
        'cheating_metrics' => [
            'tab_switches' => $tab_switches,
            'copy_attempts' => $copy_attempts,
            'face_fails' => $face_fails
        ]
    ];
    
    $backup_file = $backup_dir . "submission_{$student_id}_{$session_id}_" . date('Y-m_d_H-i-s') . ".json";
    file_put_contents($backup_file, json_encode($backup_data, JSON_PRETTY_PRINT));
    
    // Return success response
    echo json_encode([
        'success' => true,
        'session_id' => $session_id,
        'result' => [
            'total' => $total_questions,
            'correct' => $correct_answers,
            'wrong' => $wrong_answers,
            'percentage' => round($score_percentage, 2)
        ],
        'message' => 'Exam submitted successfully'
    ]);
    
} catch(Exception $e) {
    // Log error but still return success for redirect
    error_log("Submit exam error: " . $e->getMessage());
    
    // Save error backup
    $error_dir = __DIR__ . '/json_backups/errors/';
    if (!file_exists($error_dir)) {
        mkdir($error_dir, 0777, true);
    }
    
    $error_data = [
        'session_id' => $session_id ?? null,
        'student_id' => $student_id ?? null,
        'error_time' => date('Y-m-d H:i:s'),
        'error_message' => $e->getMessage(),
        'answers' => $answers ?? []
    ];
    
    $error_file = $error_dir . "error_" . date('Y-m_d_H-i-s') . ".json";
    file_put_contents($error_file, json_encode($error_data, JSON_PRETTY_PRINT));
    
    // Always return success to ensure redirect
    echo json_encode([
        'success' => true,
        'session_id' => $session_id ?? null,
        'message' => 'Exam submitted (with errors logged)'
    ]);
}
?>
