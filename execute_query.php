<?php
/**
 * Project: SQL Master Web App
 * Author: [Marfo John Kusi]
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

// Allow only SELECT queries for security
function isSelectQuery($query) {
    $trimmed = trim(strtolower($query));
    $forbiddenKeywords = ['insert', 'update', 'delete', 'drop', 'alter', 'create', 'truncate', 'grant', 'revoke'];
    
    // Check if it starts with SELECT
    $isSelect = strpos($trimmed, 'select') === 0;
    
    // Check for any forbidden keywords
    foreach ($forbiddenKeywords as $keyword) {
        if (strpos($trimmed, $keyword) !== false && strpos($trimmed, $keyword) < 10) {
            return false;
        }
    }
    
    return $isSelect;
}

try {
    $pdo = getDBConnection();
    
    // Get the query from POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $query = $data['query'] ?? '';
    
    if (empty($query)) {
        throw new Exception('No query provided');
    }
    
    // Security check - allow only SELECT statements
    if (!isSelectQuery($query)) {
        throw new Exception('Only SELECT queries are allowed for security reasons');
    }
    
    // Execute the query
    $stmt = $pdo->query($query);
    
    // Fetch results
    $results = $stmt->fetchAll();
    
    // Get column names
    $columns = [];
    if (!empty($results)) {
        $columns = array_keys($results[0]);
    } else {
        // For empty results, try to get column info from the statement
        $columnCount = $stmt->columnCount();
        for ($i = 0; $i < $columnCount; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $columns[] = $meta['name'];
        }
    }
    
    // Return results
    echo json_encode([
        'success' => true,
        'columns' => $columns,
        'data' => $results,
        'rowCount' => count($results)
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
