<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false];
    
    try {
        // Store quiz state in session
        $_SESSION['quiz_state'] = [
            'current_question' => $_POST['current_question'],
            'score' => $_POST['score'],
            'time_left' => $_POST['time_left'],
            'quiz_id' => $_POST['quiz_id'],
            'timestamp' => time()
        ];
        
        $response['success'] = true;
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}