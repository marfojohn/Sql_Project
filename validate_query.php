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

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
        
        // Execute correct query with error handling
        $correct_stmt = $pdo->query($correct_query);
        if ($correct_stmt === false) {
            $error = $pdo->errorInfo();
            error_log("Correct query failed: " . $correct_query . " - Error: " . $error[2]);
            echo json_encode(['success' => false, 'error' => 'Correct query error: ' . $error[2]]);
            exit;
        }
        $correct_result = $correct_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Log for debugging (remove in production)
        error_log("User query: " . $user_query);
        error_log("Correct query: " . $correct_query);
        error_log("User result count: " . count($user_result));
        error_log("Correct result count: " . count($correct_result));
        
        // Normalize and compare results
        $normalized_user = normalizeResultSet($user_result);
        $normalized_correct = normalizeResultSet($correct_result);
        
        $is_correct = compareResultSets($normalized_user, $normalized_correct);
        
        error_log("Comparison result: " . ($is_correct ? 'MATCH' : 'NO MATCH'));
        
        echo json_encode(['success' => $is_correct]);
        
    } catch (PDOException $e) {
        error_log("PDO Exception: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        error_log("General Exception: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
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
            // Convert all values to strings for consistent comparison
            $value = $row[$col];
            if ($value === null) {
                $normalized_row[$col] = 'NULL';
            } else {
                $normalized_row[$col] = (string)$value;
            }
        }
        $normalized[] = $normalized_row;
    }
    
    // Sort the rows using a more robust comparison
    usort($normalized, function($a, $b) {
        $a_str = json_encode(array_values($a));
        $b_str = json_encode(array_values($b));
        return strcmp($a_str, $b_str);
    });
    
    return $normalized;
}

function compareResultSets($set1, $set2) {
    if (count($set1) !== count($set2)) {
        error_log("Row count mismatch: " . count($set1) . " vs " . count($set2));
        return false;
    }
    
    if (count($set1) === 0 && count($set2) === 0) {
        return true; // Both empty results are equal
    }
    
    for ($i = 0; $i < count($set1); $i++) {
        $row1 = $set1[$i];
        $row2 = $set2[$i];
        
        if (count($row1) !== count($row2)) {
            error_log("Column count mismatch in row $i: " . count($row1) . " vs " . count($row2));
            return false;
        }
        
        foreach ($row1 as $key => $value) {
            if (!array_key_exists($key, $row2)) {
                error_log("Column '$key' missing in second result set");
                return false;
            }
            
            if ($row1[$key] !== $row2[$key]) {
                error_log("Value mismatch in row $i, column '$key': '" . $row1[$key] . "' vs '" . $row2[$key] . "'");
                return false;
            }
        }
    }
    
    return true;
}