<?php
/**
 * Project: SQL Master Web App
 * Author: [Marfo John Kusi]
 * Internship: NIT Open Labs Ghana
 * Description: Built to help students practice SQL queries with
 *              real-time checking and scoring system.
 */
// validate_query.php
require_once 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $user_query = $input['user_query'] ?? '';
    $correct_query = $input['correct_query'] ?? '';
    
    if (empty($user_query)) {
        echo json_encode(['success' => false, 'error' => 'No query provided']);
        exit;
    }
    
    try {
        $pdo = getDBConnection();
        
        // Execute user query
        $user_stmt = $pdo->query($user_query);
        if ($user_stmt === false) {
            $error = $pdo->errorInfo();
            echo json_encode(['success' => false, 'error' => 'Invalid query: ' . $error[2]]);
            exit;
        }
        $user_result = $user_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Execute correct query
        $correct_stmt = $pdo->query($correct_query);
        $correct_result = $correct_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Normalize and compare results
        $normalized_user = normalizeResultSet($user_result);
        $normalized_correct = normalizeResultSet($correct_result);
        
        $is_correct = compareResultSets($normalized_user, $normalized_correct);
        
        echo json_encode(['success' => $is_correct]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

function normalizeResultSet($result) {
    if (empty($result)) return [];
    
    // Get all column names and sort them
    $columns = array_keys($result[0]);
    sort($columns);
    
    $normalized = [];
    
    // Sort each row by column names and add to normalized array
    foreach ($result as $row) {
        $normalized_row = [];
        foreach ($columns as $col) {
            $normalized_row[$col] = $row[$col];
        }
        $normalized[] = $normalized_row;
    }
    
    // Sort the rows
    usort($normalized, function($a, $b) {
        return strcmp(serialize($a), serialize($b));
    });
    
    return $normalized;
}

function compareResultSets($set1, $set2) {
    if (count($set1) !== count($set2)) {
        return false;
    }
    
    for ($i = 0; $i < count($set1); $i++) {
        if ($set1[$i] != $set2[$i]) {
            return false;
        }
    }
    
    return true;
}
?>
