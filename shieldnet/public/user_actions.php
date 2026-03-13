<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'add') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header("Location: admin_dashboard.php?tab=users&error=duplicate");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$full_name, $email, $hashed_password, $role])) {
        header("Location: admin_dashboard.php?tab=users&success=added");
    } else {
        header("Location: admin_dashboard.php?tab=users&error=add_failed");
    }
} 

elseif ($action == 'edit') {
    $id = $_POST['id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        header("Location: admin_dashboard.php?tab=users&error=duplicate");
        exit();
    }

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, role = ?, password = ? WHERE id = ?");
        $success = $stmt->execute([$full_name, $email, $role, $hashed_password, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, role = ? WHERE id = ?");
        $success = $stmt->execute([$full_name, $email, $role, $id]);
    }

    if ($success) {
        header("Location: admin_dashboard.php?tab=users&success=updated");
    } else {
        header("Location: admin_dashboard.php?tab=users&error=update_failed");
    }
}

elseif ($action == 'delete') {
    $id = $_POST['id'];
    if ($id == $_SESSION['user_id']) {
        header("Location: admin_dashboard.php?tab=users&error=self_delete");
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: admin_dashboard.php?tab=users&success=deleted");
    } else {
        header("Location: admin_dashboard.php?tab=users&error=delete_failed");
    }
}

else {
    header("Location: admin_dashboard.php?tab=users");
}
exit();
?>
