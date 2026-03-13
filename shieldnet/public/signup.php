<?php
session_start();
require_once 'db_connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($full_name) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "Email already registered.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'user')");
                if ($stmt->execute([$full_name, $email, $hashed_password])) {
                    $success = "Registration successful! <a href='login.php'>Login here</a>";
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            }
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - ShieldNet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-form-side">
            <div class="auth-content-wrapper">
                <div class="logo-section">
                    <img src="assets/1-removebg-preview.png" alt="ShieldNet Logo" class="logo-icon">
                    <span class="logo-text">SHIELDNET</span>
                </div>
                
                <div class="auth-header">
                    <h2>Create your account</h2>
                </div>

                <?php if ($error): ?>
                    <div style="color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div style="color: #28a745; background: #d4edda; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form action="signup.php" method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control" placeholder="Enter your full name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                    </div>
                    
                    <button type="submit" class="btn-auth">Sign Up</button>
                </form>

                <div class="auth-footer">
                    Already have an account? <a href="login.php">Sign in</a>
                </div>
            </div>
        </div>

        <div class="auth-image-side">
            <h1>Glad to see you!</h1>
            <p>Securely manage access, monitor activity, and control your smart lock system with ease.</p>
        </div>
    </div>
</body>
</html>
