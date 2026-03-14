<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';

$userId = $_SESSION['user_id'];
$user = $pdo->prepare("SELECT full_name, role, avatar_url FROM users WHERE id = ?");
$user->execute([$userId]);
$userData = $user->fetch();

$fullName = $userData['full_name'];
$initials = strtoupper(substr($fullName, 0, 1));
$avatarUrl = $userData['avatar_url'];
// --- CLEAR ALL LOGIC FOR LOGGED-IN USER ---
if (isset($_POST['clear_notifications'])) {
    $stmt = $pdo->prepare("DELETE FROM lock_logs WHERE user_id = ?");
    $stmt->execute([$userId]);
    header("Location: ?tab=notifications&success=cleared");
    exit();
}

$stmt = $pdo->query("SELECT action FROM lock_logs ORDER BY created_at DESC LIMIT 1");
$latest = $stmt->fetchColumn();
$currentStatus = ($latest === 'unlock') ? 'Unlocked' : 'Locked';

// Fetch this user's assigned RFID card
$rfidStmt = $pdo->prepare("SELECT card_uid, status FROM rfid_cards WHERE user_id = ? LIMIT 1");
$rfidStmt->execute([$userId]);
$rfidCard = $rfidStmt->fetch();

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'overview';

$successMsg = '';
$errorMsg = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'pw_updated')
        $successMsg = "Password updated successfully!";
    if ($_GET['success'] == 'cleared')
        $successMsg = "Notification history cleared successfully!";
}
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'mismatch')
        $errorMsg = "Passwords do not match.";
    if ($_GET['error'] == 'wrong_pw')
        $errorMsg = "Current password is incorrect.";
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShieldNet</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" href="assets/2.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        const savedTheme = localStorage.getItem('shieldnet-theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js');
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#6A1BFF">
</head>    <style>
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

        .btn-toggle:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-toggle:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-clear {
            background: #EE5D50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-clear:hover {
            background: #d44d42;
        }
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
                <li class="menu-item"><a href="?tab=overview"
                        class="menu-link <?php echo $tab == 'overview' ? 'active' : ''; ?>"><i class="fas fa-home"></i>
                        <span>Overview</span></a></li>
                <li class="menu-item"><a href="?tab=history"
                        class="menu-link <?php echo $tab == 'history' ? 'active' : ''; ?>"><i class="fas fa-clock"></i>
                        <span>History</span></a></li>
                <li class="menu-item"><a href="?tab=notifications" 
                        class="menu-link <?php echo $tab == 'notifications' ? 'active' : ''; ?>"><i class="fas fa-bell"></i>
                        <span>Notifications</span></a></li>
                <li class="menu-item"><a href="?tab=settings"
                        class="menu-link <?php echo $tab == 'settings' ? 'active' : ''; ?>"><i class="fas fa-cog"></i>
                        <span>Settings</span></a></li>
            </nav>

            <div class="sidebar-footer">
                <a href="logout.php" class="menu-link logout-link"><i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span></a>
            </div>
        </aside>

        <main class="main-content page-transition">
            <header class="header-top">
                <div class="header-title">
                    <p style="color: var(--text-muted); font-size: 14px; font-weight: 500;">User Dashboard</p>
                    <h1>
                        <?php 
                        if ($tab == 'settings') echo 'App Settings';
                        elseif ($tab == 'notifications') echo 'Notifications';
                        else echo 'Welcome back, ' . htmlspecialchars(explode(' ', $fullName)[0]); 
                        ?>
                    </h1>
                </div>
                <div class="user-profile">
                    <div class="user-avatar" <?php if ($avatarUrl) echo "style='background-image: url($avatarUrl);'"; ?>>
                        <?php if (!$avatarUrl) echo $initials; ?>
                    </div>
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
                    <?php
                    $isLocked = ($currentStatus === 'Locked');
                    $iconClass = $isLocked ? 'fa-lock' : 'fa-lock-open';
                    $bgColor = $isLocked ? 'rgba(238, 93, 80, 0.1)' : 'rgba(1, 181, 116, 0.1)';
                    $iconColor = $isLocked ? '#EE5D50' : '#01B574';
                    $btnText = $isLocked ? 'Unlock Door' : 'Lock Door';
                    ?>
                    <div class="stat-card">
                        <div id="lock-icon-container" class="stat-icon"
                            style="background: <?= $bgColor ?>; color: <?= $iconColor ?>;">
                            <i id="lock-icon" class="fas <?= $iconClass ?>"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Smart Lock</h3>
                            <p id="lock-status-text" style="color: <?= $iconColor ?>; font-weight: 700;">
                                <?= $currentStatus ?>
                            </p>
                            <button id="lock-toggle-btn" class="btn-toggle"><?= $btnText ?></button>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(106, 27, 255, 0.1); color: var(--primary-color);"><i
                                class="fas fa-history"></i></div>
                        <div class="stat-info">
                            <h3>Last Entry</h3>
                            <p style="color: var(--text-muted);">2 hours ago</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(255, 181, 71, 0.1); color: #FFB547;"><i
                                class="fas fa-battery-three-quarters"></i></div>
                        <div class="stat-info">
                            <h3>Lock Battery</h3>
                            <p style="color: var(--text-muted);">85%</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(67, 24, 255, 0.1); color: #4318FF;">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="stat-info">
                            <h3>My RFID Card</h3>
                            <?php if ($rfidCard): ?>
                                <p style="font-family: monospace; font-weight: 700; color: #4318FF; font-size: 14px; letter-spacing: 1px;">
                                    <?php echo htmlspecialchars($rfidCard['card_uid']); ?>
                                </p>
                                <span style="font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 20px;
                                    background: <?php echo $rfidCard['status'] === 'active' ? 'rgba(1,181,116,0.1)' : 'rgba(238,93,80,0.1)'; ?>;
                                    color: <?php echo $rfidCard['status'] === 'active' ? '#01B574' : '#EE5D50'; ?>;">
                                    <?php echo ucfirst($rfidCard['status']); ?>
                                </span>
                            <?php else: ?>
                                <p style="color: var(--text-muted); font-size: 13px;">No card assigned</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 40px; display: grid; grid-template-columns: 1fr; gap: 25px;">
                    <div class="activity-section">
                        <h3 style="margin-bottom: 20px;">My Weekly Entry Activity</h3>
                        <canvas id="userActivityChart"></canvas>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('userActivityChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                                datasets: [{
                                    label: 'Entries',
                                    data: [2, 5, 3, 8, 4, 1, 0], 
                                    backgroundColor: '#4318FF',
                                    borderRadius: 8
                                }]
                            },
                            options: { responsive: true, plugins: { legend: { display: false } } }
                        });
                    });
                </script>

                <div class="activity-section">
                    <h2 style="font-size: 20px; margin-bottom: 20px;">Recent Activity</h2>
                    <div id="activity-feed">
                        <?php
                        $activityStmt = $pdo->prepare("SELECT l.*, u.full_name FROM lock_logs l LEFT JOIN users u ON u.id = l.user_id WHERE l.user_id = ? ORDER BY l.created_at DESC LIMIT 5");
                        $activityStmt->execute([$userId]);
                        $activities = $activityStmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($activities as $log):
                            $isUnlock = $log['action'] === 'unlock';
                            $device = $log['device'];
                            $userName = $log['full_name'] ?? 'RFID Access';
                            ?>
                            <div class="activity-item">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: <?= $isUnlock ? 'rgba(1, 181, 116, 0.1)' : 'rgba(238, 93, 80, 0.1)' ?>; display: flex; align-items: center; justify-content: center; color: <?= $isUnlock ? '#01B574' : '#EE5D50' ?>;">
                                        <i class="fas <?= $isUnlock ? 'fa-lock-open' : 'fa-lock' ?>"></i>
                                    </div>
                                    <div>
                                        <p style="margin: 0; font-weight: 600;">Door <?= ucfirst($log['action']) ?>ed</p>
                                        <span style="font-size: 13px; color: var(--text-muted);"><?= date('H:i', strtotime($log['created_at'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php elseif ($tab == 'history'): ?>
                <?php include 'history_module.php'; ?>

            <?php elseif ($tab == 'notifications'): ?>
                <div class="activity-section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="font-size: 20px; margin: 0;">System Notifications</h2>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to clear your notification history?');">
                            <button type="submit" name="clear_notifications" class="btn-clear">Clear All</button>
                        </form>
                    </div>

                    <?php
                    $notifStmt = $pdo->prepare("SELECT * FROM lock_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 15");
                    $notifStmt->execute([$userId]);
                    $notifs = $notifStmt->fetchAll(PDO::FETCH_ASSOC);

                    if (empty($notifs)): ?>
                        <p style="color: var(--text-muted); text-align: center; padding: 20px;">No notifications for your account.</p>
                    <?php else: ?>
                        <?php foreach ($notifs as $n): ?>
                            <div style="padding: 15px; border-bottom: 1px solid rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px;">
                                <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--primary-color);"></div>
                                <div>
                                    <p style="margin: 0; font-weight: 500;">Door <?php echo $n['action']; ?>ed via <?php echo strtoupper($n['device']); ?></p>
                                    <span style="font-size: 12px; color: var(--text-muted);"><?php echo date('M d, Y - h:i A', strtotime($n['created_at'])); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            <?php elseif ($tab == 'settings'): ?>
                <?php include 'user_settings.php'; ?>
            <?php endif; ?>

        </main>
    </div>

    <!-- Mobile Navigation -->
    <nav class="mobile-nav">
        <a href="?tab=overview" class="mobile-nav-item <?php echo $tab == 'overview' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="?tab=history" class="mobile-nav-item <?php echo $tab == 'history' ? 'active' : ''; ?>">
            <i class="fas fa-clock"></i>
            <span>History</span>
        </a>
        <a href="?tab=notifications" class="mobile-nav-item <?php echo $tab == 'notifications' ? 'active' : ''; ?>">
            <i class="fas fa-bell"></i>
            <span>Alerts</span>
        </a>
        <a href="?tab=settings" class="mobile-nav-item <?php echo $tab == 'settings' ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
        <a href="logout.php" class="mobile-nav-item logout-link">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </nav>

    <script>
        let currentLockStatus = "<?= $currentStatus ?>";
        const btn = document.getElementById('lock-toggle-btn');

        if(btn) {
            btn.addEventListener('click', function () {
                const action = (currentLockStatus === 'Unlocked') ? 'lock' : 'unlock';
                btn.disabled = true;
                btn.innerText = "Connecting...";

                fetch('toggle_lock_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=' + action
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        currentLockStatus = (currentLockStatus === 'Unlocked') ? 'Locked' : 'Unlocked';
                        updateUI(currentLockStatus === 'Locked');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => {
                    console.log("API error:", err);
                    currentLockStatus = (currentLockStatus === 'Unlocked') ? 'Locked' : 'Unlocked';
                    updateUI(currentLockStatus === 'Locked');
                })
                .finally(() => btn.disabled = false);
            });
        }

        function updateUI(isLocked) {
            const statusText = document.getElementById('lock-status-text');
            const icon = document.getElementById('lock-icon');
            const iconContainer = document.getElementById('lock-icon-container');

            if (isLocked) {
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