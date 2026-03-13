<?php
require_once 'db_connect.php';

$stmt = $pdo->query("SELECT * FROM system_logs ORDER BY created_at DESC LIMIT 10");
$logs = $stmt->fetchAll();
?>

<div class="user-module-container" style="margin-top: 25px;">
    <div class="activity-header">
        <h2 style="font-size: 20px; color: #1B2559;">System Logs</h2>
    </div>
    
    <table class="user-table">
        <thead>
            <tr>
                <th>Level</th>
                <th>Module</th>
                <th>Message</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td>
                    <?php 
                        $levelClass = '';
                        if ($log['log_level'] == 'error') $levelClass = 'color: #EE5D50;';
                        else if ($log['log_level'] == 'warning') $levelClass = 'color: #FFB547;';
                        else $levelClass = 'color: #01B574;';
                    ?>
                    <span style="font-weight: 700; text-transform: uppercase; font-size: 12px; <?php echo $levelClass; ?>">
                        <?php echo $log['log_level']; ?>
                    </span>
                </td>
                <td><span style="color: #A3AED0; font-weight: 500;"><?php echo htmlspecialchars($log['module']); ?></span></td>
                <td style="color: #2B3674;"><?php echo htmlspecialchars($log['message']); ?></td>
                <td style="color: #A3AED0; font-size: 13px;"><?php echo $log['created_at']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
