<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';

$fullName = $_SESSION['full_name'];
$initials = strtoupper(substr($fullName, 0, 1));

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'overview';

$successMsg = '';
$errorMsg = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'pw_updated') $successMsg = "Password updated successfully!";
}
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'mismatch') $errorMsg = "Passwords do not match.";
    if ($_GET['error'] == 'wrong_pw') $errorMsg = "Current password is incorrect.";
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - ShieldNet</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        const savedTheme = localStorage.getItem('shieldnet-theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
    <style>
        .btn-toggle {
            margin-top: 10px;
            padding: 8px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--primary-color);
            color: white;
            font-size: 14px;
        }
        .btn-toggle:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-toggle:disabled { background: #ccc; cursor: not-allowed; }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <img src="assets/1-removebg-preview.png" alt="ShieldNet Logo">
                <span>SHIELDNET</span>
            </div>
            
            <nav class="sidebar-menu">
                <li class="menu-item"><a href="?tab=overview" class="menu-link <?php echo $tab == 'overview' ? 'active' : ''; ?>"><i class="fas fa-home"></i> <span>Overview</span></a></li>
                <li class="menu-item"><a href="#" class="menu-link"><i class="fas fa-clock"></i> <span>History</span></a></li>
                <li class="menu-item"><a href="#" class="menu-link"><i class="fas fa-bell"></i> <span>Notifications</span></a></li>
                <li class="menu-item"><a href="?tab=settings" class="menu-link <?php echo $tab == 'settings' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
            </nav>

            <div class="sidebar-footer">
                <a href="logout.php" class="menu-link logout-link"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
            </div>
        </aside>

        <main class="main-content">
            <header class="header-top">
                <div class="header-title">
                    <p style="color: var(--text-muted); font-size: 14px; font-weight: 500;">User Dashboard</p>
                    <h1><?php echo $tab == 'settings' ? 'App Settings' : 'Welcome back, ' . htmlspecialchars(explode(' ', $fullName)[0]); ?></h1>
                </div>
                <div class="user-profile">
                    <div class="user-avatar"><?php echo $initials; ?></div>
                    <span style="font-weight: 600;"><?php echo htmlspecialchars($fullName); ?></span>
                </div>
            </header>

            <?php if ($successMsg): ?>
                <div style="color: #01B574; background: rgba(1, 181, 116, 0.1); padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i> <?php echo $successMsg; ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMsg): ?>
                <div style="color: #EE5D50; background: rgba(238, 93, 80, 0.1); padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                    <i class="fas fa-times-circle"></i> <?php echo $errorMsg; ?>
                </div>
            <?php endif; ?>

            <?php if ($tab == 'overview'): ?>
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div id="lock-icon-container" class="stat-icon" style="background: rgba(1, 181, 116, 0.1); color: #01B574;">
                            <i id="lock-icon" class="fas fa-lock-open"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Smart Lock</h3>
                            <p id="lock-status-text" style="color: #01B574; font-weight: 700;">Unlocked</p>
                            <button id="lock-toggle-btn" class="btn-toggle" onclick="toggleLockState()">Lock Door</button>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(106, 27, 255, 0.1); color: var(--primary-color);"><i class="fas fa-history"></i></div>
                        <div class="stat-info">
                            <h3>Last Entry</h3>
                            <p style="color: var(--text-muted);">2 hours ago</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(255, 181, 71, 0.1); color: #FFB547;"><i class="fas fa-battery-three-quarters"></i></div>
                        <div class="stat-info">
                            <h3>Lock Battery</h3>
                            <p style="color: var(--text-muted);">85%</p>
                        </div>
                    </div>
                </div>

                <div class="activity-section">
                    <h2 style="font-size: 20px; margin-bottom: 20px;">Recent Activity</h2>
                    <div id="activity-feed">
                        <div class="activity-item">
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(1, 181, 116, 0.1); display: flex; align-items: center; justify-content: center; color: #01B574;">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div>
                                    <p style="margin: 0; font-weight: 600;">Door Unlocked via RFID</p>
                                    <span style="font-size: 13px; color: var(--text-muted);">Today at 6:45 PM</span>
                                </div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(238, 93, 80, 0.1); display: flex; align-items: center; justify-content: center; color: #EE5D50;">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <div>
                                    <p style="margin: 0; font-weight: 600;">Door Locked Manually</p>
                                    <span style="font-size: 13px; color: var(--text-muted);">Today at 9:15 AM</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($tab == 'settings'): ?>
                <?php include 'user_settings.php'; ?>
            <?php endif; ?>

        </main>
    </div>

    <script>
        function toggleLockState() {
            const btn = document.getElementById('lock-toggle-btn');
            const statusText = document.getElementById('lock-status-text');
            const icon = document.getElementById('lock-icon');
            const iconContainer = document.getElementById('lock-icon-container');

            const currentlyUnlocked = statusText.innerText.trim() === 'Unlocked';

            btn.disabled = true;
            btn.innerText = "Connecting...";

            fetch('toggle_lock_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=' + (currentlyUnlocked ? 'lock' : 'unlock')
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    updateUI(currentlyUnlocked);
                }
            })
            .catch(err => {
                console.log("API not found, simulating UI change.");
                updateUI(currentlyUnlocked);
            })
            .finally(() => {
                btn.disabled = false;
            });
        }

        function updateUI(toLocked) {
            const btn = document.getElementById('lock-toggle-btn');
            const statusText = document.getElementById('lock-status-text');
            const icon = document.getElementById('lock-icon');
            const iconContainer = document.getElementById('lock-icon-container');

            if (toLocked) {
                statusText.innerText = 'Locked';
                statusText.style.color = '#EE5D50';
                icon.className = 'fas fa-lock';
                iconContainer.style.background = 'rgba(238, 93, 80, 0.1)';
                iconContainer.style.color = '#EE5D50';
                btn.innerText = 'Unlock Door';
            } else {
                statusText.innerText = 'Unlocked';
                statusText.style.color = '#01B574';
                icon.className = 'fas fa-lock-open';
                iconContainer.style.background = 'rgba(1, 181, 116, 0.1)';
                iconContainer.style.color = '#01B574';
                btn.innerText = 'Lock Door';
            }
        }
    </script>
</body>
</html>
