<?php
session_start();

// Clear quiz-related session variables
unset($_SESSION['quiz_questions']);
unset($_SESSION['quiz_start_time']);
unset($_SESSION['answered_questions']);
unset($_SESSION['quiz_id']);

echo json_encode(['success' => true]);
?>