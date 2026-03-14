<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/Exception.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';

function sendOTP($recipientEmail, $otpCode) {
    $mail = new PHPMailer(true);

    try {
        // --- SMTP SERVER SETTINGS ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'czarinad91@gmail.com'; 
        $mail->Password   = 'cllavkgtbwmnwjoq';    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // --- SSL FIX ---
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // --- RECIPIENTS ---
        $mail->setFrom('no-reply@shieldnet.com', 'ShieldNet Security');
        $mail->addAddress($recipientEmail);

        // --- CONTENT ---
        $mail->isHTML(true);
        $mail->Subject = 'ShieldNet OTP Verification';
        $mail->Body    = "Your verification code is: <b>$otpCode</b>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function sendResetLink($recipientEmail, $resetLink) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'czarinad91@gmail.com';
        $mail->Password   = 'cllavkgtbwmnwjoq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('no-reply@shieldnet.com', 'ShieldNet Security');
        $mail->addAddress($recipientEmail);

        $mail->isHTML(true);
        $mail->Subject = 'ShieldNet – Password Reset Request';
        $mail->Body    = "
            <div style='font-family:Arial,sans-serif;max-width:500px;margin:auto;padding:30px;border-radius:12px;background:#f9f9f9;'>
                <h2 style='color:#4318FF;'>ShieldNet Password Reset</h2>
                <p style='color:#555;'>We received a request to reset the password for your account.</p>
                <p style='color:#555;'>Click the button below to set a new password. This link expires in <strong>15 minutes</strong>.</p>
                <a href='$resetLink'
                   style='display:inline-block;margin:20px 0;padding:12px 28px;background:#4318FF;color:#fff;border-radius:8px;text-decoration:none;font-weight:bold;'>
                    Reset Password
                </a>
                <p style='color:#999;font-size:12px;'>If you did not request this, you can safely ignore this email.</p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}