<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';

$fullName = $_SESSION['full_name'];
$initials = strtoupper(substr($fullName, 0, 1));

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$activeRFID = $pdo->query("SELECT COUNT(*) FROM rfid_cards WHERE status = 'active'")->fetchColumn();
$onlineDevices = $pdo->query("SELECT COUNT(*) FROM devices WHERE status = 'online'")->fetchColumn();

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - ShieldNet</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        const savedTheme = localStorage.getItem('shieldnet-theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</head>
<body>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <img src="assets/1-removebg-preview.png" alt="ShieldNet Logo">
                <span>SHIELDNET</span>
            </div>
            
            <nav class="sidebar-menu">
                <li class="menu-item"><a href="?tab=dashboard" class="menu-link <?php echo $tab == 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-th-large"></i> <span>Overview</span></a></li>
                <li class="menu-item"><a href="?tab=users" class="menu-link <?php echo $tab == 'users' ? 'active' : ''; ?>"><i class="fas fa-users"></i> <span>Manage Users</span></a></li>
                <li class="menu-item"><a href="?tab=rfid" class="menu-link <?php echo $tab == 'rfid' ? 'active' : ''; ?>"><i class="fas fa-id-card"></i> <span>RFID Cards</span></a></li>
                <li class="menu-item"><a href="?tab=devices" class="menu-link <?php echo $tab == 'devices' ? 'active' : ''; ?>"><i class="fas fa-microchip"></i> <span>Manage Devices</span></a></li>
                <li class="menu-item"><a href="?tab=logs" class="menu-link <?php echo $tab == 'logs' ? 'active' : ''; ?>"><i class="fas fa-list-alt"></i> <span>System Logs</span></a></li>
                <li class="menu-item"><a href="?tab=settings" class="menu-link <?php echo $tab == 'settings' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> <span>System Settings</span></a></li>
            </nav>

            <div class="sidebar-footer">
                <a href="logout.php" class="menu-link logout-link"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
            </div>
        </aside>

        <main class="main-content">
            <header class="header-top">
                <div class="header-title">
                    <p style="color: var(--text-muted); font-size: 14px; font-weight: 500;">Admin Panel</p>
                    <h1><?php 
                        switch($tab) {
                            case 'users': echo 'User Management'; break;
                            case 'rfid': echo 'RFID Control'; break;
                            case 'devices': echo 'IoT Devices'; break;
                            case 'logs': echo 'Security Analytics'; break;
                            case 'settings': echo 'System Config'; break;
                            default: echo 'Security Overview';
                        }
                    ?></h1>
                </div>
                <div class="user-profile">
                    <div class="user-avatar"><?php echo $initials; ?></div>
                    <span style="font-weight: 600;"><?php echo htmlspecialchars($fullName); ?> (Admin)</span>
                </div>
            </header>

            <?php if ($tab == 'dashboard'): ?>
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
                        <div class="stat-info">
                            <h3>Total Users</h3>
                            <p><?php echo $totalUsers; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-id-card"></i></div>
                        <div class="stat-info">
                            <h3>Active RFIDs</h3>
                            <p><?php echo $activeRFID; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-network-wired"></i></div>
                        <div class="stat-info">
                            <h3>Online Devices</h3>
                            <p><?php echo $onlineDevices; ?></p>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 40px;">
                    <?php include 'device_module.php'; ?>
                </div>
                <div style="margin-top: 40px;">
                    <?php include 'logs_module.php'; ?>
                </div>

            <?php elseif ($tab == 'users'): ?>
                <?php include 'user_module.php'; ?>
            <?php elseif ($tab == 'rfid'): ?>
                <?php include 'rfid_module.php'; ?>
            <?php elseif ($tab == 'devices'): ?>
                <?php include 'device_module.php'; ?>
            <?php elseif ($tab == 'logs'): ?>
                <?php include 'logs_module.php'; ?>
            <?php elseif ($tab == 'settings'): ?>
                <?php include 'settings_module.php'; ?>
            <?php endif; ?>

        </main>
    </div>
</body>
</html>
