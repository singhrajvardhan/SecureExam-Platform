<?php
// teacher/dashboard.php - FIXED VERSION
require_once 'config.php';  // Changed from ../config.php to config.php

// Check if teacher is logged in
if (!isset($_SESSION['teacher_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Handle student deletion
if (isset($_GET['delete_student'])) {
    $student_id = $_GET['delete_student'];
    $delete = $pdo->prepare("DELETE FROM students WHERE application_id = ?");
    $delete->execute([$student_id]);
    header('Location: dashboard.php?msg=deleted');
    exit();
}

// Handle question operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_question'])) {
        $stmt = $pdo->prepare("INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_answer, marks) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['question_text'],
            $_POST['option_a'],
            $_POST['option_b'],
            $_POST['option_c'],
            $_POST['option_d'],
            $_POST['correct_answer'],
            $_POST['marks']
        ]);
        header('Location: dashboard.php?msg=added');
        exit();
    }
    
    if (isset($_POST['edit_question'])) {
        $stmt = $pdo->prepare("UPDATE questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ?, marks = ? WHERE id = ?");
        $stmt->execute([
            $_POST['question_text'],
            $_POST['option_a'],
            $_POST['option_b'],
            $_POST['option_c'],
            $_POST['option_d'],
            $_POST['correct_answer'],
            $_POST['marks'],
            $_POST['question_id']
        ]);
        header('Location: dashboard.php?msg=updated');
        exit();
    }
    
    if (isset($_POST['delete_question'])) {
        $delete = $pdo->prepare("DELETE FROM questions WHERE id = ?");
        $delete->execute([$_POST['question_id']]);
        header('Location: dashboard.php?msg=deleted');
        exit();
    }
}

// Get all students
$students = $pdo->query("SELECT * FROM students ORDER BY register_date DESC")->fetchAll();

// Get all questions
$questions = $pdo->query("SELECT * FROM questions ORDER BY id")->fetchAll();

// Get all exam results with student details
$results = $pdo->query("
    SELECT er.*, s.application_id, s.email, s.phone, s.full_name as student_full_name 
    FROM exam_results er 
    LEFT JOIN students s ON er.student_id = s.application_id 
    ORDER BY er.completed_at DESC
")->fetchAll();

// Get tab switch statistics
$tab_stats = $pdo->query("
    SELECT student_id, COUNT(*) as total_switches 
    FROM tab_switch_logs 
    GROUP BY student_id 
    ORDER BY total_switches DESC
")->fetchAll();

// Get camera statistics
$camera_stats = $pdo->query("
    SELECT student_id, 
           COUNT(*) as total_detections,
           SUM(CASE WHEN face_detected = 0 THEN 1 ELSE 0 END) as face_missing
    FROM camera_logs 
    GROUP BY student_id 
    ORDER BY face_missing DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Online Exam System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="raj.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .top-bar {
            background: #1a1a2e;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            flex-wrap: wrap;
        }
        
        .tab-btn {
            padding: 10px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .tab-btn.active {
            background: #667eea;
            color: white;
            border-radius: 8px;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }
        
        .data-table {
            background: white;
            border-radius: 15px;
            overflow-x: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin: 2px;
        }
        
        .btn-edit {
            background: #007bff;
            color: white;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-view {
            background: #28a745;
            color: white;
        }
        
        .btn-add {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            margin-bottom: 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .btn-save {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .close {
            float: right;
            font-size: 28px;
            cursor: pointer;
        }
        
        .warning-badge {
            background: #dc3545;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
        }
        
        .success-msg {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 20px 15px;
            }
            
            th, td {
                padding: 8px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

<div class="top-bar">
    <div>
        <i class="fas fa-chalkboard-teacher"></i> Teacher Dashboard | 
        Welcome, <?php echo htmlspecialchars($_SESSION['teacher_name'] ?? 'Admin'); ?>
    </div>
    <button class="logout-btn" onclick="logout()">
        <i class="fas fa-sign-out-alt"></i> Logout
    </button>
</div>

<div class="dashboard-container">
    <?php if (isset($_GET['msg'])): ?>
        <div class="success-msg">
            <?php 
                if ($_GET['msg'] == 'deleted') echo "Record deleted successfully!";
                if ($_GET['msg'] == 'added') echo "Question added successfully!";
                if ($_GET['msg'] == 'updated') echo "Question updated successfully!";
            ?>
        </div>
    <?php endif; ?>
    
    <div class="tabs">
        <button class="tab-btn active" onclick="showTab('students')">Students</button>
        <button class="tab-btn" onclick="showTab('results')">Exam Results</button>
        <button class="tab-btn" onclick="showTab('questions')">Questions</button>
        <button class="tab-btn" onclick="showTab('logs')">Security Logs</button>
    </div>
    
    <!-- Students Tab -->
    <div id="students" class="tab-content active">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo count($students); ?></div>
                <div>Total Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo count($results); ?></div>
                <div>Exams Taken</div>
            </div>
        </div>
        
        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Roll Number</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Registered On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['application_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['phone']); ?></td>
                        <td><?php echo date('d M Y', strtotime($student['register_date'])); ?></td>
                        <td>
                            <button class="btn btn-edit" onclick="editStudent('<?php echo $student['application_id']; ?>')">Edit</button>
                            <button class="btn btn-delete" onclick="deleteStudent('<?php echo $student['application_id']; ?>')">Delete</button>
                            <button class="btn btn-view" onclick="viewStudentResults('<?php echo $student['application_id']; ?>')">Results</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Results Tab -->
    <div id="results" class="tab-content">
        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Roll Number</th>
                        <th>Score</th>
                        <th>Correct/Wrong</th>
                        <th>Tab Switches</th>
                        <th>Suspicious</th>
                        <th>Completed At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($result['student_name'] ?? $result['student_full_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($result['roll_number'] ?? 'N/A'); ?></td>
                        <td>
                            <strong><?php echo round($result['score_percentage']); ?>%</strong><br>
                            <small><?php echo $result['obtained_marks']; ?>/<?php echo $result['total_marks']; ?></small>
                        </td>
                        <td>
                            <span style="color: green;">✓ <?php echo $result['correct_answers']; ?></span> / 
                            <span style="color: red;">✗ <?php echo $result['wrong_answers']; ?></span>
                        </td>
                        <td>
                            <?php echo $result['total_tab_switches']; ?>
                            <?php if ($result['total_tab_switches'] > 0): ?>
                                <span class="warning-badge">⚠️</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $result['suspicious_activities']; ?></td>
                        <td><?php echo date('d M Y H:i', strtotime($result['completed_at'])); ?></td>
                        <td>
                            <button class="btn btn-view" onclick="viewResultDetails(<?php echo $result['session_id']; ?>)">View Details</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($results)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No exam results found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Questions Tab -->
    <div id="questions" class="tab-content">
        <button class="btn-add" onclick="openAddQuestionModal()">
            <i class="fas fa-plus"></i> Add New Question
        </button>
        
        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Question</th>
                        <th>Option A</th>
                        <th>Option B</th>
                        <th>Option C</th>
                        <th>Option D</th>
                        <th>Correct</th>
                        <th>Marks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($questions as $question): ?>
                    <tr>
                        <td><?php echo $question['id']; ?></td>
                        <td style="max-width: 300px;"><?php echo htmlspecialchars(substr($question['question_text'], 0, 60)); ?>...</td>
                        <td><?php echo htmlspecialchars(substr($question['option_a'], 0, 30)); ?></td>
                        <td><?php echo htmlspecialchars(substr($question['option_b'], 0, 30)); ?></td>
                        <td><?php echo htmlspecialchars(substr($question['option_c'], 0, 30)); ?></td>
                        <td><?php echo htmlspecialchars(substr($question['option_d'], 0, 30)); ?></td>
                        <td><strong><?php echo $question['correct_answer']; ?></strong></td>
                        <td><?php echo $question['marks']; ?></td>
                        <td>
                            <button class="btn btn-edit" onclick="editQuestion(<?php echo $question['id']; ?>)">Edit</button>
                            <button class="btn btn-delete" onclick="deleteQuestion(<?php echo $question['id']; ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Security Logs Tab -->
    <div id="logs" class="tab-content">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo array_sum(array_column($tab_stats, 'total_switches')); ?></div>
                <div>Total Tab Switches</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo count($camera_stats); ?></div>
                <div>Students with Camera Issues</div>
            </div>
        </div>
        
        <div class="data-table">
            <h3 style="padding: 15px;">Tab Switch Logs</h3>
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Total Switches</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tab_stats as $stat): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($stat['student_id']); ?></td>
                        <td>
                            <?php echo $stat['total_switches']; ?>
                            <?php if ($stat['total_switches'] > 3): ?>
                                <span class="warning-badge">High Risk</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-view" onclick="viewCameraLogs('<?php echo $stat['student_id']; ?>')">View Details</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="data-table">
            <h3 style="padding: 15px;">Camera Detection Issues</h3>
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Total Detections</th>
                        <th>Face Missing</th>
                        <th>Compliance Rate</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($camera_stats as $stat): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($stat['student_id']); ?></td>
                        <td><?php echo $stat['total_detections']; ?></td>
                        <td>
                            <?php echo $stat['face_missing']; ?>
                            <?php if ($stat['face_missing'] > 5): ?>
                                <span class="warning-badge">⚠️</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                                $compliance = 0;
                                if ($stat['total_detections'] > 0) {
                                    $compliance = (($stat['total_detections'] - $stat['face_missing']) / $stat['total_detections']) * 100;
                                }
                                echo round($compliance, 1) . '%';
                            ?>
                        </td>
                        <td>
                            <button class="btn btn-view" onclick="viewCameraLogs('<?php echo $stat['student_id']; ?>')">View Logs</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Question Modal -->
<div id="questionModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Add Question</h2>
        <form method="POST" id="questionForm">
            <input type="hidden" name="question_id" id="question_id">
            <div class="form-group">
                <label>Question Text</label>
                <textarea name="question_text" id="question_text" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label>Option A</label>
                <input type="text" name="option_a" id="option_a" required>
            </div>
            <div class="form-group">
                <label>Option B</label>
                <input type="text" name="option_b" id="option_b" required>
            </div>
            <div class="form-group">
                <label>Option C</label>
                <input type="text" name="option_c" id="option_c" required>
            </div>
            <div class="form-group">
                <label>Option D</label>
                <input type="text" name="option_d" id="option_d" required>
            </div>
            <div class="form-group">
                <label>Correct Answer</label>
                <select name="correct_answer" id="correct_answer" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
            <div class="form-group">
                <label>Marks</label>
                <input type="number" name="marks" id="marks" value="4" required>
            </div>
            <button type="submit" name="add_question" id="submitBtn" class="btn-save">Save Question</button>
        </form>
    </div>
</div>

<script>
    function showTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.getElementById(tabName).classList.add('active');
        event.target.classList.add('active');
    }
    
    function deleteStudent(applicationId) {
        if (confirm('Are you sure you want to delete this student?')) {
            window.location.href = `?delete_student=${applicationId}`;
        }
    }
    
    function editStudent(applicationId) {
        window.location.href = `edit_student.php?id=${applicationId}`;
    }
    
    function viewStudentResults(applicationId) {
        window.location.href = `student_results.php?id=${applicationId}`;
    }
    
    function openAddQuestionModal() {
        document.getElementById('modalTitle').textContent = 'Add New Question';
        document.getElementById('questionForm').reset();
        document.getElementById('question_id').value = '';
        document.getElementById('submitBtn').name = 'add_question';
        document.getElementById('questionModal').style.display = 'block';
    }
    
    function editQuestion(questionId) {
        fetch(`get_question.php?id=${questionId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalTitle').textContent = 'Edit Question';
                document.getElementById('question_id').value = data.id;
                document.getElementById('question_text').value = data.question_text;
                document.getElementById('option_a').value = data.option_a;
                document.getElementById('option_b').value = data.option_b;
                document.getElementById('option_c').value = data.option_c;
                document.getElementById('option_d').value = data.option_d;
                document.getElementById('correct_answer').value = data.correct_answer;
                document.getElementById('marks').value = data.marks;
                document.getElementById('submitBtn').name = 'edit_question';
                document.getElementById('questionModal').style.display = 'block';
            })
            .catch(error => {
                alert('Error fetching question details');
            });
    }
    
    function deleteQuestion(questionId) {
        if (confirm('Are you sure you want to delete this question?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="question_id" value="${questionId}">
                <input type="hidden" name="delete_question" value="1">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    function viewResultDetails(sessionId) {
        window.location.href = `view_result_details.php?session_id=${sessionId}`;
    }
    
    function viewCameraLogs(studentId) {
        window.location.href = `camera_logs.php?student_id=${studentId}`;
    }
    
    function closeModal() {
        document.getElementById('questionModal').style.display = 'none';
    }
    
    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'logout.php';
        }
    }
    
    window.onclick = function(event) {
        if (event.target == document.getElementById('questionModal')) {
            closeModal();
        }
    }
</script>
</body>
</html>
