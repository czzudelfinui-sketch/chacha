<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $targetDir = "uploads/avatars/";
    if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
    
    $fileType = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
    $fileName = $userId . "_" . time() . "." . $fileType;
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFile)) {
        $stmt = $pdo->prepare("UPDATE users SET avatar_url = ? WHERE id = ?");
        $stmt->execute([$targetFile, $userId]);
        echo json_encode(['success' => true, 'avatar_url' => $targetFile]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Upload failed']);
    }
    exit();
}

$stmt = $pdo->prepare("SELECT avatar_url FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
header('Content-Type: application/json');
echo json_encode(['success' => true, 'avatar_url' => $user['avatar_url']]);
?>
