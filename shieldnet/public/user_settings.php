<div class="settings-container">
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
});
</script>
