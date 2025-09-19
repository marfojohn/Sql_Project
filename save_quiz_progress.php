<?php
// save_quiz_progress.php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['student_id'] ?? 0;
    $progress_id = $_POST['progress_id'] ?? 0;
    $current_question = $_POST['current_question'] ?? 0;
    $score = $_POST['score'] ?? 0;
    $time_left = $_POST['time_left'] ?? 1800;
    $user_answers = $_POST['user_answers'] ?? '[]';
    $answered_questions = $_POST['answered_questions'] ?? '[]';
    
    if ($student_id && $progress_id) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("UPDATE quiz_progress SET current_question_index = ?, user_answers = ?, score = ?, time_left = ?, answered_questions = ?, updated_at = NOW() WHERE progress_id = ? AND student_id = ?");
            $result = $stmt->execute([
                $current_question,
                $user_answers,
                $score,
                $time_left,
                $answered_questions,
                $progress_id,
                $student_id
            ]);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update progress']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid student or progress ID']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>