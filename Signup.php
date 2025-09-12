<?php
session_start();
// Include database configuration
require_once 'config/database.php';

// Redirect if already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: quiz.php");
    exit();
}

// Handle form submission
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $errors[] = "Email already registered";
            } else {
                // Hash password and insert new student
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO students (name, email, password) VALUES (?, ?, ?)");
                if ($stmt->execute([$name, $email, $hashed_password])) {
                    $success = "Registration successful! You can now <a href='login.php'>login</a>.";
                    // Clear form fields
                    $name = $email = '';
                } else {
                    $errors[] = "Registration failed. Please try again.";
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Master - Sign Up</title>
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
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 450px;
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

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .back-link i {
            margin-right: 5px;
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

        .password-strength {
            margin-top: 5px;
            font-size: 14px;
        }

        .strength-weak {
            color: #dc3545;
        }

        .strength-medium {
            color: #fd7e14;
        }

        .strength-strong {
            color: #28a745;
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

        .login-redirect {
            text-align: center;
            margin-top: 20px;
            color: var(--gray);
        }

        .login-redirect a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .login-redirect a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="login.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Login
        </a>
        
        <div class="card">
            <div class="logo">
                <i class="fas fa-database"></i>
                <h1>Create Your Account</h1>
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

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <div><?php echo $success; ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <div class="password-strength" id="password-strength">Must be at least 6 characters</div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Create Account</button>
            </form>

            <div class="login-redirect">
                Already have an account? <a href="login.php">Log in here</a>
            </div>
        </div>
    </div>

    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthText = document.getElementById('password-strength');
        
        passwordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            let strength = '';
            let strengthClass = '';
            
            if (password.length === 0) {
                strength = 'Must be at least 6 characters';
                strengthClass = '';
            } else if (password.length < 6) {
                strength = 'Too short';
                strengthClass = 'strength-weak';
            } else if (password.length < 8) {
                strength = 'Weak';
                strengthClass = 'strength-weak';
            } else if (password.length < 10) {
                strength = 'Medium';
                strengthClass = 'strength-medium';
            } else {
                strength = 'Strong';
                strengthClass = 'strength-strong';
            }
            
            strengthText.textContent = strength;
            strengthText.className = 'password-strength ' + strengthClass;
        });
        
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