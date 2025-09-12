<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['quiz_current_question'] = $_POST['current_question'] ?? 0;
    $_SESSION['quiz_score'] = $_POST['score'] ?? 0;
    $_SESSION['quiz_time_left'] = $_POST['time_left'] ?? 1800;
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>