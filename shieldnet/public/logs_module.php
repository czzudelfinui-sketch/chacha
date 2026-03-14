<?php
require_once 'db_connect.php';

$search = isset($_GET['search_log']) ? $_GET['search_log'] : '';

// Query with Search Filter
$query = "SELECT l.*, u.full_name 
          FROM lock_logs l 
          LEFT JOIN users u ON l.user_id = u.id";

if (!empty($search)) {
    $query .= " WHERE u.full_name LIKE :search OR l.action LIKE :search";
}
$query .= " ORDER BY l.created_at DESC LIMIT 15";

$stmt = $pdo->prepare($query);
if (!empty($search)) {
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt->execute();
}
$logs = $stmt->fetchAll();
?>

<div class="user-module-container" style="margin-top: 25px;">
    <div class="activity-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 style="font-size: 20px; color: #1B2559;">User Access Logs</h2>
        <form method="GET" class="search-form" style="display: flex; align-items: center; background: #F4F7FE; padding: 5px 10px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05);">
    <input type="hidden" name="tab" value="logs">
    
    <i class="fas fa-search" style="color: #2B3674; margin-right: 8px; font-size: 14px;"></i>
    
    <input type="text" name="search_log" placeholder="Search logs..." 
           value="<?php echo htmlspecialchars($search); ?>"
           style="background: transparent; border: none; outline: none; color: #2B3674; font-size: 13px; width: 180px; font-weight: 500;">
    
    <button type="submit" style="background: #1B2559; color: white; border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer; font-size: 12px; transition: all 0.2s ease; margin-left: 5px; font-weight: 600;">
        Go
    </button>
    
    <?php if (!empty($search)): ?>
        <a href="?tab=logs" style="margin-left: 8px; color: #EE5D50; font-size: 12px; text-decoration: none;"><i class="fas fa-times"></i></a>
    <?php endif; ?>
</form>
    </div>
    
    <table class="user-table">
        <thead>
            <tr>
                <th>Action</th>
                <th>User Name</th>
                <th>Method</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): 
                $statusColor = ($log['action'] == 'lock') ? '#EE5D50' : '#01B574';
            ?>
            <tr>
                <td>
                    <span style="font-weight: 700; text-transform: uppercase; font-size: 11px; color: <?php echo $statusColor; ?>; background: <?php echo $statusColor; ?>1A; padding: 4px 8px; border-radius: 4px;">
                        <?php echo htmlspecialchars($log['action']); ?>
                    </span>
                </td>
                <td><span style="color: #2B3674; font-weight: 600;"><?php echo htmlspecialchars($log['full_name'] ?? 'System/RFID'); ?></span></td>
                <td style="color: #A3AED0;"><i class="fas <?php echo $log['device'] === 'rfid' ? 'fa-id-card' : 'fa-globe'; ?>"></i> <?php echo strtoupper($log['device']); ?></td>
                <td style="color: #A3AED0; font-size: 13px;"><?php echo date('M d, H:i', strtotime($log['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>