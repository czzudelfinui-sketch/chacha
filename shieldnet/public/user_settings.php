<div class="settings-container">
    <div class="settings-section">
        <h3 style="margin-bottom: 20px;">Profile Settings</h3>
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
            <div id="avatar-preview" class="user-avatar" style="width: 80px; height: 80px; font-size: 24px;">
                <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
            </div>
            <div>
                <input type="file" id="avatar-input" style="display: none;" accept="image/*">
                <button class="btn-auth" style="padding: 8px 15px; font-size: 13px;" onclick="document.getElementById('avatar-input').click()">Change Photo</button>
                <p style="font-size: 11px; color: var(--text-muted); margin-top: 5px;">JPG, PNG or GIF. Max 1MB.</p>
            </div>
        </div>
    </div>

    <div class="settings-section">
        <h3 style="margin-bottom: 20px;">Logged-in Devices</h3>
        <div class="settings-row">
            <div id="sessions-list" style="margin-top: 15px;">
                <?php
                if (!isset($pdo)) require_once 'db_connect.php';
                $sessStmt = $pdo->prepare("SELECT * FROM user_sessions WHERE user_id = ? ORDER BY last_activity DESC LIMIT 5");
                $sessStmt->execute([$_SESSION['user_id']]);
                $sessions = $sessStmt->fetchAll();
                foreach($sessions as $s):
                    $isCurrent = ($s['session_id'] === session_id());
                ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fas <?php echo strpos($s['device_info'], 'Mobile') !== false ? 'fa-mobile-alt' : 'fa-laptop'; ?>" style="color: var(--text-muted);"></i>
                        <div>
                            <p style="margin: 0; font-weight: 600; font-size: 13px;"><?php echo $isCurrent ? 'Current Session' : 'Device: ' . substr($s['ip_address'], 0, 15); ?></p>
                            <span style="font-size: 11px; color: var(--text-muted);"><?php echo date('M d, H:i', strtotime($s['last_activity'])); ?></span>
                        </div>
                    </div>
                    <?php if (!$isCurrent): ?>
                        <button style="border: none; background: none; color: #EE5D50; cursor: pointer; font-size: 11px; font-weight: 600;" onclick="alert('Session Revoked (Simulated)')">Logout</button>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="settings-section">
        <h3 style="margin-bottom: 20px;">Personalization</h3>
        <div class="settings-row">
            <div>
                <h4>Dark Mode</h4>
                <p style="color: var(--text-muted); font-size: 14px;">Toggle between light and dark themes.</p>
            </div>
            <label class="switch">
                <input type="checkbox" id="themeToggle" onchange="toggleTheme()">
                <span class="slider"></span>
            </label>
        </div>
    </div>

    <div class="settings-section">
        <h3 style="margin-bottom: 20px;">Security</h3>
        
        <form action="settings_actions.php" method="POST">
            <input type="hidden" name="action" value="change_password">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" class="form-control" placeholder="••••••••" required>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" placeholder="••••••••" required>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-auth" style="width: auto; padding: 12px 35px; border-radius: 15px; text-transform: none;">Update Password</button>
        </form>

        <div class="settings-row" style="margin-top: 40px; border-top: 1px solid var(--input-bg); padding-top: 30px;">
            <div>
                <h4>Auto-Lock Timer</h4>
                <p style="color: var(--text-muted); font-size: 14px;">Duration before the door locks automatically.</p>
            </div>
            <div style="text-align: right; min-width: 250px;">
                <input type="range" class="range-input" min="5" max="120" value="30" id="lockTimer" oninput="document.getElementById('timerVal').innerText = this.value">
                <p style="font-size: 14px; color: var(--primary-color); font-weight: 700; margin-top: 8px;"><span id="timerVal">30</span> Seconds</p>
            </div>
        </div>
    </div>

    <div class="settings-section">
        <h3 style="margin-bottom: 20px;">Notifications</h3>
        <div class="settings-row">
            <div>
                <h4>Push Notifications</h4>
                <p style="color: var(--text-muted); font-size: 14px;">Get alerts on your mobile device.</p>
            </div>
            <label class="switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
            </label>
        </div>
        <div class="settings-row">
            <div>
                <h4>Email Alerts</h4>
                <p style="color: var(--text-muted); font-size: 14px;">Receive security logs via email.</p>
            </div>
            <label class="switch">
                <input type="checkbox">
                <span class="slider"></span>
            </label>
        </div>
    </div>
</div>

<script>
function toggleTheme() {
    const isDark = document.getElementById('themeToggle').checked;
    document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
    localStorage.setItem('shieldnet-theme', isDark ? 'dark' : 'light');
}

document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('shieldnet-theme') || 'light';
    const themeToggle = document.getElementById('themeToggle');
    if(themeToggle) {
        themeToggle.checked = (savedTheme === 'dark');
    }

    const bioToggle = document.getElementById('bioToggle');
    if(bioToggle) {
        bioToggle.checked = (localStorage.getItem('shieldnet-bio') === 'true');
    }

    // Avatar Upload Handler
    const avatarInput = document.getElementById('avatar-input');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            if (e.target.files[0]) {
                const formData = new FormData();
                formData.append('avatar', e.target.files[0]);
                fetch('api_profile.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        const preview = document.getElementById('avatar-preview');
                        preview.style.backgroundImage = `url(${data.avatar_url})`;
                        preview.style.backgroundSize = 'cover';
                        preview.style.backgroundPosition = 'center';
                        preview.innerText = '';
                        location.reload(); 
                    }
                });
            }
        });
    }

    // Check if avatar exists on load
    fetch('api_profile.php').then(res => res.json()).then(data => {
        const preview = document.getElementById('avatar-preview');
        if(data.avatar_url && preview) {
            preview.style.backgroundImage = `url(${data.avatar_url})`;
            preview.style.backgroundSize = 'cover';
            preview.style.backgroundPosition = 'center';
            preview.innerText = '';
        }
    });
});
</script>
