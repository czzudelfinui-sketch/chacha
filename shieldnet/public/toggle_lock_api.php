<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once 'db_connect.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if (!in_array($action, ['lock', 'unlock'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
}

$userId = $_SESSION['user_id'];
$device = 'web/app'; 

try {
    $stmt = $pdo->prepare("
        INSERT INTO lock_logs (user_id, device, action, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([$userId, $device, $action]);

    echo json_encode(['success' => true, 'action' => $action]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
