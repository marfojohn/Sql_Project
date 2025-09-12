<?php
/**
 * Project: SQL Master Web App
 * Author: [John Kusi Marfo]
 * Internship: NIT Open Labs Ghana
 * Description: Built to help students practice SQL queries with
 *              real-time checking and scoring system.
 */

// Include database configuration
require_once 'config/database.php';

session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Initialize answered questions array if not set
if (!isset($_SESSION['answered_questions'])) {
    $_SESSION['answered_questions'] = [];
}

// Fetch questions from database or use stored ones
if (!isset($_SESSION['quiz_questions']) || isset($_GET['new_quiz'])) {
    // Clear any existing quiz questions if starting a new quiz
    if (isset($_GET['new_quiz'])) {
        unset($_SESSION['quiz_questions']);
        unset($_SESSION['quiz_start_time']);
        unset($_SESSION['answered_questions']); // Clear answered questions on new quiz
        // Also clear any saved state from localStorage via JavaScript later
    }
    
    try {
        $pdo = getDBConnection();
        
        // Get all questions grouped by difficulty
        $stmt = $pdo->query("SELECT question_id, question_text, correct_query, difficulty FROM questions");
        $allQuestions = $stmt->fetchAll();
        
        // Separate questions by difficulty
        $basicQuestions = [];
        $intermediateQuestions = [];
        $advancedQuestions = [];
        
        foreach ($allQuestions as $question) {
            $difficulty = strtolower($question['difficulty']);
            if ($difficulty === 'basic') {
                $basicQuestions[] = $question;
            } elseif ($difficulty === 'intermediate') {
                $intermediateQuestions[] = $question;
            } elseif ($difficulty === 'advanced') {
                $advancedQuestions[] = $question;
            }
        }
        
        // Randomly select questions from each category
        shuffle($basicQuestions);
        shuffle($intermediateQuestions);
        shuffle($advancedQuestions);
        
        // Select the required number of questions from each category
        $selectedQuestions = array_merge(
            array_slice($basicQuestions, 0, 4),
            array_slice($intermediateQuestions, 0, 3),
            array_slice($advancedQuestions, 0, 3)
        );
        
        // Shuffle the selected questions to mix difficulties
        shuffle($selectedQuestions);
        
        // Format questions for frontend
        $formattedQuestions = [];
        foreach ($selectedQuestions as $question) {
            $formattedQuestions[] = [
                'id' => $question['question_id'],
                'text' => $question['question_text'],
                'solution' => $question['correct_query'],
                'difficulty' => strtolower($question['difficulty'])
            ];
        }
        
        // Store questions in session
        $_SESSION['quiz_questions'] = $formattedQuestions;
        $_SESSION['quiz_start_time'] = time();
        $_SESSION['answered_questions'] = []; // Reset answered questions
        
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        $formattedQuestions = [];
    }
} else {
    // Use stored questions from session
    $formattedQuestions = $_SESSION['quiz_questions'];
}

// Convert to JSON for JavaScript usage
$questions_json = json_encode($formattedQuestions);

// Store quiz results in database for review
if (isset($_POST['quiz_completed'])) {
    $student_id = $_SESSION['student_id'];
    $score = $_POST['score'];
    $total_questions = $_POST['total_questions'];
    $time_taken = $_POST['time_taken'];
    $user_answers = json_decode($_POST['user_answers'], true);
    $question_ids = json_decode($_POST['question_ids'], true);
    
    try {
        $pdo = getDBConnection();
        
        // Start transaction
        $pdo->beginTransaction();
        
        // Store quiz attempt
        $stmt = $pdo->prepare("INSERT INTO quiz_attempts (student_id, score, total_questions, time_taken) VALUES (?, ?, ?, ?)");
        $stmt->execute([$student_id, $score, $total_questions, $time_taken]);
        $attempt_id = $pdo->lastInsertId();
        
        // Store each answer
        $stmt = $pdo->prepare("INSERT INTO quiz_answers (attempt_id, question_id, user_answer, is_correct) VALUES (?, ?, ?, ?)");
        
        foreach ($user_answers as $index => $user_answer) {
            $question_id = $question_ids[$index];
            
            // Check if answer is correct by comparing with correct query from database
            $correct_query = getCorrectQuery($pdo, $question_id);
            
            // If user didn't answer, mark as incorrect
            if (empty(trim($user_answer))) {
                $is_correct = false;
            } else {
                $is_correct = compareQueries($user_answer, $correct_query);
            }
            
            $stmt->execute([$attempt_id, $question_id, $user_answer, $is_correct]);
        }
        
        // Commit transaction
        $pdo->commit();
        
        // Store attempt ID in session for review page
        $_SESSION['last_attempt_id'] = $attempt_id;
        
        // Clear quiz questions from session
        unset($_SESSION['quiz_questions']);
        unset($_SESSION['quiz_start_time']);
        unset($_SESSION['answered_questions']);
        
        // Redirect to review page
        header("Location: review.php");
        exit();
        
    } catch (PDOException $e) {
        // Rollback transaction on error
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        $error = "Database error: " . $e->getMessage();
    }
}

    // Helper function to get the correct query for a question
    function getCorrectQuery($pdo, $question_id) {
        $stmt = $pdo->prepare("SELECT correct_query FROM questions WHERE question_id = ?");
        $stmt->execute([$question_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['correct_query'] : '';
    }

    // Helper function to get question difficulty
    function getQuestionDifficulty($pdo, $question_id) {
        $stmt = $pdo->prepare("SELECT difficulty FROM questions WHERE question_id = ?");
        $stmt->execute([$question_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? strtolower($result['difficulty']) : 'basic';
    }

    // Helper function to compare two SQL queries by their result sets
    // Helper function to compare two SQL queries by their result sets
function compareQueries($user_query, $correct_query) {
    try {
        $pdo = getDBConnection();

        // Execute user query
        $user_stmt = $pdo->query($user_query);
        $user_result = $user_stmt ? $user_stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        
        // Execute correct query
        $correct_stmt = $pdo->query($correct_query);
        $correct_result = $correct_stmt ? $correct_stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        
        // Normalize both result sets for comparison
        $normalized_user = normalizeResultSet($user_result);
        $normalized_correct = normalizeResultSet($correct_result);
        
        // Compare the normalized result sets
        return compareResultSets($normalized_user, $normalized_correct);
        
    } catch (PDOException $e) {
        return false;
    }
}

// Normalize a result set by sorting rows and columns
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

// Compare two normalized result sets
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Master - Query Practice Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/eclipse.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/hint/show-hint.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --success: #4cc9f0;
            --warning: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --bg-light: #ffffff;
            --bg-dark: #121212;
            --text-light: #212529;
            --text-dark: #f8f9fa;
            --card-light: #ffffff;
            --card-dark: #1e1e1e;
            --shadow: rgba(0, 0, 0, 0.1);
            --shadow-dark: rgba(0, 0, 0, 0.3);
            --terminal-bg: #1e1e1e;
            --terminal-text: #f0f0f0;
            --terminal-prompt: #00ff00;
            --terminal-border: #333;
            --correct-bg: #d4edda;
            --correct-bg-dark: #155724;
            --incorrect-bg: #f8d7da;
            --incorrect-bg-dark: #721c24;
            --transition-speed: 0.3s;
        }

        .dark-mode {
            --bg-light: var(--bg-dark);
            --text-light: var(--text-dark);
            --card-light: var(--card-dark);
            --shadow: var(--shadow-dark);
            --correct-bg: var(--correct-bg-dark);
            --incorrect-bg: var(--incorrect-bg-dark);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color var(--transition-speed), color var(--transition-speed), border-color var(--transition-speed);
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-light);
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 15px 0;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px var(--shadow);
            position: relative;
            animation: slideInDown 0.5s ease;
        }

        .header-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding: 0 15px;
            gap: 15px;
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .logo i {
            margin-right: 8px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .user-name {
            font-weight: 500;
            font-size: 14px;
        }

        .theme-toggle {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 5px 10px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .logout-btn {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 14px;
        }

        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        .timer {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: bold;
            min-width: 85px;
            text-align: center;
        }

        .timer.warning {
            background-color: var(--warning);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 992px) {
            .main-content {
                grid-template-columns: 1fr 1fr;
            }
        }

        .card {
            background-color: var(--card-light);
            border-radius: 10px;
            box-shadow: 0 4px 6px var(--shadow);
            padding: 20px;
            margin-bottom: 20px;
            animation: fadeInUp 0.5s ease;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--primary);
            display: flex;
            align-items: center;
        }

        .card-title i {
            margin-right: 10px;
        }

        .question-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .question-item.answered {
            background-color: #d4edda;
        }
        
        .question-item.answered.correct {
            background-color: #d4edda;
        }
        
        .question-item.answered.incorrect {
            background-color: #f8d7da;
        }
        
        .dark-mode .question-item.answered.correct {
            background-color: #155724;
        }
        
        .dark-mode .question-item.answered.incorrect {
            background-color: #721c24;
        }

        .difficulty {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .difficulty.easy {
            background-color: #e8f5e9;
            color: #4caf50;
        }

        .difficulty.medium {
            background-color: #fff8e1;
            color: #ff9800;
        }

        .difficulty.hard {
            background-color: #ffebee;
            color: #f44336;
        }

        .question-text {
            margin-bottom: 20px;
            line-height: 1.6;
            font-size: 16px;
        }

        .schema-container {
            background-color: var(--bg-light);
            border-left: 4px solid var(--primary);
            padding: 15px;
            border-radius: 0 5px 5px 0;
            margin-bottom: 20px;
            overflow-x: auto;
        }

        .schema-image {
            width: 100%;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Terminal Styling */
        .terminal-container {
            background-color: var(--terminal-bg);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--terminal-border);
        }

        .terminal-header {
            background-color: #363636;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--terminal-border);
        }

        .terminal-controls {
            display: flex;
            gap: 8px;
        }

        .terminal-control {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .control-close {
            background-color: #ff5f56;
        }

        .control-minimize {
            background-color: #ffbd2e;
        }

        .control-maximize {
            background-color: #27c93f;
        }

        .terminal-title {
            color: #aaa;
            font-size: 13px;
            font-family: monospace;
        }

        .editor-container {
            position: relative;
        }

        .CodeMirror {
            height: auto;
            font-family: 'Fira Code', 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 14px;
            line-height: 1.5;
            padding: 10px 0;
        }

        .CodeMirror-scroll {
            min-height: 150px;
        }

        .terminal-prompt {
            color: var(--terminal-prompt);
            font-family: monospace;
            margin-right: 5px;
            user-select: none;
        }

        .button-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px var(--shadow);
        }

        .btn-secondary {
            background-color: #e2e6ea;
            color: var(--dark);
            padding: 6px 12px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-secondary:hover {
            background-color: #dae0e5;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px var(--shadow);
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px var(--shadow);
        }

        .btn-warning {
            background-color: #ffc107;
            color: var(--dark);
        }

        .btn-warning:hover {
            background-color: #e0a800;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px var(--shadow);
        }

        .result-container {
            margin-top: 20px;
        }

        .table-container {
            overflow-x: auto;
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 400px;
        }

        th, td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        th {
            background-color: var(--bg-light);
            position: sticky;
            top: 0;
        }

        tr:hover {
            background-color: var(--bg-light);
        }

        .score-container {
            text-align: center;
            padding: 15px;
            background-color: var(--bg-light);
            border-radius: 10px;
            margin-top: 20px;
            animation: fadeIn 0.5s ease;
        }

        .score {
            font-size: 36px;
            font-weight: bold;
            color: var(--primary);
        }

        .progress-container {
            margin: 20px 0;
        }

        .progress-bar {
            height: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress {
            height: 100%;
            background-color: var(--primary);
            border-radius: 5px;
            transition: width 0.3s ease;
        }

        .question-nav {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            gap: 10px;
        }

        .feedback {
            padding: 12px;
            border-radius: 5px;
            margin-top: 20px;
            display: none;
            font-size: 14px;
            animation: fadeIn 0.5s ease;
        }

        .feedback.success {
            background-color: var(--correct-bg);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .feedback.error {
            background-color: var(--incorrect-bg);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 15px;
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background-color: var(--card-light);
            padding: 25px;
            border-radius: 10px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            animation: slideInUp 0.3s ease;
        }

        .modal h2 {
            margin-bottom: 15px;
            color: var(--primary);
            font-size: 24px;
        }
        
        .btn-secondary {
            background-color: #e2e6ea;
            color: var(--dark);
            padding: 6px 12px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-secondary:hover {
            background-color: #dae0e5;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px var(--shadow);
        }

        .modal p {
            margin-bottom: 15px;
            font-size: 16px;
        }

        .highlight {
            background-color: #fff8e1;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
        }

        .dark-mode .highlight {
        color: #000; /* force black text in dark mode */
    }
        
        /* New styles */
        .loading {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }
        
        .query-error {
            color: #dc3545;
            padding: 10px;
            background-color: #f8d7da;
            border-radius: 5px;
            margin-top: 10px;
            border-left: 4px solid #dc3545;
            font-size: 14px;
        }
        
        .result-summary {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #e9ecef;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .table-scroll {
            overflow-x: auto;
            max-height: 300px;
            overflow-y: auto;
        }
        
        .text-center {
            text-align: center;
        }

        .warning-text {
            color: #dc3545;
            font-weight: bold;
            padding: 10px;
            background-color: #fff3cd;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }

        .end-quiz-container {
            margin-top: 20px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .questions-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .question-item {
            padding: 10px;
            background-color: var(--bg-light);
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            box-shadow: 0 2px 4px var(--shadow);
        }

        .question-item:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .question-item.active {
            background-color: var(--primary);
            color: white;
            transform: scale(1.05);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInUp {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInDown {
            from { 
                opacity: 0;
                transform: translateY(-20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 576px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .user-info {
                width: 100%;
                justify-content: space-between;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
            
            .question-nav {
                flex-direction: column;
            }
            
            .questions-list {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-database"></i> SQL Master
                </div>
                <div class="user-info">
                    <span class="user-name">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <button class="theme-toggle" id="theme-toggle">
                        <i class="fas fa-moon"></i>
                    </button>
                    <a href="quiz.php?new_quiz=true" class="btn btn-secondary" id="new-quiz-btn">New Quiz</a>
                    <button class="logout-btn" id="logout-btn">Logout</button>
                    <div class="timer" id="timer">30:00</div>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="main-content">
            <div class="left-panel">
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-question-circle"></i> Question
                    </div>
                    <div class="question-info">
                        <div>Question <span id="question-number">1</span> of <span id="total-questions"><?php echo count($formattedQuestions); ?></span></div>
                        <div class="difficulty easy" id="question-difficulty">Easy</div>
                    </div>
                    <div class="question-text" id="question-text">
                        Loading question...
                    </div>
                    
                    <div class="schema-container">
                        <strong>Database Schema:</strong><br>
                        <img src="New Data .png" alt="Database Schema" class="schema-image">
                    </div>
                    
                    <!-- Terminal-style SQL Editor -->
                    <div class="terminal-container">
                        <div class="terminal-header">
                            <div class="terminal-controls">
                                <div class="terminal-control control-close"></div>
                                <div class="terminal-control control-minimize"></div>
                                <div class="terminal-control control-maximize"></div>
                            </div>
                            <div class="terminal-title">SQL Terminal</div>
                            <div style="width: 60px;"></div> <!-- Spacer for balance -->
                        </div>
                        <div class="editor-container">
                            <textarea id="sql-editor" style="display: none;"></textarea>
                        </div>
                    </div>
                    
                    <div class="button-group">
                        <button class="btn btn-primary" id="run-query">Run Query</button>
                        <button class="btn btn-primary" id="reset-editor">Clear</button>
                        <button class="btn btn-success" id="submit-answer">Submit Answer</button>
                    </div>
                    
                    <div class="feedback success" id="feedback-success">
                        <i class="fas fa-check-circle"></i> Correct! Well done.
                    </div>
                    <div class="feedback error" id="feedback-error">
                        <i class="fas fa-times-circle"></i> Incorrect. Please try again.
                    </div>

                    <div class="end-quiz-container">
                        <button class="btn btn-warning" id="end-quiz">End Quiz</button>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-table"></i> Result
                    </div>
                    <div class="table-container" id="result-container">
                        <p class="text-center">Your query results will appear here</p>
                    </div>
                </div>
            </div>
            
            <div class="right-panel">
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-chart-bar"></i> Progress
                    </div>
                    <div class="progress-container">
                        <div class="progress-bar">
                            <div class="progress" id="progress-bar" style="width: 10%;"></div>
                        </div>
                        <div class="text-center" id="progress-text">1/<?php echo count($formattedQuestions); ?> questions completed</div>
                    </div>
                    
                    <div class="score-container">
                        <div>Your Score</div>
                        <div class="score" id="score">0</div>
                        <div>points</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-list-ol"></i> Questions
                    </div>
                    <div class="question-nav">
                        <button class="btn btn-primary" id="prev-question">Previous</button>
                        <button class="btn btn-primary" id="next-question">Next</button>
                    </div>
                    
                    <div class="questions-list" id="questions-list">
                        <?php
                        // Generate question list items
                        if (!empty($formattedQuestions)) {
                            foreach ($formattedQuestions as $index => $question) {
                                $difficultyClass = $question['difficulty'];
                                $activeClass = $index === 0 ? 'active' : '';
                                echo "<div class='question-item {$activeClass}' data-index='{$index}'>" . 
                                     ($index + 1) . ". " . 
                                     ucfirst($difficultyClass) .
                                     "</div>";
                            }
                        } else {
                            echo "<div class='question-item active'>1. Sample Question</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="completion-modal">
        <div class="modal-content">
            <h2>Quiz Completed!</h2>
            <p>You've answered <span id="completed-count">0</span> out of <span class="highlight"><?php echo count($formattedQuestions); ?></span> questions</p>
            <p>Your score: <span class="highlight" id="final-score">0</span> points</p>
            <p>Time taken: <span class="highlight" id="time-taken">30:00</span></p>
            <button class="btn btn-primary" id="review-answers">Review Answers</button>
            <button class="btn btn-secondary" id="try-again">Try Again</button>
        </div>
    </div>

    <div class="modal" id="confirm-end-modal">
        <div class="modal-content">
            <h2>End Quiz Early?</h2>
            <p>You've answered <span id="answered-count">0</span> out of <span class="highlight"><?php echo count($formattedQuestions); ?></span> questions</p>
            <p>Your current score: <span class="highlight" id="current-score">0</span> points</p>
            <p class="warning-text"><i class="fas fa-exclamation-triangle"></i> Any unanswered questions will be marked as incorrect.</p>
            <p>Are you sure you want to end the quiz early?</p>
            <div class="modal-buttons">
                <button class="btn btn-primary pr-5" id="cancel-end">Cancel</button>
                <button class="btn btn-danger" id="confirm-end">Yes, End Quiz</button>
            </div>
        </div>
    </div>


    <!-- CodeMirror and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/sql/sql.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/matchbrackets.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/hint/show-hint.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/hint/sql-hint.min.js"></script>

    <script>
    // Pass PHP data to JavaScript
    const questions = <?php echo $questions_json; ?>;
    const totalQuestions = <?php echo count($formattedQuestions); ?>;
    const answeredQuestions = <?php echo json_encode($_SESSION['answered_questions'] ?? []); ?>;

    // Global variables
    let currentQuestionIndex = 0;
    let score = 0;
    let userAnswers = {};
    let timeLeft = 1800; // 30 minutes in seconds
    let timerInterval;
    let codeEditor;
    let quizStartTime = Date.now();

    // DOM elements
    const timerEl = document.getElementById('timer');
    const questionNumberEl = document.getElementById('question-number');
    const totalQuestionsEl = document.getElementById('total-questions');
    const questionDifficultyEl = document.getElementById('question-difficulty');
    const questionTextEl = document.getElementById('question-text');
    const sqlEditorEl = document.getElementById('sql-editor');
    const runQueryBtn = document.getElementById('run-query');
    const resetEditorBtn = document.getElementById('reset-editor');
    const submitAnswerBtn = document.getElementById('submit-answer');
    const resultContainerEl = document.getElementById('result-container');
    const progressBarEl = document.getElementById('progress-bar');
    const progressTextEl = document.getElementById('progress-text');
    const scoreEl = document.getElementById('score');
    const prevQuestionBtn = document.getElementById('prev-question');
    const nextQuestionBtn = document.getElementById('next-question');
    const feedbackSuccessEl = document.getElementById('feedback-success');
    const feedbackErrorEl = document.getElementById('feedback-error');
    const completionModal = document.getElementById('completion-modal');
    const finalScoreEl = document.getElementById('final-score');
    const timeTakenEl = document.getElementById('time-taken');
    const completedCountEl = document.getElementById('completed-count');
    const reviewAnswersBtn = document.getElementById('review-answers');
    const tryAgainBtn = document.getElementById('try-again');
    const questionsListEl = document.getElementById('questions-list');
    const endQuizBtn = document.getElementById('end-quiz');
    const confirmEndModal = document.getElementById('confirm-end-modal');
    const cancelEndBtn = document.getElementById('cancel-end');
    const confirmEndBtn = document.getElementById('confirm-end');
    const answeredCountEl = document.getElementById('answered-count');
    const currentScoreEl = document.getElementById('current-score');
    const logoutBtn = document.getElementById('logout-btn');
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = themeToggle.querySelector('i');

    // Initialize CodeMirror editor
    function initCodeEditor() {
        // Make sure to clear any existing editor first
        if (codeEditor) {
            codeEditor.toTextArea();
        }
        
        codeEditor = CodeMirror.fromTextArea(sqlEditorEl, {
            mode: "text/x-sql",
            theme: "monokai",
            indentWithTabs: true,
            smartIndent: true,
            lineNumbers: true,
            matchBrackets: true,
            autofocus: true,
            extraKeys: {
                "Ctrl-Space": "autocomplete",
                "Ctrl-Enter": function() { runQuery(); },
                "Ctrl-/": "toggleComment",
                "Tab": "indentMore"
            },
            hintOptions: {
                tables: {
                    users: ["id", "name", "email", "password", "created_at"],
                    products: ["id", "name", "price", "description", "category_id", "created_at"],
                    categories: ["id", "name", "description"],
                    orders: ["id", "user_id", "total_amount", "status", "created_at"],
                    order_items: ["id", "order_id", "product_id", "quantity", "price"]
                }
            }
        });
        
        // Set editor height
        codeEditor.setSize("100%", "auto");
        codeEditor.refresh();
    }

   // Initialize the quiz
    function initQuiz() {
        console.log("Initializing quiz with", questions.length, "questions");
        initTheme();
        initCodeEditor();
        
        // Check if we need to clear localStorage (new quiz)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('new_quiz')) {
            localStorage.removeItem('sqlQuizState');
        }
        
        // Load saved state if available
        loadQuizState();
        
        startTimer();
        updateProgress();
        
        // Add event listeners
        addEventListeners();
    }

    // Load saved quiz state from localStorage
    function loadQuizState() {
        // Load saved state if available
        const savedState = localStorage.getItem('sqlQuizState');
        if (savedState) {
            try {
                const state = JSON.parse(savedState);
                currentQuestionIndex = state.currentQuestionIndex || 0;
                score = state.score || 0;
                userAnswers = state.userAnswers || {};
                timeLeft = state.timeLeft || 1800;
                quizStartTime = state.quizStartTime || Date.now();
                
                console.log("Loaded saved state:", state);
            } catch (e) {
                console.error("Error loading saved state:", e);
                // Reset to defaults if there's an error
                currentQuestionIndex = 0;
                score = 0;
                userAnswers = {};
                timeLeft = 1800;
                quizStartTime = Date.now();
            }
        }
        
        // Update UI with loaded state
        scoreEl.textContent = score;
        loadQuestion(currentQuestionIndex);
        updateTimerDisplay();
    }


    // Save quiz state to localStorage
    function saveQuizState() {
        const state = {
            currentQuestionIndex: currentQuestionIndex,
            score: score,
            userAnswers: userAnswers,
            answeredQuestions: answeredQuestions, // Save answered questions
            timeLeft: timeLeft,
            quizStartTime: quizStartTime
        };
        
        localStorage.setItem('sqlQuizState', JSON.stringify(state));
    }

    // Load saved quiz state from localStorage
    function loadQuizState() {
        // Load saved state if available
        const savedState = localStorage.getItem('sqlQuizState');
        if (savedState) {
            try {
                const state = JSON.parse(savedState);
                currentQuestionIndex = state.currentQuestionIndex || 0;
                score = state.score || 0;
                userAnswers = state.userAnswers || {};
                answeredQuestions = state.answeredQuestions || []; // Load answered questions
                timeLeft = state.timeLeft || 1800;
                quizStartTime = state.quizStartTime || Date.now();
                
                console.log("Loaded saved state:", state);
            } catch (e) {
                console.error("Error loading saved state:", e);
                // Reset to defaults if there's an error
                currentQuestionIndex = 0;
                score = 0;
                userAnswers = {};
                answeredQuestions = [];
                timeLeft = 1800;
                quizStartTime = Date.now();
            }
        }
        
        // Update UI with loaded state
        scoreEl.textContent = score;
        loadQuestion(currentQuestionIndex);
        updateTimerDisplay();
        
        // Update question items styling based on answered status
        updateQuestionItemsStyling();
    }

    // Update question items styling based on answered status
    function updateQuestionItemsStyling() {
        const questionItems = document.querySelectorAll('.question-item');
        questionItems.forEach((item, index) => {
            const questionId = questions[index].id;
            if (answeredQuestions.includes(questionId)) {
                // Check if the answer was correct by verifying the user's answer
                // For simplicity, we'll assume it was correct if it's in answeredQuestions
                // In a real implementation, you might want to track correct/incorrect separately
                item.classList.add('answered', 'correct');
            }
        });
    }

    // Redirect to review page
    function redirectToReview() {
        // Calculate time taken
        const timeTaken = Math.floor((Date.now() - quizStartTime) / 1000);
        const minutes = Math.floor(timeTaken / 60);
        const seconds = timeTaken % 60;
        const timeTakenStr = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        // Prepare question IDs for storage
        const questionIds = {};
        questions.forEach((question, index) => {
            questionIds[index] = question.id; // Store the actual question ID from database
        });
        
        // Mark unanswered questions as empty strings
        for (let i = 0; i < questions.length; i++) {
            if (!userAnswers.hasOwnProperty(i)) {
                userAnswers[i] = ""; // Mark unanswered questions as empty
            }
        }
        
        // Create a form to submit the quiz data to store in database
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'quiz.php';
        
        const answersInput = document.createElement('input');
        answersInput.type = 'hidden';
        answersInput.name = 'user_answers';
        answersInput.value = JSON.stringify(userAnswers);
        form.appendChild(answersInput);
        
        const questionIdsInput = document.createElement('input');
        questionIdsInput.type = 'hidden';
        questionIdsInput.name = 'question_ids';
        questionIdsInput.value = JSON.stringify(questionIds);
        form.appendChild(questionIdsInput);
        
        const scoreInput = document.createElement('input');
        scoreInput.type = 'hidden';
        scoreInput.name = 'score';
        scoreInput.value = score;
        form.appendChild(scoreInput);
        
        const totalQuestionsInput = document.createElement('input');
        totalQuestionsInput.type = 'hidden';
        totalQuestionsInput.name = 'total_questions';
        totalQuestionsInput.value = totalQuestions;
        form.appendChild(totalQuestionsInput);
        
        const timeTakenInput = document.createElement('input');
        timeTakenInput.type = 'hidden';
        timeTakenInput.name = 'time_taken';
        timeTakenInput.value = timeTakenStr;
        form.appendChild(timeTakenInput);
        
        const completedInput = document.createElement('input');
        completedInput.type = 'hidden';
        completedInput.name = 'quiz_completed';
        completedInput.value = 'true';
        form.appendChild(completedInput);
        
        document.body.appendChild(form);
        form.submit();
    }

    // Add all event listeners
    function addEventListeners() {
        runQueryBtn.addEventListener('click', runQuery);
        
        resetEditorBtn.addEventListener('click', () => {
            codeEditor.setValue('');
            resultContainerEl.innerHTML = '<p class="text-center">Your query results will appear here</p>';
        });
        
        submitAnswerBtn.addEventListener('click', submitAnswer);
        
        prevQuestionBtn.addEventListener('click', () => {
            if (currentQuestionIndex > 0) {
                currentQuestionIndex--;
                loadQuestion(currentQuestionIndex);
                saveQuizState();
            }
        });
        
        nextQuestionBtn.addEventListener('click', () => {
            if (currentQuestionIndex < questions.length - 1) {
                currentQuestionIndex++;
                loadQuestion(currentQuestionIndex);
                saveQuizState();
            }
        });
        
        // Click on question items to navigate
        document.querySelectorAll('.question-item').forEach(item => {
            item.addEventListener('click', () => {
                const index = parseInt(item.getAttribute('data-index'));
                if (!isNaN(index) && index >= 0 && index < questions.length) {
                    currentQuestionIndex = index;
                    loadQuestion(currentQuestionIndex);
                    saveQuizState();
                }
            });
        });
        
        reviewAnswersBtn.addEventListener('click', () => {
            completionModal.style.display = 'none';
            redirectToReview();
        });
        
        tryAgainBtn.addEventListener('click', () => {
            // Redirect to start a new quiz
            window.location.href = 'quiz.php?new_quiz=true';
        });
        
        // End quiz functionality
        endQuizBtn.addEventListener('click', () => {
            answeredCountEl.textContent = Object.keys(userAnswers).length;
            currentScoreEl.textContent = score;
            confirmEndModal.style.display = 'flex';
        });
        
        cancelEndBtn.addEventListener('click', () => {
            confirmEndModal.style.display = 'none';
        });
        
        confirmEndBtn.addEventListener('click', () => {
            confirmEndModal.style.display = 'none';
            finishQuiz();
        });
        
        // Logout functionality
        logoutBtn.addEventListener('click', () => {
            if (confirm('Are you sure you want to logout? Your progress will be saved.')) {
                // Save state before logging out
                saveQuizState();
                window.location.href = 'logout.php';
            }
        });
        
        // Save state before page unload
        window.addEventListener('beforeunload', () => {
            saveQuizState();
        });
    }

    // Load a question
    function loadQuestion(index) {
        if (questions.length === 0) {
            questionTextEl.textContent = "No questions available. Please check your database connection.";
            return;
        }
        
        const question = questions[index];
        questionNumberEl.textContent = index + 1;
        questionTextEl.textContent = question.text;
        
        // Update difficulty
        questionDifficultyEl.textContent = question.difficulty.charAt(0).toUpperCase() + question.difficulty.slice(1);
        questionDifficultyEl.className = 'difficulty ' + question.difficulty;
        
        // Load user's previous answer if exists
        if (userAnswers[index]) {
            codeEditor.setValue(userAnswers[index]);
        } else {
            codeEditor.setValue('');
        }
        
        // Refresh editor to update display
        codeEditor.refresh();
        
        // Hide feedback
        feedbackSuccessEl.style.display = 'none';
        feedbackErrorEl.style.display = 'none';
        
        // Clear result container
        resultContainerEl.innerHTML = '<p class="text-center">Your query results will appear here</p>';
        
        // Update navigation buttons
        prevQuestionBtn.disabled = (index === 0);
        nextQuestionBtn.disabled = (index === questions.length - 1);
        
        // Update question list highlighting
        document.querySelectorAll('.question-item').forEach((item, i) => {
            if (i === index) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    // Start the timer
    function startTimer() {
        clearInterval(timerInterval);
        updateTimerDisplay();
        
        timerInterval = setInterval(() => {
            timeLeft--;
            updateTimerDisplay();
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                finishQuiz();
            } else if (timeLeft <= 300) { // 5 minutes left
                timerEl.classList.add('warning');
            }
            
            // Save state every 10 seconds
            if (timeLeft % 10 === 0) {
                saveQuizState();
            }
        }, 1000);
    }

    // Update timer display
    function updateTimerDisplay() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    // Update progress bar and text
    function updateProgress() {
        const answeredCount = Object.keys(userAnswers).length;
        const progressPercentage = (answeredCount / questions.length) * 100;
        
        progressBarEl.style.width = `${progressPercentage}%`;
        progressTextEl.textContent = `${answeredCount}/${questions.length} questions completed`;
    }

    // Execute the query on the server
    async function runQuery() {
        const query = codeEditor.getValue().trim();
        if (!query) {
            resultContainerEl.innerHTML = '<p class="text-center">Please enter a query first</p>';
            return;
        }
        
        // Show loading state
        const originalText = runQueryBtn.textContent;
        runQueryBtn.innerHTML = '<span class="loading"></span> Running...';
        runQueryBtn.disabled = true;
        
        try {
            console.log("Executing query:", query);
            const response = await fetch("execute_query.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ query: query })
            });
            
            const result = await response.json();
            console.log("Query result:", result);
            
            if (result.success) {
                displayQueryResults(result.columns, result.data, result.rowCount);
            } else {
                displayQueryError(result.error);
            }
        } catch (error) {
            console.error("Error executing query:", error);
            displayQueryError("Network error: Could not execute query");
        } finally {
            // Restore button state
            runQueryBtn.textContent = originalText;
            runQueryBtn.disabled = false;
        }
    }

    // Display query results in a table
    function displayQueryResults(columns, data, rowCount) {
        let html = `
            <div class="result-summary">
                <p>Query returned ${rowCount} row${rowCount !== 1 ? 's' : ''}</p>
            </div>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
        `;
        
        // Add column headers
        columns.forEach(col => {
            html += `<th>${escapeHtml(col)}</th>`;
        });
        
        html += `</tr></thead><tbody>`;
        
        // Add data rows
        if (data && data.length > 0) {
            data.forEach(row => {
                html += `<tr>`;
                columns.forEach(col => {
                    html += `<td>${escapeHtml(row[col] !== null ? row[col] : 'NULL')}</td>`;
                });
                html += `</tr>`;
            });
        } else {
            html += `<tr><td colspan="${columns.length}" class="text-center">No results found</td></tr>`;
        }
        
        html += `</tbody></table></div>`;
        
        resultContainerEl.innerHTML = html;
    }

    // Display query error
    function displayQueryError(error) {
        resultContainerEl.innerHTML = `
            <div class="query-error">
                <strong>Error:</strong> ${escapeHtml(error)}
            </div>
        `;
    }

    // Utility function to escape HTML
    function escapeHtml(unsafe) {
        if (unsafe === null || unsafe === undefined) return '';
        return unsafe.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Submit answer
    async function submitAnswer() {
        const query = codeEditor.getValue().trim();
        if (!query) {
            alert('Please write a query before submitting.');
            return;
        }
        
        // Save answer
        userAnswers[currentQuestionIndex] = query;
        saveQuizState();
        
        // Validate the answer
        await validateAnswer(query);
        
        updateProgress();
        
        // If this was the last question, finish the quiz
        if (Object.keys(userAnswers).length === questions.length) {
            setTimeout(finishQuiz, 1500);
        }
    }

    // Validate the answer against the correct query
    // Enhanced function to validate answer
        async function validateAnswer(query) {
            const question = questions[currentQuestionIndex];
            const questionId = question.id;
            
            // Check if this question has already been answered correctly
            if (answeredQuestions.includes(questionId)) {
                feedbackErrorEl.style.display = 'block';
                feedbackSuccessEl.style.display = 'none';
                feedbackErrorEl.innerHTML = '<i class="fas fa-times-circle"></i> You have already answered this question correctly.';
                
                // Restore button state
                submitAnswerBtn.textContent = 'Submit Answer';
                submitAnswerBtn.disabled = false;
                return;
            }
            
            try {
                // Show loading state
                const originalText = submitAnswerBtn.textContent;
                submitAnswerBtn.innerHTML = '<span class="loading"></span> Checking...';
                submitAnswerBtn.disabled = true;
                
                // Send both queries to server for comparison
                const response = await fetch("validate_query.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ 
                        user_query: query, 
                        correct_query: question.solution 
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    feedbackSuccessEl.style.display = 'block';
                    feedbackErrorEl.style.display = 'none';
                    
                    // Only award points if not already answered correctly
                    if (!answeredQuestions.includes(questionId)) {
                        // Award points based on difficulty
                        if (question.difficulty === 'basic') {
                            score += 10;
                        } else if (question.difficulty === 'intermediate') {
                            score += 20;
                        } else if (question.difficulty === 'advanced') {
                            score += 30;
                        }
                        
                        // Mark this question as answered correctly
                        answeredQuestions.push(questionId);
                        
                        // Update the question item styling
                        const questionItems = document.querySelectorAll('.question-item');
                        if (questionItems[currentQuestionIndex]) {
                            questionItems[currentQuestionIndex].classList.add('answered', 'correct');
                        }
                        
                        scoreEl.textContent = score;
                        saveQuizState();
                    }
                } else {
                    feedbackErrorEl.style.display = 'block';
                    feedbackSuccessEl.style.display = 'none';
                    
                    // Update the question item styling for incorrect answer
                    const questionItems = document.querySelectorAll('.question-item');
                    if (questionItems[currentQuestionIndex]) {
                        questionItems[currentQuestionIndex].classList.add('answered', 'incorrect');
                    }
                    
                    // Show specific error message if available
                    if (result.error) {
                        feedbackErrorEl.innerHTML = `<i class="fas fa-times-circle"></i> ${result.error}`;
                    }
                    
                    // Hide error message after 5 seconds
                    setTimeout(() => {
                        feedbackErrorEl.style.display = 'none';
                    }, 5000);
                }
            } catch (error) {
                console.error("Error validating answer:", error);
                feedbackErrorEl.style.display = 'block';
                feedbackSuccessEl.style.display = 'none';
                feedbackErrorEl.innerHTML = '<i class="fas fa-times-circle"></i> Error validating query. Please try again.';
                
                // Hide error message after 5 seconds
                setTimeout(() => {
                    feedbackErrorEl.style.display = 'none';
                }, 5000);
            } finally {
                // Restore button state
                submitAnswerBtn.textContent = 'Submit Answer';
                submitAnswerBtn.disabled = false;
            }
        }

    // Compare query results for validation
    function compareResults(userResult, correctResult) {
        // Simple comparison: check if column names match and data has same rows
        if (userResult.columns.length !== correctResult.columns.length) {
            return false;
        }
        
        // Check if all column names match (order matters in SQL)
        for (let i = 0; i < userResult.columns.length; i++) {
            if (userResult.columns[i] !== correctResult.columns[i]) {
                return false;
            }
        }
        
        // Check if row counts match
        if (userResult.data.length !== correctResult.data.length) {
            return false;
        }
        
        // For a more robust implementation, you might want to compare the actual data
        // This is a simple implementation that might not work for all cases
        
        return true;
    }

    // Finish the quiz (when time runs out or all questions answered)
    function finishQuiz() {
        clearInterval(timerInterval);
        
        // Calculate time taken
        const timeTaken = Math.floor((Date.now() - quizStartTime) / 1000);
        const minutes = Math.floor(timeTaken / 60);
        const seconds = timeTaken % 60;
        const timeTakenStr = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        // Update completion modal
        completedCountEl.textContent = Object.keys(userAnswers).length;
        finalScoreEl.textContent = score;
        timeTakenEl.textContent = timeTakenStr;
        
        // Show completion modal
        completionModal.style.display = 'flex';
        
        // Clear saved state
        localStorage.removeItem('sqlQuizState');
    }

        // Theme toggle functionality
        function toggleTheme() {
            const body = document.body;
            const isDarkMode = body.classList.toggle('dark-mode');
            
            // Update icon
            themeIcon.className = isDarkMode ? 'fas fa-sun' : 'fas fa-moon';
            
            // Save preference
            localStorage.setItem('darkMode', isDarkMode);
            
            // Update CodeMirror theme
            const theme = isDarkMode ? 'monokai' : 'eclipse';
            userAnswerEditor.setOption('theme', theme);
            correctAnswerEditor.setOption('theme', theme);
        }
        
        // Check for saved theme preference or respect OS preference
        function initTheme() {
            const savedTheme = localStorage.getItem('darkMode');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (savedTheme === null) {
                // No saved preference, use OS preference
                if (prefersDark) {
                    document.body.classList.add('dark-mode');
                    themeIcon.className = 'fas fa-sun';
                }
            } else if (savedTheme === 'true') {
                document.body.classList.add('dark-mode');
                themeIcon.className = 'fas fa-sun';
            }
        }
        
        // Event listeners
        prevQuestionBtn.addEventListener('click', () => {
            if (currentQuestionIndex > 0) {
                loadQuestion(currentQuestionIndex - 1);
            }
        });
        
        nextQuestionBtn.addEventListener('click', () => {
            if (currentQuestionIndex < questionIds.length - 1) {
                loadQuestion(currentQuestionIndex + 1);
            }
        });
        
        themeToggle.addEventListener('click', toggleTheme);
    
       // Add event listener for new quiz button
        document.getElementById('new-quiz-btn').addEventListener('click', function(e) {
            if (Object.keys(userAnswers).length > 0) {
                if (!confirm('Starting a new quiz will erase your current progress. Are you sure?')) {
                    e.preventDefault();
                }
            }
        }); 

        // Initialize the quiz when the page loads
        document.addEventListener('DOMContentLoaded', initQuiz);
</script>
</body>
</html>