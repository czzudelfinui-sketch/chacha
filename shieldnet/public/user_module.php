<?php
require_once 'db_connect.php';

$stmt = $pdo->query("SELECT id, full_name, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

$successMsg = '';
$errorMsg = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'added') $successMsg = "User created successfully!";
    if ($_GET['success'] == 'updated') $successMsg = "User updated successfully!";
    if ($_GET['success'] == 'deleted') $successMsg = "User deleted successfully!";
}
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'duplicate') $errorMsg = "Error: Email already registered!";
    if ($_GET['error'] == 'self_delete') $errorMsg = "Error: You cannot delete your own account!";
    if ($_GET['error'] == 'update_failed') $errorMsg = "Error updating user.";
}
?>

<div class="user-module-container" style="margin-top: 25px;">
    <div class="activity-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 20px; color: var(--text-main);">User Management</h2>
        <button class="btn-auth" style="width: auto; padding: 10px 20px; text-transform: none; border-radius: 12px;" onclick="document.getElementById('addUserModal').style.display='flex'">Add New User</button>
    </div>

    <?php if ($successMsg): ?>
        <div style="color: #01B574; background: rgba(1, 181, 116, 0.1); padding: 10px; border-radius: 8px; margin-top: 15px; font-size: 14px;">
            <i class="fas fa-check-circle"></i> <?php echo $successMsg; ?>
        </div>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
        <div style="color: #EE5D50; background: rgba(238, 93, 80, 0.1); padding: 10px; border-radius: 8px; margin-top: 15px; font-size: 14px;">
            <i class="fas fa-times-circle"></i> <?php echo $errorMsg; ?>
        </div>
    <?php endif; ?>
    
    <table class="user-table">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email Address</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td style="font-weight: 600; color: var(--text-main);"><?php echo htmlspecialchars($user['full_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td>
                    <span class="role-badge role-<?php echo $user['role']; ?>">
                        <?php echo $user['role']; ?>
                    </span>
                </td>
                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                <td>
                    <button class="action-btn" title="Edit" onclick="openUserEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)"><i class="fas fa-edit"></i></button>
                    <form action="user_actions.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                        <button type="submit" class="action-btn delete" title="Delete"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="addUserModal" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-card); padding: 30px; border-radius: 20px; width: 100%; max-width: 450px; box-shadow: var(--card-shadow);">
        <h3 style="margin-bottom: 20px; color: var(--text-main);">Add New User</h3>
        <form action="user_actions.php" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control" placeholder="E.g. John Doe" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 25px;">
                <button type="button" class="btn-auth" style="background: var(--text-muted); flex: 1;" onclick="this.closest('#addUserModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn-auth" style="flex: 2;">Create User</button>
            </div>
        </form>
    </div>
</div>

<div id="editUserModal" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-card); padding: 30px; border-radius: 20px; width: 100%; max-width: 450px; box-shadow: var(--card-shadow);">
        <h3 style="margin-bottom: 20px; color: var(--text-main);">Edit User</h3>
        <form action="user_actions.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_user_id">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>New Password (Leave blank to keep current)</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••">
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" id="edit_role" class="form-control" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 25px;">
                <button type="button" class="btn-auth" style="background: var(--text-muted); flex: 1;" onclick="this.closest('#editUserModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn-auth" style="flex: 2;">Update User</button>
            </div>
        </form>
    </div>
</div>

<script>
function openUserEditModal(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_full_name').value = user.full_name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('editUserModal').style.display = 'flex';
}
</script>
