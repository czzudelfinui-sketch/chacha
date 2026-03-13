<div class="user-module-container" style="margin-top: 25px;">
    <div class="activity-header">
        <h2 style="font-size: 20px; color: #1B2559;">System Settings</h2>
    </div>
    
    <div style="margin-top: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <div style="background: #F4F7FE; padding: 20px; border-radius: 15px;">
            <h4 style="margin-bottom: 15px; color: #2B3674;">Security Configuration</h4>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0;">
                <span style="font-size: 14px; color: #A3AED0;">Two-Factor Authentication</span>
                <span style="color: #01B574; font-weight: 600;">Enabled</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0;">
                <span style="font-size: 14px; color: #A3AED0;">Auto-Lock Timeout</span>
                <span style="color: #2B3674; font-weight: 600;">30 Seconds</span>
            </div>
        </div>

        <div style="background: #F4F7FE; padding: 20px; border-radius: 15px;">
            <h4 style="margin-bottom: 15px; color: #EE5D50;">Danger Zone</h4>
            <p style="font-size: 13px; color: #A3AED0; margin-bottom: 15px;">Perform a factory reset of the entire ShieldNet system. This action is irreversible.</p>
            <button class="btn-auth" style="background-color: #EE5D50; border-radius: 12px; padding: 10px;" onclick="if(confirm('Are you absolutely sure you want to reset the system? All data will be lost.')) alert('System Reset Initiated (Simulated)');">
                Reset System
            </button>
        </div>
    </div>
</div>
