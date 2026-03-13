<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'add') {
    $card_uid = $_POST['card_uid'];
    $user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : null;
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("INSERT INTO rfid_cards (user_id, card_uid, status) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $card_uid, $status]);
        header("Location: admin_dashboard.php?tab=rfid&success=added");
    } catch (PDOException $e) {
        header("Location: admin_dashboard.php?tab=rfid&error=duplicate");
    }
} 

elseif ($action == 'edit') {
    $id = $_POST['id'];
    $card_uid = $_POST['card_uid'];
    $user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : null;
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("UPDATE rfid_cards SET user_id = ?, card_uid = ?, status = ? WHERE id = ?");
        $stmt->execute([$user_id, $card_uid, $status, $id]);
        header("Location: admin_dashboard.php?tab=rfid&success=updated");
    } catch (PDOException $e) {
        header("Location: admin_dashboard.php?tab=rfid&error=update_failed");
    }
}

elseif ($action == 'delete') {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM rfid_cards WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_dashboard.php?tab=rfid&success=deleted");
}

else {
    header("Location: admin_dashboard.php?tab=rfid");
}
exit();
?>
