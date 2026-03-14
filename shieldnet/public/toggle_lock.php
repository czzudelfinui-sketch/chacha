<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once 'db_connect.php';

header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';

if (!in_array($action, ['lock', 'unlock'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
}

$newStatus = ($action === 'unlock') ? 'unlocked' : 'locked';
$userId    = $_SESSION['user_id'];


$conn->begin_transaction();

try {
    $stmt = $conn->prepare("
        INSERT INTO lock_status (id, status, updated_by, updated_at)
        VALUES (1, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE status = VALUES(status), updated_by = VALUES(updated_by), updated_at = NOW()
    ");
    $stmt->bind_param("si", $newStatus, $userId);
    $stmt->execute();
    $stmt->close();

    $stmt2 = $conn->prepare("
        INSERT INTO lock_logs (user_id, action, created_at)
        VALUES (?, ?, NOW())
    ");
    $stmt2->bind_param("is", $userId, $action);
    $stmt2->execute();
    $stmt2->close();

    $conn->commit();

    echo json_encode(['success' => true, 'status' => $newStatus]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
