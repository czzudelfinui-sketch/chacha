<?php
require_once 'db_connect.php';

$result = $pdo->query("
    SELECT l.*, u.full_name
    FROM lock_logs l
    LEFT JOIN users u ON u.id = l.user_id
    ORDER BY l.created_at DESC
    LIMIT 50
");

$logs = [];
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $logs[] = $row;
}
?>

<div class="activity-section">
    <h2>History</h2>
    <div id="activity-feed">
        <?php foreach($logs as $log): 
            $isUnlock = $log['action'] === 'unlock';
            $device = $log['device'];
            $userName = $log['full_name'] ?? 'RFID Access';

            // Set icon and colors
            if ($device === 'rfid') {
                $bgColor = $isUnlock ? 'rgba(0, 123, 255, 0.1)' : 'rgba(0, 123, 255, 0.1)';
                $iconColor = '#007BFF';
                $icon = $isUnlock ? 'fa-key' : 'fa-lock';
            } else { // web_app
                $bgColor = $isUnlock ? 'rgba(1, 181, 116, 0.1)' : 'rgba(238, 93, 80, 0.1)';
                $iconColor = $isUnlock ? '#01B574' : '#EE5D50';
                $icon = $isUnlock ? 'fa-lock-open' : 'fa-lock';
            }
        ?>
        <div class="activity-item">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 40px; height: 40px; border-radius: 50%; 
                    background: <?php echo $bgColor; ?>; 
                    display: flex; align-items: center; justify-content: center; 
                    color: <?php echo $iconColor; ?>;">
                    <i class="fas <?php echo $icon; ?>"></i>
                </div>
                <div>
                    <p style="margin: 0; font-weight: 600;">
                        Door <?php echo ucfirst($log['action']); ?> via <?php echo $device === 'rfid' ? 'RFID' : 'Web/App'; ?>
                    </p>
                    <span style="font-size: 13px; color: var(--text-muted);">
                        <?php echo htmlspecialchars($userName); ?> • <?php echo date('Y-m-d H:i', strtotime($log['created_at'])); ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>