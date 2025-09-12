<?php

/**
 * Project: SQL Master Web App
 * Author: [John Kusi Marfo]
 * Internship: NIT Open Labs Ghana
 * Description: Built to help students practice SQL queries with
 *              real-time checking and scoring system.
 */


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Include database configuration
require_once 'config/database.php';

try {
    $pdo = getDBConnection();
    
    // Fetch questions from database
    $stmt = $pdo->query("SELECT question_id, question_text, correct_query, difficulty FROM questions");
    $questions = $stmt->fetchAll();
    
    // Format the response
    $formattedQuestions = [];
    foreach ($questions as $question) {
        $formattedQuestions[] = [
            'id' => $question['question_id'],
            'text' => $question['question_text'],
            'solution' => $question['correct_query'],
            'difficulty' => strtolower($question['difficulty']),
            'schema' => "Employee (emp_id, first_name, last_name, birth_date, sex, salary, super_id, branch_id)\n" .
                        "Branch (branch_id, branch_name, mgr_id, mgr_start_date)\n" +
                        "Client (client_id, client_name, branch_id)\n" +
                        "Branch_Supplier (branch_id, supplier_name, supply_type)"
        ];
    }
    
    echo json_encode($formattedQuestions);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>