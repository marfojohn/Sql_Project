<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answered_questions = $_POST['answered_questions'] ?? '[]';
    
    // Save to session
    $_SESSION['answered_questions'] = json_decode($answered_questions, true);
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>