<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isStudentLoggedIn() {
    return isset($_SESSION['student_id']) && isset($_SESSION['student_type']) && $_SESSION['student_type'] == 'student';
}

function isTeacherLoggedIn() {
    return isset($_SESSION['teacher_id']) && isset($_SESSION['teacher_type']) && $_SESSION['teacher_type'] == 'teacher';
}

function requireStudentLogin() {
    if (!isStudentLoggedIn()) {
        header('Location: /student/login.php');
        exit();
    }
}

function requireTeacherLogin() {
    if (!isTeacherLoggedIn()) {
        header('Location: /teacher/login.php');
        exit();
    }
}
?>