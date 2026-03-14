<?php
require_once 'db_connect.php';

// Fetch devices
$stmt = $pdo->query("SELECT * FROM devices ORDER BY status DESC, device_name ASC");
$devices = $stmt->fetchAll();
?>

<div class="user-module-container" style="margin-top: 25px;">
    <div class="activity-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 20px; color: #1B2559;">Device Management</h2>
        <span style="font-size: 11px; color: #A3AED0; background: #F4F7FE; padding: 5px 12px; border-radius: 20px; font-weight: 700;">
            <i class="fas fa-sync fa-spin" style="margin-right: 5px;"></i> LIVE STATUS
        </span>
    </div>
    
    <div class="dashboard-grid" style="margin-top: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <?php foreach ($devices as $device): 
            $rawName = $device['device_name'];
            $isOnline = ($device['status'] == 'online');
            
            // UI REPLACEMENT: "Living Room Camera" -> "Main Gate"
            if (stripos($rawName, 'Camera') !== false) {
                $displayName = "Main Gate Controller";
                $icon = "fa-torii-gate";
                $color = "#6a1bff";
            } else {
                $displayName = htmlspecialchars($rawName);
                $icon = (stripos($rawName, 'Siren') !== false) ? "fa-bullhorn" : "fa-lock";
                $color = $isOnline ? '#01B574' : '#EE5D50';
            }

            $battery = isset($device['battery']) ? $device['battery'] : 100;
        ?>
        <div class="stat-card" style="flex-direction: column; align-items: flex-start; padding: 22px; border-radius: 24px; background: #fff; border: 1px solid rgba(0,0,0,0.02); box-shadow: 0px 10px 30px rgba(0,0,0,0.02);">
            
            <div style="display: flex; justify-content: space-between; width: 100%;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div class="stat-icon" style="background: <?php echo $color; ?>15; color: <?php echo $color; ?>; width: 45px; height: 45px; border-radius: 14px;">
                        <i class="fas <?php echo $icon; ?>"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 15px; color: #1B2559; font-weight: 700;"><?php echo $displayName; ?></h3>
                        <p style="font-size: 11px; margin: 3px 0 0; color: <?php echo $isOnline ? '#01B574' : '#EE5D50'; ?>; font-weight: 800; letter-spacing: 0.5px;">
                            ● <?php echo strtoupper($device['status']); ?>
                        </p>
                    </div>
                </div>
                
                <div style="text-align: right; color: #A3AED0; background: #F4F7FE; padding: 4px 8px; border-radius: 8px; height: fit-content;">
                    <i class="fas fa-battery-<?php echo ($battery > 70 ? 'full' : ($battery > 20 ? 'half' : 'quarter')); ?>" style="font-size: 10px;"></i>
                    <span style="font-size: 10px; font-weight: 800;"><?php echo $battery; ?>%</span>
                </div>
            </div>

            <div style="margin-top: 25px; width: 100%; border-top: 1px solid #F8F9FD; padding-top: 18px; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-size: 11px; color: #A3AED0;">
                    <i class="far fa-clock"></i> Last seen: <b>09:49 PM</b>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <?php if ($isOnline && stripos($displayName, 'Siren') === false): ?>
                        <button class="control-btn toggle-action" title="Toggle State">
                            <i class="fas fa-power-off"></i>
                        </button>
                    <?php endif; ?>
                    <button class="control-btn settings-action" title="Config">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .control-btn {
        border: none; width: 34px; height: 34px; border-radius: 10px; 
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; cursor: pointer; transition: all 0.2s ease;
    }
    .toggle-action { background: #F4F7FE; color: #2B3674; }
    .settings-action { background: #1B2559; color: white; }
    
    .control-btn:hover { transform: translateY(-2px); filter: brightness(1.1); }
    .control-btn:active { transform: scale(0.95); }
    
    /* Responsive adjustment */
    @media (max-width: 768px) {
        .dashboard-grid { grid-template-columns: 1fr; }
    }
</style>