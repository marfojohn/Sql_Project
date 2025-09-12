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

// Check if we have a quiz attempt to review
if (!isset($_SESSION['last_attempt_id'])) {
    header("Location: quiz.php");
    exit();
}

$attempt_id = $_SESSION['last_attempt_id'];
$student_id = $_SESSION['student_id'];

// Fetch quiz attempt and answers from database
try {
    $pdo = getDBConnection();
    
    // Get quiz attempt details
    $stmt = $pdo->prepare("SELECT score, total_questions, time_taken FROM quiz_attempts WHERE attempt_id = ? AND student_id = ?");
    $stmt->execute([$attempt_id, $student_id]);
    $attempt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$attempt) {
        header("Location: quiz.php");
        exit();
    }
    
    $score = $attempt['score'];
    $totalQuestions = $attempt['total_questions'];
    $timeTaken = $attempt['time_taken'];
    
    // Get answers with questions
    $stmt = $pdo->prepare("
        SELECT qa.question_id, qa.user_answer, qa.is_correct, 
               q.question_text, q.correct_query, q.difficulty 
        FROM quiz_answers qa 
        JOIN questions q ON qa.question_id = q.question_id 
        WHERE qa.attempt_id = ?
    ");
    $stmt->execute([$attempt_id]);
    $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organize data for the view
    $userAnswers = [];
    $correctness = [];
    $questions = [];
    
    foreach ($answers as $answer) {
        $question_id = $answer['question_id'];
        $userAnswers[$question_id] = $answer['user_answer'];
        $correctness[$question_id] = (bool)$answer['is_correct'];
        $questions[$question_id] = [
            'text' => $answer['question_text'],
            'solution' => $answer['correct_query'],
            'difficulty' => $answer['difficulty']
        ];
    }
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    header("Location: quiz.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Master - Review Answers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/eclipse.min.css">
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

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
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
        }

        .btn-secondary:hover {
            background-color: #dae0e5;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px var(--shadow);
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

        .score-container {
            text-align: center;
            padding: 15px;
            background-color: var(--card-light);
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px var(--shadow);
            animation: fadeIn 0.5s ease;
        }

        .score {
            font-size: 36px;
            font-weight: bold;
            color: var(--primary);
        }

        .review-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 992px) {
            .review-content {
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
            padding: 15px;
            background-color: var(--bg-light);
            border-radius: 5px;
            border-left: 4px solid var(--primary);
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

        .code-container {
            padding: 15px;
            font-family: monospace;
            white-space: pre-wrap;
            color: var(--terminal-text);
            overflow-x: auto;
            line-height: 1.5;
            min-height: 100px;
        }

        .CodeMirror {
            height: auto;
            font-family: monospace;
            font-size: 14px;
            padding: 10px;
            border-radius: 0 0 5px 5px;
        }

        .correct-answer {
            border-left: 4px solid #28a745;
        }

        .user-answer {
            border-left: 4px solid #17a2b8;
        }

        .answer-feedback {
            padding: 12px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 14px;
            animation: fadeIn 0.5s ease;
        }

        .answer-correct {
            background-color: var(--correct-bg);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .answer-incorrect {
            background-color: var(--incorrect-bg);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            gap: 10px;
        }

        .question-nav {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 30px;
            gap: 10px;
            flex-wrap: wrap;
            animation: fadeIn 0.5s ease;
        }

        .question-item {
            padding: 8px 12px;
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

        .question-item.correct {
            border: 2px solid #28a745;
        }

        .question-item.incorrect {
            border: 2px solid #dc3545;
        }

        .text-center {
            text-align: center;
        }

        .mt-20 {
            margin-top: 20px;
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

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
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
            
            .navigation {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
            
            .review-content {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-database"></i> SQL Master - Review Answers
                </div>
                <div class="user-info">
                    <span class="user-name">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <button class="theme-toggle" id="theme-toggle">
                        <i class="fas fa-moon"></i>
                    </button>
                    <a href="quiz.php" class="btn btn-secondary">Back to Quiz</a>
                    <a href="logout.php" class="btn btn-primary">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="score-container">
            <div>Your Score</div>
            <div class="score"><?php echo $score; ?></div>
            <div>points (<?php echo $totalQuestions > 0 ? round(($score / ($totalQuestions * 10)) * 100) : 0; ?>%)</div>
        </div>

        <?php if ($totalQuestions > 0): ?>
        <div class="question-nav">
            <?php 
            $index = 0;
            foreach ($userAnswers as $questionId => $userAnswer): 
                $question = $questions[$questionId] ?? null;
                $isCorrect = $correctness[$questionId] ?? false;
            ?>
                <div class="question-item <?php echo $index === 0 ? 'active' : ''; ?> <?php echo $isCorrect ? 'correct' : 'incorrect'; ?>" 
                     data-index="<?php echo $index; ?>" data-question-id="<?php echo $questionId; ?>">
                    <?php echo $index + 1; ?>
                </div>
            <?php 
                $index++;
            endforeach; 
            ?>
        </div>

        <?php 
        $firstQuestionId = array_key_first($userAnswers);
        $firstQuestion = $questions[$firstQuestionId] ?? null;
        $firstUserAnswer = $userAnswers[$firstQuestionId] ?? '';
        $firstCorrectness = $correctness[$firstQuestionId] ?? false;
        ?>

        <div class="review-content">
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-question-circle"></i> Question
                </div>
                <div class="question-info">
                    <div>Question <span id="question-number">1</span> of <span id="total-questions"><?php echo $totalQuestions; ?></span></div>
                    <?php if ($firstQuestion): ?>
                        <div class="difficulty <?php echo $firstQuestion['difficulty']; ?>"><?php echo ucfirst($firstQuestion['difficulty']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="question-text" id="question-text">
                    <?php echo $firstQuestion ? htmlspecialchars($firstQuestion['text']) : 'Question not found'; ?>
                </div>

                <div class="card-title">
                    <i class="fas fa-code"></i> Your Answer
                </div>
                <div class="terminal-container">
                    <div class="terminal-header">
                        <div class="terminal-controls">
                            <div class="terminal-control control-close"></div>
                            <div class="terminal-control control-minimize"></div>
                            <div class="terminal-control control-maximize"></div>
                        </div>
                        <div class="terminal-title">Your SQL Query</div>
                        <div style="width: 60px;"></div>
                    </div>
                    <div id="user-answer-editor" class="code-container user-answer"></div>
                </div>

                <div class="answer-feedback <?php echo $firstCorrectness ? 'answer-correct' : 'answer-incorrect'; ?>" id="answer-feedback">
                    <i class="fas <?php echo $firstCorrectness ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                    <?php echo $firstCorrectness ? 'Correct! Well done.' : 'Incorrect. See the correct answer below.'; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-check-circle"></i> Correct Answer
                </div>
                <div class="terminal-container">
                    <div class="terminal-header">
                        <div class="terminal-controls">
                            <div class="terminal-control control-close"></div>
                            <div class="terminal-control control-minimize"></div>
                            <div class="terminal-control control-maximize"></div>
                        </div>
                        <div class="terminal-title">Correct SQL Query</div>
                        <div style="width: 60px;"></div>
                    </div>
                    <div id="correct-answer-editor" class="code-container correct-answer"></div>
                </div>

                <div class="card-title mt-20">
                    <i class="fas fa-lightbulb"></i> Explanation
                </div>
                <div class="question-text">
                    <?php if ($firstQuestion): ?>
                        <p>This query <?php echo strtolower(explode(' ', $firstQuestion['text'])[0]); ?> 
                        <?php echo substr($firstQuestion['text'], strpos($firstQuestion['text'], ' ') + 1); ?>.</p>
                        
                        <p class="3"><strong>Key elements:</strong></p>
                        <ul>
                            <li>Retrieves the required data</li>
                            <li>Uses proper filtering conditions</li>
                            <li>Includes all necessary table joins</li>
                            <li>Follows SQL syntax rules</li>
                        </ul>
                    <?php else: ?>
                        <p>No explanation available for this question.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="navigation">
            <button class="btn btn-secondary" id="prev-question">Previous Question</button>
            <button class="btn btn-primary" id="next-question">Next Question</button>
        </div>
        <?php else: ?>
            <div class="card">
                <div class="text-center">
                    <p>No answers to review. Please complete a quiz first.</p>
                    <a href="quiz.php" class="btn btn-primary">Take a Quiz</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/sql/sql.min.js"></script>
    <script>
        // Question data from PHP
        const questions = <?php echo json_encode($questions); ?>;
        const userAnswers = <?php echo json_encode($userAnswers); ?>;
        const correctness = <?php echo json_encode($correctness); ?>;
        const questionIds = <?php echo json_encode(array_keys($questions)); ?>;
        
        // DOM elements
        const questionNumberEl = document.getElementById('question-number');
        const totalQuestionsEl = document.getElementById('total-questions');
        const questionTextEl = document.getElementById('question-text');
        const answerFeedbackEl = document.getElementById('answer-feedback');
        const difficultyEl = document.querySelector('.difficulty');
        const prevQuestionBtn = document.getElementById('prev-question');
        const nextQuestionBtn = document.getElementById('next-question');
        const questionItems = document.querySelectorAll('.question-item');
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = themeToggle.querySelector('i');
        
        // CodeMirror editors
        let userAnswerEditor, correctAnswerEditor;
        
        // Current question index
        let currentQuestionIndex = 0;
        
        // Initialize CodeMirror editors
        function initEditors() {
            const isDarkMode = document.body.classList.contains('dark-mode');
            
            userAnswerEditor = CodeMirror(document.getElementById('user-answer-editor'), {
                value: "",
                mode: "text/x-sql",
                theme: isDarkMode ? "monokai" : "eclipse",
                lineNumbers: true,
                readOnly: true,
                lineWrapping: true
            });
            
            correctAnswerEditor = CodeMirror(document.getElementById('correct-answer-editor'), {
                value: "",
                mode: "text/x-sql",
                theme: isDarkMode ? "monokai" : "eclipse",
                lineNumbers: true,
                readOnly: true,
                lineWrapping: true
            });
        }
        
        // Function to load question
        function loadQuestion(index) {
            if (index < 0 || index >= questionIds.length) return;
            
            currentQuestionIndex = index;
            const questionId = questionIds[index];
            const question = questions[questionId];
            
            if (!question) return;
            
            // Update question info
            questionNumberEl.textContent = index + 1;
            questionTextEl.textContent = question.text;
            
            // Update difficulty
            if (difficultyEl) {
                difficultyEl.textContent = question.difficulty.charAt(0).toUpperCase() + question.difficulty.slice(1);
                difficultyEl.className = 'difficulty ' + question.difficulty;
            }
            
            // Update answers in editors
            if (userAnswers[questionId] && userAnswers[questionId].trim() !== '') {
                userAnswerEditor.setValue(userAnswers[questionId]);
            } else {
                userAnswerEditor.setValue('-- No answer provided');
            }
            
            correctAnswerEditor.setValue(question.solution);
            
            // Update feedback
            const isCorrect = correctness[questionId] || false;
            answerFeedbackEl.className = isCorrect ? 
                'answer-feedback answer-correct' : 'answer-feedback answer-incorrect';
            answerFeedbackEl.innerHTML = isCorrect ? 
                '<i class="fas fa-check-circle"></i> Correct! Well done.' : 
                '<i class="fas fa-times-circle"></i> Incorrect. See the correct answer below.';
            
            // Update navigation buttons
            prevQuestionBtn.disabled = (index === 0);
            nextQuestionBtn.disabled = (index === questionIds.length - 1);
            
            // Update question list highlighting
            questionItems.forEach((item, i) => {
                if (i === index) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
            
            // Refresh editors to ensure proper rendering
            setTimeout(() => {
                userAnswerEditor.refresh();
                correctAnswerEditor.refresh();
            }, 100);
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
        
        // Click on question items to navigate
        questionItems.forEach(item => {
            item.addEventListener('click', () => {
                const index = parseInt(item.getAttribute('data-index'));
                if (!isNaN(index) && index >= 0 && index < questionIds.length) {
                    loadQuestion(index);
                }
            });
        });
        
        themeToggle.addEventListener('click', toggleTheme);
        
        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            initTheme();
            initEditors();
            totalQuestionsEl.textContent = questionIds.length;
            loadQuestion(0);
        });
    </script>
</body>
</html>