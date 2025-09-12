<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answeredQuestions = json_decode($_POST['answered_questions'], true);
    if (is_array($answeredQuestions)) {
        $_SESSION['answered_questions'] = $answeredQuestions;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid data format']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>