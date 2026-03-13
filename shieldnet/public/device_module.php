<?php
require_once 'db_connect.php';

$stmt = $pdo->query("SELECT * FROM devices ORDER BY device_name ASC");
$devices = $stmt->fetchAll();
?>

<div class="user-module-container" style="margin-top: 25px;">
    <div class="activity-header">
        <h2 style="font-size: 20px; color: #1B2559;">Device Management</h2>
    </div>
    
    <div class="dashboard-grid" style="margin-top: 20px;">
        <?php foreach ($devices as $device): ?>
        <div class="stat-card" style="justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div class="stat-icon" style="background: <?php echo $device['status'] == 'online' ? 'rgba(1, 181, 116, 0.1)' : 'rgba(238, 93, 80, 0.1)'; ?>; color: <?php echo $device['status'] == 'online' ? '#01B574' : '#EE5D50'; ?>;">
                    <i class="fas <?php echo strpos($device['device_name'], 'Camera') !== false ? 'fa-video' : 'fa-lock'; ?>"></i>
                </div>
                <div class="stat-info">
                    <h3 style="margin: 0;"><?php echo htmlspecialchars($device['device_name']); ?></h3>
                    <p style="font-size: 14px; margin: 0; display: flex; align-items: center; gap: 5px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: <?php echo $device['status'] == 'online' ? '#01B574' : '#EE5D50'; ?>;"></span>
                        <?php echo ucfirst($device['status']); ?>
                    </p>
                </div>
            </div>
            <div class="actions">
                <button class="action-btn" title="Configure"><i class="fas fa-cog"></i></button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
