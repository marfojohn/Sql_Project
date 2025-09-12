<?php
/**
 * Project: SQL Master Web App
 * Author: [John Kusi Marfo]
 * Internship: NIT Open Labs Ghana
 * Description: Built to help students practice SQL queries with
 *              real-time checking and scoring system.
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Master - Test Your Database Skills</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --accent: #7209b7;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4cc9f0;
            --warning: #f72585;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            flex: 1;
        }

        header {
            text-align: center;
            margin-bottom: 40px;
            animation: fadeIn 1s ease-out;
        }

        .logo {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .logo i {
            color: var(--success);
            filter: drop-shadow(0 0 10px rgba(76, 201, 240, 0.7));
        }

        .tagline {
            font-size: 22px;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        @media (max-width: 900px) {
            .main-content {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.18);
            animation: slideUp 1s ease-out;
        }

        .card-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--success);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .points-system {
            margin-bottom: 25px;
        }

        .point-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            transition: transform 0.3s;
        }

        .point-item:hover {
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.1);
        }

        .point-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 18px;
        }

        .basic .point-icon {
            background: rgba(76, 201, 240, 0.2);
            color: var(--success);
        }

        .intermediate .point-icon {
            background: rgba(247, 37, 133, 0.2);
            color: var(--warning);
        }

        .advanced .point-icon {
            background: rgba(114, 9, 183, 0.2);
            color: var(--accent);
        }

        .point-details {
            flex: 1;
        }

        .point-value {
            font-weight: bold;
            font-size: 18px;
        }

        .point-desc {
            font-size: 14px;
            opacity: 0.8;
        }

        .database-schema {
            margin-top: 30px;
        }

        .schema-container {
            background: rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 14px;
            line-height: 1.6;
            overflow-x: auto;
            margin-top: 15px;
        }

        .table-name {
            color: var(--success);
            font-weight: bold;
        }

        .columns {
            color: #ffa500;
        }

        .quiz-info {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .features {
            list-style-type: none;
            margin: 20px 0;
        }

        .features li {
            margin-bottom: 12px;
            padding-left: 30px;
            position: relative;
        }

        .features li i {
            position: absolute;
            left: 0;
            top: 3px;
            color: var(--success);
        }

        .schema-image {
            width: 100%;
            border-radius: 8px;
            margin-top: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .start-btn-container {
            text-align: center;
            margin-top: 40px;
            animation: pulse 2s infinite;
        }

        .start-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 18px 45px;
            font-size: 20px;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .start-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.6);
        }

        .start-btn:active {
            transform: translateY(1px);
        }

        .floating-items {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: -1;
        }

        .floating-item {
            position: absolute;
            width: 30px;
            height: 30px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }

        .floating-item:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
            width: 40px;
            height: 40px;
        }

        .floating-item:nth-child(2) {
            top: 60%;
            left: 80%;
            animation-delay: 2s;
            width: 20px;
            height: 20px;
        }

        .floating-item:nth-child(3) {
            top: 40%;
            left: 70%;
            animation-delay: 4s;
            width: 35px;
            height: 35px;
        }

        .floating-item:nth-child(4) {
            top: 70%;
            left: 20%;
            animation-delay: 6s;
            width: 25px;
            height: 25px;
        }

        .floating-item:nth-child(5) {
            top: 30%;
            left: 50%;
            animation-delay: 8s;
            width: 30px;
            height: 30px;
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
            100% { transform: translateY(0) rotate(360deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            width: 100%;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .footer-text {
            font-size: 16px;
            opacity: 0.9;
        }

        .github-link {
            color: var(--success);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .github-link:hover {
            color: white;
            transform: translateY(-2px);
        }

        .max-points {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            margin-top: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="floating-items">
        <div class="floating-item"></div>
        <div class="floating-item"></div>
        <div class="floating-item"></div>
        <div class="floating-item"></div>
        <div class="floating-item"></div>
    </div>

    <div class="container">
        <header>
            <div class="logo">
                <i class="fas fa-database"></i>
                <span>SQL Master</span>
            </div>
            <div class="tagline">Test your SQL query skills with our interactive challenge!</div>
        </header>

        <div class="main-content">
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-star"></i>
                    Points System
                </div>
                
                <div class="points-system">
                    <div class="point-item basic">
                        <div class="point-icon">
                            <i class="fas fa-circle"></i>
                        </div>
                        <div class="point-details">
                            <div class="point-value">10 points - Basic Questions</div>
                            <div class="point-desc">4 questions testing fundamental SQL knowledge</div>
                        </div>
                    </div>
                    
                    <div class="point-item intermediate">
                        <div class="point-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="point-details">
                            <div class="point-value">20 points - Intermediate Questions</div>
                            <div class="point-desc">3 questions with moderate complexity</div>
                        </div>
                    </div>
                    
                    <div class="point-item advanced">
                        <div class="point-icon">
                            <i class="fas fa-gem"></i>
                        </div>
                        <div class="point-details">
                            <div class="point-value">30 points - Advanced Questions</div>
                            <div class="point-desc">3 challenging questions for experts</div>
                        </div>
                    </div>

                    <div class="max-points">
                        Maximum Possible Score: 190 points
                    </div>
                </div>
                
                <div class="database-schema">
                    <div class="card-title">
                        <i class="fas fa-table"></i>
                        Database Schema
                    </div>
                    <p class="quiz-info">
                        You'll be working with the following database structure. Study it carefully before starting the quiz.
                    </p>
                    <img src="New Data .png" alt="Company Database Schema" class="schema-image">
                </div>
            </div>
            
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-info-circle"></i>
                    About the Quiz
                </div>
                
                <p class="quiz-info">
                    Welcome to SQL Master, the ultimate challenge for testing your SQL query skills! 
                    You'll be working with a company database containing information about employees, 
                    branches, clients, and suppliers.
                </p>
                
                <p class="quiz-info">
                    The quiz consists of 10 questions with varying difficulty levels. You'll need to 
                    write SQL queries to answer questions based on the database schema shown.
                </p>
                
                <ul class="features">
                    <li><i class="fas fa-check"></i> Real-time query execution</li>
                    <li><i class="fas fa-check"></i> Instant feedback on your answers</li>
                    <li><i class="fas fa-check"></i> Score tracking based on question difficulty</li>
                    <li><i class="fas fa-check"></i> 30-minute time limit to complete all questions</li>
                    <li><i class="fas fa-check"></i> Performance summary at the end</li>
                </ul>
                
                <div class="card-title" style="margin-top: 30px;">
                    <i class="fas fa-lightbulb"></i>
                    Pro Tips
                </div>
                
                <ul class="features">
                    <li><i class="fas fa-star"></i> Review the database schema before starting</li>
                    <li><i class="fas fa-star"></i> Pay attention to table relationships</li>
                    <li><i class="fas fa-star"></i> Test your queries thoroughly before submitting</li>
                    <li><i class="fas fa-star"></i> Basic questions are worth 10 points each</li>
                    <li><i class="fas fa-star"></i> Intermediate questions are worth 20 points each</li>
                    <li><i class="fas fa-star"></i> Advanced questions are worth 30 points each</li>
                    <li><i class="fas fa-star"></i> Maximum possible score is 190 points</li>
                </ul>
            </div>
        </div>
        
        <div class="start-btn-container">
            <a href="quiz.php" class="start-btn">
                <i class="fas fa-play"></i>
                Start SQL Quiz
            </a>
        </div>
    </div>
    
    <footer>
        <div class="footer-content">
            <p class="footer-text">© 2025 SQL Master | Test your database query skills</p>
            <a href="https://github.com/marfojohn" class="github-link" target="_blank">
                <i class="fab fa-github"></i>
                Made by John Empire
            </a>
        </div>
    </footer>

    <script>
        // Additional animation for the start button
        document.addEventListener('DOMContentLoaded', function() {
            const startBtn = document.querySelector('.start-btn');
            
            startBtn.addEventListener('mouseover', function() {
                this.style.background = 'linear-gradient(135deg, #3a0ca3, #7209b7)';
            });
            
            startBtn.addEventListener('mouseout', function() {
                this.style.background = 'linear-gradient(135deg, #4361ee, #3a0ca3)';
            });
        });
    </script>
</body>
</html>