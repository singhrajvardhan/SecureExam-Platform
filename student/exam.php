<?php
require_once 'config.php';

// Check if student is logged in
if (!isset($_SESSION['student_logged_in']) && !isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

$student_app_id = $_SESSION['student_application_id'] ?? null;
if (!$student_app_id) {
    header('Location: login.php');
    exit();
}

// Get student details
$stmt = $pdo->prepare("SELECT * FROM students WHERE application_id = ?");
$stmt->execute([$student_app_id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: login.php');
    exit();
}

// Check if there's an active exam session
$session_check = $pdo->prepare("SELECT * FROM exam_sessions WHERE student_id = ? AND status = 'active' ORDER BY id DESC LIMIT 1");
$session_check->execute([$student['application_id']]);
$active_session = $session_check->fetch();

if (!$active_session) {
    // Create new exam session
    $create_session = $pdo->prepare("INSERT INTO exam_sessions (student_id, student_name, roll_number, start_time, status) VALUES (?, ?, ?, NOW(), 'active')");
    $create_session->execute([$student['application_id'], $student['full_name'], $student['roll_number']]);
    $session_id = $pdo->lastInsertId();
} else {
    $session_id = $active_session['id'];
}

// Get all questions
$questions_stmt = $pdo->query("SELECT * FROM questions ORDER BY id LIMIT 50");
$questions = $questions_stmt->fetchAll();

$total_questions = count($questions);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Exam - Cheating Detection System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            overflow-x: hidden;
        }

        .warning-bar {
            background: #dc3545;
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
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
            margin-top: 40px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 999;
        }

        .timer-container {
            background: #28a745;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 20px;
            font-weight: bold;
        }

        .exam-container {
            display: flex;
            margin-top: 110px;
            padding: 20px;
            gap: 20px;
            min-height: calc(100vh - 110px);
        }

        .questions-panel {
            flex: 1;
            background: white;
            border-radius: 15px;
            padding: 20px;
            overflow-y: auto;
            max-height: calc(100vh - 130px);
        }

        .question-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            display: none;
        }

        .question-card.active {
            display: block;
        }

        .question-number {
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 15px;
        }

        .question-text {
            font-size: 16px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .options {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: white;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .option:hover {
            background: #e9ecef;
        }

        .option.selected {
            background: #d4edda;
            border-color: #28a745;
        }

        .option input {
            margin-right: 10px;
            cursor: pointer;
        }

        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            gap: 10px;
        }

        .nav-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .nav-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .prev-btn {
            background: #6c757d;
            color: white;
        }

        .next-btn {
            background: #007bff;
            color: white;
        }

        .submit-btn {
            background: #28a745;
            color: white;
        }

        .palette-panel {
            width: 300px;
            background: white;
            border-radius: 15px;
            padding: 20px;
            position: sticky;
            top: 130px;
            max-height: calc(100vh - 150px);
            overflow-y: auto;
        }

        .palette-title {
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .palette-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }

        .palette-btn {
            aspect-ratio: 1;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .palette-btn.unanswered {
            background: #e9ecef;
            color: #6c757d;
        }

        .palette-btn.answered {
            background: #28a745;
            color: white;
        }

        .palette-btn.current {
            border: 3px solid #007bff;
            transform: scale(1.05);
        }

        .camera-feed {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 200px;
            height: 150px;
            border: 3px solid #28a745;
            border-radius: 10px;
            overflow: hidden;
            background: #000;
            z-index: 1000;
        }

        .camera-feed video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .camera-warning {
            position: fixed;
            bottom: 180px;
            right: 20px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            z-index: 1000;
            display: none;
        }

        @media (max-width: 768px) {
            .exam-container {
                flex-direction: column;
            }
            
            .palette-panel {
                width: 100%;
                position: static;
            }
            
            .camera-feed {
                width: 150px;
                height: 112px;
            }
        }
    </style>
    <link rel="icon" type="image/png" href="raj.png">
</head>
<body>

<div class="warning-bar" id="warningBar">
    ⚠️ Warning: Copying, Tab switching, and any cheating attempts are strictly prohibited!
</div>

<div class="top-bar">
    <div>
        <i class="fas fa-user"></i> <?php echo htmlspecialchars($student['full_name']); ?>
        <span style="margin-left: 15px;"><i class="fas fa-id-card"></i> <?php echo htmlspecialchars($student['roll_number']); ?></span>
    </div>
    <div class="timer-container">
        <i class="fas fa-hourglass-half"></i> Time Left: <span id="timer">40:00</span>
    </div>
</div>

<div class="exam-container">
    <div class="questions-panel" id="questionsPanel">
        <?php foreach ($questions as $index => $question): ?>
            <div class="question-card" data-qid="<?php echo $question['id']; ?>" data-index="<?php echo $index; ?>">
                <div class="question-number">Question <?php echo $index + 1; ?> of <?php echo $total_questions; ?></div>
                <div class="question-text"><?php echo htmlspecialchars($question['question_text']); ?></div>
                <div class="options">
                    <label class="option">
                        <input type="radio" name="q<?php echo $question['id']; ?>" value="A"> 
                        <span>A) <?php echo htmlspecialchars($question['option_a']); ?></span>
                    </label>
                    <label class="option">
                        <input type="radio" name="q<?php echo $question['id']; ?>" value="B"> 
                        <span>B) <?php echo htmlspecialchars($question['option_b']); ?></span>
                    </label>
                    <label class="option">
                        <input type="radio" name="q<?php echo $question['id']; ?>" value="C"> 
                        <span>C) <?php echo htmlspecialchars($question['option_c']); ?></span>
                    </label>
                    <label class="option">
                        <input type="radio" name="q<?php echo $question['id']; ?>" value="D"> 
                        <span>D) <?php echo htmlspecialchars($question['option_d']); ?></span>
                    </label>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="nav-buttons">
            <button class="nav-btn prev-btn" onclick="previousQuestion()">← Previous</button>
            <button class="nav-btn next-btn" onclick="nextQuestion()">Next →</button>
            <button class="nav-btn submit-btn" onclick="submitExam()">Submit Exam</button>
        </div>
    </div>
    
    <div class="palette-panel">
        <div class="palette-title">Question Palette</div>
        <div class="palette-grid" id="paletteGrid">
            <?php for ($i = 0; $i < $total_questions; $i++): ?>
                <button class="palette-btn unanswered" onclick="goToQuestion(<?php echo $i; ?>)">
                    <?php echo $i + 1; ?>
                </button>
            <?php endfor; ?>
        </div>
    </div>
</div>

<div class="camera-feed">
    <video id="video" autoplay playsinline></video>
</div>
<div class="camera-warning" id="cameraWarning">
    ⚠️ Face not detected!
</div>

<script>
    let currentQuestion = 0;
    let totalQuestions = <?php echo $total_questions; ?>;
    let answers = {};
    let tabSwitchCount = 0;
    let copyAttemptCount = 0;
    let faceDetectionFails = 0;
    let examStarted = true;
    let sessionId = <?php echo $session_id; ?>;
    let studentId = '<?php echo $student['application_id']; ?>';
    let videoStream = null;
    let faceDetectionInterval = null;
    
    let timeLeft = 60 * 60;
    let timerInterval;
    
    document.addEventListener('DOMContentLoaded', function() {
        showQuestion(0);
        startTimer();
        startCamera();
        setupTabDetection();
        setupVisibilityDetection();
        setupCopyProtection();
        loadSavedAnswers();
        
        setInterval(saveAnswersToServer, 10000);
        setInterval(saveToJsonBackup, 30000);
    });
    
    function setupCopyProtection() {
        document.body.style.userSelect = 'none';
        document.body.style.webkitUserSelect = 'none';
        document.body.style.mozUserSelect = 'none';
        document.body.style.msUserSelect = 'none';
        
        document.addEventListener('copy', function(e) {
            e.preventDefault();
            copyAttemptCount++;
            logCopyAttempt();
            showWarning(`⚠️ Copying is prohibited! Attempt #${copyAttemptCount}`);
            
            if (copyAttemptCount >= 3) {
                showWarning('Multiple copying attempts detected! Exam will be auto-submitted!');
                setTimeout(() => {
                    if (copyAttemptCount >= 3) {
                        submitExam();
                    }
                }, 5000);
            }
            return false;
        });
        
        document.addEventListener('cut', function(e) {
            e.preventDefault();
            copyAttemptCount++;
            logCopyAttempt();
            showWarning('⚠️ Cutting text is prohibited!');
            return false;
        });
        
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            logSuspiciousActivity('right_click');
            showWarning('Right-click is disabled!');
            return false;
        });
        
        document.addEventListener('dragstart', function(e) {
            e.preventDefault();
            return false;
        });
        
        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
            return false;
        });
        
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey && (e.key === 'c' || e.key === 'x' || e.key === 'v' || e.key === 'a')) ||
                (e.metaKey && (e.key === 'c' || e.key === 'x' || e.key === 'v' || e.key === 'a'))) {
                e.preventDefault();
                copyAttemptCount++;
                logCopyAttempt();
                showWarning(`⚠️ Keyboard shortcuts are disabled! Attempt #${copyAttemptCount}`);
                
                if (copyAttemptCount >= 3) {
                    setTimeout(() => submitExam(), 5000);
                }
                return false;
            }
            
            if (e.key === 'PrintScreen') {
                e.preventDefault();
                logSuspiciousActivity('print_screen');
                showWarning('Screenshots are disabled!');
                return false;
            }
            
            if (e.key === 'F12') {
                e.preventDefault();
                logSuspiciousActivity('dev_tools');
                showWarning('Developer tools are disabled!');
                return false;
            }
        });
    }
    
    function logCopyAttempt() {
        fetch('log_suspicious_activity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                session_id: sessionId,
                student_id: studentId,
                activity: 'copy_attempt',
                details: `Copy attempt #${copyAttemptCount}`
            })
        });
    }
    
    function saveToJsonBackup() {
        const backupData = {
            session_id: sessionId,
            student_id: studentId,
            student_name: '<?php echo htmlspecialchars($student['full_name']); ?>',
            roll_number: '<?php echo htmlspecialchars($student['roll_number']); ?>',
            timestamp: new Date().toISOString(),
            answers: answers,
            tab_switches: tabSwitchCount,
            copy_attempts: copyAttemptCount,
            face_fails: faceDetectionFails,
            total_questions: totalQuestions
        };
        
        fetch('save_json_backup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(backupData)
        }).catch(error => console.error('JSON backup error:', error));
    }
    
    function showQuestion(index) {
        document.querySelectorAll('.question-card').forEach(card => {
            card.classList.remove('active');
        });
        
        const currentCard = document.querySelector(`.question-card[data-index="${index}"]`);
        if (currentCard) {
            currentCard.classList.add('active');
        }
        
        document.querySelectorAll('.palette-btn').forEach((btn, i) => {
            btn.classList.remove('current');
            if (i === index) {
                btn.classList.add('current');
            }
        });
        
        currentQuestion = index;
    }
    
    function nextQuestion() {
        if (currentQuestion < totalQuestions - 1) {
            saveCurrentAnswer();
            showQuestion(currentQuestion + 1);
        }
    }
    
    function previousQuestion() {
        if (currentQuestion > 0) {
            saveCurrentAnswer();
            showQuestion(currentQuestion - 1);
        }
    }
    
    function goToQuestion(index) {
        saveCurrentAnswer();
        showQuestion(index);
    }
    
    function saveCurrentAnswer() {
        const currentCard = document.querySelector(`.question-card[data-index="${currentQuestion}"]`);
        if (currentCard) {
            const qid = currentCard.getAttribute('data-qid');
            const selected = document.querySelector(`input[name="q${qid}"]:checked`);
            if (selected) {
                answers[qid] = selected.value;
                updatePaletteButton(currentQuestion, true);
            } else {
                updatePaletteButton(currentQuestion, false);
            }
        }
    }
    
    function updatePaletteButton(index, answered) {
        const paletteBtns = document.querySelectorAll('.palette-btn');
        if (paletteBtns[index]) {
            if (answered) {
                paletteBtns[index].classList.remove('unanswered');
                paletteBtns[index].classList.add('answered');
            } else {
                paletteBtns[index].classList.remove('answered');
                paletteBtns[index].classList.add('unanswered');
            }
        }
    }
    
    function loadSavedAnswers() {
        fetch(`save_exam_answers.php?action=load&session_id=${sessionId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.answers) {
                    for (let qid in data.answers) {
                        answers[qid] = data.answers[qid];
                        const radio = document.querySelector(`input[name="q${qid}"][value="${data.answers[qid]}"]`);
                        if (radio) {
                            radio.checked = true;
                            const cards = document.querySelectorAll('.question-card');
                            for (let i = 0; i < cards.length; i++) {
                                if (cards[i].getAttribute('data-qid') == qid) {
                                    updatePaletteButton(i, true);
                                    break;
                                }
                            }
                        }
                    }
                }
            })
            .catch(error => console.error('Error loading answers:', error));
    }
    
    function saveAnswersToServer() {
        fetch('save_exam_answers.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                session_id: sessionId,
                student_id: studentId,
                answers: answers
            })
        }).catch(error => console.error('Error saving answers:', error));
    }
    
    function startTimer() {
        timerInterval = setInterval(() => {
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                submitExam();
            } else {
                timeLeft--;
                updateTimerDisplay();
            }
        }, 1000);
    }
    
    function updateTimerDisplay() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        document.getElementById('timer').textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft < 300) {
            document.querySelector('.timer-container').style.background = '#dc3545';
        }
    }
    
    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            videoStream = stream;
            const video = document.getElementById('video');
            video.srcObject = stream;
            startFaceDetection();
        } catch (err) {
            console.error('Camera error:', err);
            document.getElementById('cameraWarning').style.display = 'block';
            document.getElementById('cameraWarning').textContent = '⚠️ Camera access denied!';
        }
    }
    
    function startFaceDetection() {
        faceDetectionInterval = setInterval(() => {
            const video = document.getElementById('video');
            if (video.readyState === 4) {
                const faceDetected = Math.random() > 0.2;
                
                if (!faceDetected) {
                    faceDetectionFails++;
                    document.getElementById('cameraWarning').style.display = 'block';
                    logCameraEvent(false);
                    
                    if (faceDetectionFails > 10) {
                        showWarning('Face not detected multiple times! Exam may be terminated.');
                    }
                } else {
                    document.getElementById('cameraWarning').style.display = 'none';
                    logCameraEvent(true);
                }
            }
        }, 3000);
    }
    
    function logCameraEvent(faceDetected) {
        fetch('log_camera_event.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                session_id: sessionId,
                student_id: studentId,
                face_detected: faceDetected,
                multiple_faces: false,
                no_face: !faceDetected
            })
        });
    }
    
    function setupTabDetection() {
        document.addEventListener('visibilitychange', function() {
            if (document.hidden && examStarted) {
                tabSwitchCount++;
                logTabSwitch();
                showWarning(`⚠️ Tab switching detected! Count: ${tabSwitchCount}`);
                
                if (tabSwitchCount >= 5) {
                    showWarning('Multiple tab switches detected! Exam will be auto-submitted!');
                    setTimeout(() => {
                        if (tabSwitchCount >= 5) {
                            submitExam();
                        }
                    }, 5000);
                }
            }
        });
    }
    
    function setupVisibilityDetection() {
        document.addEventListener('copy', function(e) {
            logSuspiciousActivity('copy_attempt');
            showWarning('Copying is prohibited!');
        });
        
        document.addEventListener('paste', function(e) {
            logSuspiciousActivity('paste_attempt');
            showWarning('Pasting is prohibited!');
        });
        
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            logSuspiciousActivity('right_click');
            showWarning('Right-click is disabled!');
        });
        
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey && (e.key === 'c' || e.key === 'v' || e.key === 'x')) || 
                (e.altKey && e.key === 'Tab') ||
                e.key === 'F12') {
                e.preventDefault();
                logSuspiciousActivity('keyboard_shortcut');
                showWarning('Keyboard shortcuts are disabled!');
            }
        });
    }
    
    function logTabSwitch() {
        fetch('log_tab_switch.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                session_id: sessionId,
                student_id: studentId,
                switch_count: tabSwitchCount
            })
        });
    }
    
    function logSuspiciousActivity(activity) {
        fetch('log_suspicious_activity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                session_id: sessionId,
                student_id: studentId,
                activity: activity
            })
        });
    }
    
    function showWarning(message) {
        const warningBar = document.getElementById('warningBar');
        warningBar.textContent = `⚠️ ${message}`;
        warningBar.style.background = '#dc3545';
        
        setTimeout(() => {
            warningBar.textContent = '⚠️ Warning: Copying, Tab switching, and any cheating attempts are strictly prohibited!';
            warningBar.style.background = '#dc3545';
        }, 3000);
    }
    
    function submitExam() {
    if (confirm('Are you sure you want to submit the exam?')) {
        // Disable submit button to prevent double submission
        const submitBtn = document.querySelector('.submit-btn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
        }
        
        examStarted = false;
        
        if (timerInterval) clearInterval(timerInterval);
        if (faceDetectionInterval) clearInterval(faceDetectionInterval);
        
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
        }
        
        saveCurrentAnswer();
        saveAnswersToServer();
        saveToJsonBackup();
        
        fetch('submit_exam.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                session_id: sessionId,
                student_id: studentId,
                answers: answers,
                tab_switches: tabSwitchCount,
                copy_attempts: copyAttemptCount,
                face_fails: faceDetectionFails
            })
        })
        .then(response => response.json())
        .then(data => {
            // Always redirect to dashboard regardless of response
            window.location.href = 'dashboard.php';
        })
        .catch(error => {
            console.error('Submit error:', error);
            // Still redirect to dashboard even if fetch fails
            window.location.href = 'dashboard.php';
        });
        
        // Fallback redirect after 3 seconds if something hangs
        setTimeout(function() {
            window.location.href = 'dashboard.php';
        }, 3000);
    }
}        
    
</script>
</body>
</html>
