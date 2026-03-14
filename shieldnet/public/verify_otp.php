<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['temp_user'])) {
    header("Location: login.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp_input = $_POST['otp_code'];
    $userId = $_SESSION['temp_user']['id'];

    $stmt = $pdo->prepare("SELECT otp_code, otp_expires_at FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $db_user = $stmt->fetch();

    if ($db_user['otp_code'] === $otp_input && strtotime($db_user['otp_expires_at']) > time()) {
        $_SESSION['user_id'] = $_SESSION['temp_user']['id'];
        $_SESSION['full_name'] = $_SESSION['temp_user']['full_name'];
        $_SESSION['role'] = $_SESSION['temp_user']['role'];
        
        $pdo->prepare("UPDATE users SET otp_code = NULL, otp_expires_at = NULL WHERE id = ?")->execute([$userId]);
        unset($_SESSION['temp_user']);

        header("Location: " . ($_SESSION['role'] == 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'));
        exit();
    } else {
        $error = "The code you entered is invalid or has expired.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP - ShieldNet</title>
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
                    <h2>Verify Your Identity</h2>
                    <p style="color: #A3AED0; font-size: 14px; margin-top: 10px;">We've sent a 6-digit code to your email.</p>
                </div>

                <?php if ($error): ?>
                    <div style="color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="verify_otp.php" method="POST">
                    <div class="form-group">
                        <label>Verification Code</label>
                        <input type="text" name="otp_code" class="form-control" placeholder="000000" maxlength="6" required 
                               style="text-align: center; font-size: 24px; letter-spacing: 8px; font-weight: bold;">
                    </div>
                    <button type="submit" class="btn-auth">Verify Code</button>
                </form>

                <div class="auth-footer">
                    Didn't get the code? <a href="login.php">Try logging in again</a>
                </div>
            </div>
        </div>

        <div class="auth-image-side">
            <h1>Stay Secure.</h1>
            <p>Two-factor authentication adds an extra layer of protection to your smart devices and logs.</p>
        </div>
    </div>
</body>
</html>