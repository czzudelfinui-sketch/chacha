<?php
require_once 'db_connect.php';

$stmt = $pdo->query("SELECT r.*, u.full_name FROM rfid_cards r LEFT JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC");
$rfid_cards = $stmt->fetchAll();

$userStmt = $pdo->query("SELECT id, full_name FROM users ORDER BY full_name ASC");
$allUsers = $userStmt->fetchAll();

$successMsg = '';
$errorMsg = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'added') $successMsg = "Card added successfully!";
    if ($_GET['success'] == 'updated') $successMsg = "Card updated successfully!";
    if ($_GET['success'] == 'deleted') $successMsg = "Card deleted successfully!";
}
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'duplicate') $errorMsg = "Error: Card UID already exists!";
    if ($_GET['error'] == 'update_failed') $errorMsg = "Error updating card.";
}
?>

<div class="user-module-container" style="margin-top: 25px;">
    <div class="activity-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 20px; color: #1B2559;">RFID Management</h2>
        <button class="btn-auth" style="width: auto; padding: 10px 20px; text-transform: none; border-radius: 12px;" onclick="document.getElementById('addCardModal').style.display='flex'">Add New Card</button>
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
                <th>Card UID</th>
                <th>Assigned User</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rfid_cards as $card): ?>
            <tr>
                <td style="font-family: monospace; font-weight: 600; color: #2B3674;"><?php echo htmlspecialchars($card['card_uid']); ?></td>
                <td><?php echo $card['full_name'] ? htmlspecialchars($card['full_name']) : '<span style="color: #A3AED0; font-style: italic;">Unassigned</span>'; ?></td>
                <td>
                    <span class="role-badge role-<?php echo ($card['status'] == 'active' ? 'admin' : 'user'); ?>">
                        <?php echo $card['status']; ?>
                    </span>
                </td>
                <td><?php echo date('M d, Y', strtotime($card['created_at'])); ?></td>
                <td>
                    <button class="action-btn" title="Edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($card)); ?>)"><i class="fas fa-edit"></i></button>
                    <form action="rfid_actions.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this card?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $card['id']; ?>">
                        <button type="submit" class="action-btn delete" title="Delete"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div id="addCardModal" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; padding: 30px; border-radius: 20px; width: 100%; max-width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <h3 style="margin-bottom: 20px; color: #1B2559;">Add New RFID Card</h3>
        <form action="rfid_actions.php" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>Card UID</label>
                <input type="text" name="card_uid" class="form-control" placeholder="E.g. E2 80 68 31" required>
            </div>
            <div class="form-group">
                <label>Assign to User</label>
                <select name="user_id" class="form-control">
                    <option value="">-- No User --</option>
                    <?php foreach ($allUsers as $user): ?>
                        <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['full_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 25px;">
                <button type="button" class="btn-auth" style="background: #A3AED0; flex: 1;" onclick="this.closest('#addCardModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn-auth" style="flex: 2;">Save Card</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editCardModal" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; padding: 30px; border-radius: 20px; width: 100%; max-width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <h3 style="margin-bottom: 20px; color: #1B2559;">Edit RFID Card</h3>
        <form action="rfid_actions.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-group">
                <label>Card UID</label>
                <input type="text" name="card_uid" id="edit_card_uid" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Assign to User</label>
                <select name="user_id" id="edit_user_id" class="form-control">
                    <option value="">-- No User --</option>
                    <?php foreach ($allUsers as $user): ?>
                        <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['full_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="edit_status" class="form-control">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 25px;">
                <button type="button" class="btn-auth" style="background: #A3AED0; flex: 1;" onclick="this.closest('#editCardModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn-auth" style="flex: 2;">Update Card</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(card) {
    document.getElementById('edit_id').value = card.id;
    document.getElementById('edit_card_uid').value = card.card_uid;
    document.getElementById('edit_user_id').value = card.user_id || '';
    document.getElementById('edit_status').value = card.status;
    document.getElementById('editCardModal').style.display = 'flex';
}
</script>
