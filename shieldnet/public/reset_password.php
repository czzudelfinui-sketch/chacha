<?php
session_start();
require_once 'db_connect.php';

$token = trim($_GET['token'] ?? '');
$message = '';
$messageType = '';
$validToken = false;
$userId = null;

// Validate token
if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        $validToken = true;
        $userId = $user['id'];
    } else {
        $message = 'This password reset link is invalid or has expired. Please request a new one.';
        $messageType = 'error';
    }
} else {
    $message = 'No reset token provided.';
    $messageType = 'error';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (strlen($new) < 6) {
        $message = 'Password must be at least 6 characters.';
        $messageType = 'error';
    } elseif ($new !== $confirm) {
        $message = 'Passwords do not match.';
        $messageType = 'error';
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
        $update->execute([$hashed, $userId]);

        $message = 'Your password has been reset successfully! You can now log in.';
        $messageType = 'success';
        $validToken = false; // Hide form after success
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - ShieldNet</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="assets/2.png">
    <style>
        .password-strength {
            height: 4px;
            border-radius: 4px;
            margin-top: 6px;
            transition: all 0.3s;
            background: #e0e0e0;
        }
        .strength-weak   { background: #EE5D50; width: 33%; }
        .strength-medium { background: #FFB547; width: 66%; }
        .strength-strong { background: #01B574; width: 100%; }
        .strength-label  { font-size: 12px; color: #A3AED0; margin-top: 4px; }
    </style>
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
                    <h2>Set New Password</h2>
                    <?php if ($validToken): ?>
                    <p style="color: #A3AED0; font-size: 14px; margin-top: 6px;">
                        Create a strong new password for your account.
                    </p>
                    <?php endif; ?>
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

                <?php if ($validToken): ?>
                <form action="reset_password.php?token=<?php echo urlencode($token); ?>" method="POST">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control"
                               placeholder="At least 6 characters" required oninput="checkStrength(this.value)">
                        <div class="password-strength" id="strength-bar"></div>
                        <div class="strength-label" id="strength-label"></div>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter your password" required>
                    </div>
                    <button type="submit" class="btn-auth">Reset Password</button>
                </form>
                <?php endif; ?>

                <div class="auth-footer" style="margin-top: 20px;">
                    <?php if ($messageType === 'success'): ?>
                        <a href="login.php" class="btn-auth" style="display: inline-block; text-align: center; text-decoration: none; padding: 12px 30px; border-radius: 12px;">
                            Go to Login
                        </a>
                    <?php else: ?>
                        <a href="forgot_password.php" style="color: var(--primary-color); text-decoration: none; font-size: 14px;">
                            ← Request a new link
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="auth-image-side">
            <h1>Secure Your Account</h1>
            <p>Choose a strong, unique password to keep your ShieldNet account protected.</p>
        </div>
    </div>

    <script>
    function checkStrength(val) {
        const bar   = document.getElementById('strength-bar');
        const label = document.getElementById('strength-label');
        if (!val) { bar.className = 'password-strength'; label.textContent = ''; return; }

        const hasLength  = val.length >= 8;
        const hasUpper   = /[A-Z]/.test(val);
        const hasNumber  = /[0-9]/.test(val);
        const hasSpecial = /[^A-Za-z0-9]/.test(val);
        const score = [hasLength, hasUpper, hasNumber, hasSpecial].filter(Boolean).length;

        if (score <= 1) {
            bar.className = 'password-strength strength-weak';
            label.textContent = 'Weak';
            label.style.color = '#EE5D50';
        } else if (score <= 3) {
            bar.className = 'password-strength strength-medium';
            label.textContent = 'Medium';
            label.style.color = '#FFB547';
        } else {
            bar.className = 'password-strength strength-strong';
            label.textContent = 'Strong';
            label.style.color = '#01B574';
        }
    }
    </script>
</body>
</html>
