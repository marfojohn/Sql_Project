<?php
session_start();
header('Content-Type: application/json');

unset($_SESSION['quiz_current_question']);
unset($_SESSION['quiz_score']);
unset($_SESSION['quiz_time_left']);
unset($_SESSION['answered_questions']);

echo json_encode(['success' => true]);
?>