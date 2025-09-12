<?php

/**
 * Project: SQL Master Web App
 * Author: [Marfo John Kusi]
 * Internship: NIT Open Labs Ghana
 * Description: Built to help students practice SQL queries with
 *              real-time checking and scoring system.
 */

session_start();
// Include database configuration
require_once 'config/database.php';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validate inputs
    $errors = [];
    if (empty($email)) {
        $errors[] = "Email is required";
    }
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            
            // Check if student exists
            $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() === 1) {
                $student = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($password, $student['password'])) {
                    // Set session variables
                    $_SESSION['student_id'] = $student['student_id'];
                    $_SESSION['name'] = $student['name'];
                    $_SESSION['email'] = $student['email'];
                    $_SESSION['loggedin'] = true;
                    
                    // Redirect to Landing page
                    header("Location: index.php");
                    exit();
                } else {
                    $errors[] = "Invalid password";
                }
            } else {
                $errors[] = "Email not found";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Check if user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: quiz.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Master - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            --shadow: rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: var(--dark);
            line-height: 1.6;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .card {
            background-color: var(--bg-light);
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 30px;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo i {
            font-size: 40px;
            color: var(--primary);
        }

        .logo h1 {
            font-size: 24px;
            margin-top: 10px;
            color: var(--dark);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: var(--primary);
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .alert-error {
            background-color: #ffebee;
            color: #f44336;
            border-left: 4px solid #f44336;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #4caf50;
            border-left: 4px solid #4caf50;
        }

        .alert i {
            margin-right: 10px;
            font-size: 18px;
        }

        .signup-redirect {
            text-align: center;
            margin-top: 20px;
            color: var(--gray);
        }

        .signup-redirect a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .signup-redirect a:hover {
            text-decoration: underline;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: var(--gray);
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                <i class="fas fa-database"></i>
                <h1>SQL Master</h1>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <div><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>

            <div class="signup-redirect">
                Don't have an account? <a href="signup.php">Sign up here</a>
            </div>

            <div class="footer">
                <p>Practice SQL queries and master your skills</p>
            </div>
        </div>
    </div>

    <script>
        // Hide alerts after 5 seconds
        setTimeout(() => {
            const errorAlert = document.querySelector('.alert-error');
            const successAlert = document.querySelector('.alert-success');
            
            if (errorAlert) {
                errorAlert.style.display = 'none';
            }
            
            if (successAlert) {
                successAlert.style.display = 'none';
            }
        }, 5000);
    </script>
</body>
</html>
