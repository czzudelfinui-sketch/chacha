<?php
session_start();
require_once 'db_connect.php';
require_once 'mail_helper.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            
            // 1. Generate OTP
            $otp = rand(100000, 999999);
            $expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));

            // 2. Store OTP in database
            $update = $pdo->prepare("UPDATE users SET otp_code = ?, otp_expires_at = ? WHERE id = ?");
            $update->execute([$otp, $expires, $user['id']]);

            // 3. Send Email
            if (sendOTP($user['email'], $otp)) {
                $_SESSION['temp_user'] = [
                    'id' => $user['id'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role']
                ];
                // Track Session for "Logged-in Devices"
                $stmt = $pdo->prepare("INSERT INTO user_sessions (user_id, session_id, device_info, ip_address, last_activity) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$user['id'], session_id(), $_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR']]);
                
                header("Location: verify_otp.php");
                exit();
            } else {
                $error = "Security system error: Failed to send OTP. Please contact Admin.";
            }
        } else {
            // Record failed login attempt
            if ($user) {
                $pdo->prepare("INSERT INTO login_attempts (user_id, ip_address, attempt_time, status) VALUES (?, ?, NOW(), 'failed')")->execute([$user['id'], $_SERVER['REMOTE_ADDR']]);
                
                // Check for multiple failures
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE user_id = ? AND status = 'failed' AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
                $stmt->execute([$user['id']]);
                if ($stmt->fetchColumn() >= 3) {
                    // require_once 'mail_helper.php'; // Already included at the top
                    // sendSecurityAlert($user['email'], $_SERVER['REMOTE_ADDR']); // Simulated
                    $error = "Too many failed attempts. A security alert has been sent to your email.";
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
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
    <title>Login - ShieldNet</title>
    <link rel="stylesheet" href="style.css">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#6A1BFF">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js');
            });
        }

        // Check for Biometric Redirect
        document.addEventListener('DOMContentLoaded', () => {
             const bioEnabled = localStorage.getItem('shieldnet-bio') === 'true';
             if (bioEnabled && !<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
                 // If bio enabled and not logged in, we can show a shortcut if they were recently active
                 // For now, we'll just show how it looks
             }
        });
    </script>
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
                    <div style="color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #f5c6cb;">
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
                        <a href="forgot_password.php" class="forgot-password">Forgot password?</a>
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