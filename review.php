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
    <link rel="stylesheet" href="review.css">
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
            <div>points (<?php round($score * 100) / 190 ?>%)</div>
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