<?php
session_start();

// Database configuration
define('DB_HOST', '..com');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '##@@');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to log activity
function logActivity($pdo, $student_id, $session_id, $activity_type, $details = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (student_id, session_id, activity_type, details, created_at) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([$student_id, $session_id, $activity_type, $details]);
    } catch(Exception $e) {
        return false;
    }
}
?>
