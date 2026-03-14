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
            <h4 style="margin-bottom: 15px; color: #4318FF;">Auto-Unlock Schedule</h4>
            <div style="font-size: 13px; color: #A3AED0; margin-bottom: 15px;">Automatically keep main door unlocked during specific hours.</div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <input type="time" class="form-control" style="padding: 8px;" value="09:00">
                <span style="align-self: center;">to</span>
                <input type="time" class="form-control" style="padding: 8px;" value="17:00">
            </div>
            <button class="btn-auth" style="border-radius: 12px; padding: 10px; font-size: 12px;">Save Schedule</button>
        </div>

        <div style="background: #F4F7FE; padding: 20px; border-radius: 15px;">
            <h4 style="margin-bottom: 15px; color: #7551FF;">Guest Access</h4>
            <div style="font-size: 13px; color: #A3AED0; margin-bottom: 15px;">Generate a temporary digital key for visitors.</div>
            <button class="btn-auth" style="border-radius: 12px; padding: 10px; font-size: 12px; background: #7551FF;" onclick="alert('Guest Key Generated: SN-GUEST-8293 \nValid for 24 hours.')">Generate Guest Key</button>
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
