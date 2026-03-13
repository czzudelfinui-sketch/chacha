<?php
session_start();
require_once 'db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
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
    <title>Login - ShieldNet</title>
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
                    <h2>Log in to ShieldNet</h2>
                </div>

                <?php if ($error): ?>
                    <div style="color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                    
                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                        <a href="#" class="forgot-password">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-auth">Sign In</button>
                </form>

                <div class="auth-footer">
                    Don't have an account? <a href="signup.php">Create account</a>
                </div>
            </div>
        </div>

        <div class="auth-image-side">
            <h1>Welcome Back!</h1>
            <p>Securely manage access, monitor activity, and control your smart lock system with ease.</p>
        </div>
    </div>
</body>
</html>
