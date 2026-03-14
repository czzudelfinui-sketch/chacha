<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

$action = $_GET['action'] ?? '';

if ($action === 'entry_trend') {
    // Entries by hour for the last 7 days
    $stmt = $pdo->query("SELECT HOUR(created_at) as hour, COUNT(*) as count FROM lock_logs WHERE action = 'unlock' GROUP BY hour ORDER BY hour ASC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $data]);
} elseif ($action === 'entry_methods') {
    // Breakdown of entry methods
    $stmt = $pdo->query("SELECT device, COUNT(*) as count FROM lock_logs WHERE action = 'unlock' GROUP BY device");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
