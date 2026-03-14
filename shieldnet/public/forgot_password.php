<?php
session_start();
require_once 'db_connect.php';
require_once 'mail_helper.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $message = 'Please enter your email address.';
        $messageType = 'error';
    } else {
        $stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Always show success to prevent email enumeration
        $message = 'If that email is registered, a password reset link has been sent. Check your inbox.';
        $messageType = 'success';

        if ($user) {
            // Generate a secure token
            $token = bin2hex(random_bytes(32));
            
            // Store token in DB (using MySQL NOW() to avoid timezone issues)
            $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE id = ?");
            $update->execute([$token, $user['id']]);

            // Build reset link
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $resetLink = "$protocol://$host/shieldnet/public/reset_password.php?token=$token";

            sendResetLink($email, $resetLink);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - ShieldNet</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="assets/2.png">
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
                    <h2>Forgot Password</h2>
                    <p style="color: #A3AED0; font-size: 14px; margin-top: 6px;">
                        Enter your email and we'll send you a link to reset your password.
                    </p>
                </div>

                <?php if ($message): ?>
                    <?php if ($messageType === 'success'): ?>
                        <div style="color: #01B574; background: rgba(1, 181, 116, 0.1); padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; border: 1px solid rgba(1, 181, 116, 0.3);">
                            ✓ <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php else: ?>
                        <div style="color: #dc3545; background: #f8d7da; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; border: 1px solid #f5c6cb;">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($messageType !== 'success'): ?>
                <form action="forgot_password.php" method="POST">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your registered email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn-auth">Send Reset Link</button>
                </form>
                <?php endif; ?>

                <div class="auth-footer" style="margin-top: 20px;">
                    <a href="login.php" style="color: var(--primary-color); text-decoration: none; font-size: 14px;">
                        ← Back to Login
                    </a>
                </div>
            </div>
        </div>

        <div class="auth-image-side">
            <h1>Reset Your Password</h1>
            <p>We'll send a secure link to your email so you can get back in quickly and safely.</p>
        </div>
    </div>
</body>
</html>
