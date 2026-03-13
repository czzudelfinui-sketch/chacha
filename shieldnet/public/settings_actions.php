<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$userId = $_SESSION['user_id'];

if ($action == 'change_password') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        header("Location: user_dashboard.php?tab=settings&error=mismatch");
        exit();
    }

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if ($user && password_verify($current, $user['password'])) {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $updateStmt->execute([$hashed, $userId]);
        header("Location: user_dashboard.php?tab=settings&success=pw_updated");
    } else {
        header("Location: user_dashboard.php?tab=settings&error=wrong_pw");
    }
}


exit();
?>
