<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

$state = [
    'current_question' => $_SESSION['quiz_state']['current_question'] ?? 0,
    'score' => $_SESSION['quiz_state']['score'] ?? 0,
    'time_left' => $_SESSION['quiz_state']['time_left'] ?? 1800
];

echo json_encode(['success' => true, 'state' => $state]);
?>