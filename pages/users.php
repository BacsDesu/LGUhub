<?php
/**
 * Page: users
 * Included by index.php when ?page=users
 */
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') { header('Location: ?page=dashboard'); exit(); }
    $users = $conn->query("
        SELECT u.*, d.dept_name, d.dept_code 
        FROM users u 
        LEFT JOIN departments d ON u.dept_id = d.dept_id 
        ORDER BY FIELD(u.status, 'pending', 'active', 'inactive'), u.created_at DESC
    ");
    
    if (isset($edit_success)): ?>
        <div class="alert alert-success"><?php echo $edit_success; ?></div>
    <?php endif; ?>
    <?php if (isset($status_success)): ?>
        <div class="alert alert-success"><?php echo $status_success; ?></div>
    <?php endif; ?>
    <?php if (isset($approve_success)): ?>
        <div class="alert alert-success"><?php echo $approve_success; ?></div>
    <?php endif; ?>
    <?php if (isset($reject_success)): ?>
        <div class="alert alert-success"><?php echo $reject_success; ?></div>
    <?php endif; ?>

<h2><i class="fas fa-users-cog" style="color:var(--accent);"></i> User Management</h2>
<p>Manage all system users, roles, account statuses, and theme preferences.</p>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users"></i> All Users</h3>
        <div style="display:flex;gap:8px;font-size:0.8rem;flex-wrap:wrap;">
            <span class="status-badge status-pending">Pending: <?php echo $conn->query("SELECT COUNT(*) as c FROM users WHERE status = 'pending'")->fetch_assoc()['c']; ?></span>
            <span class="status-badge status-active">Active: <?php echo $conn->query("SELECT COUNT(*) as c FROM users WHERE status = 'active'")->fetch_assoc()['c']; ?></span>
            <span class="status-badge status-inactive">Inactive: <?php echo $conn->query("SELECT COUNT(*) as c FROM users WHERE status = 'inactive'")->fetch_assoc()['c']; ?></span>
        </div>
    </div>
    
    <div class="table-container">
        <table class="data-table" id="userTable">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Department</th>
                    <th>Role</th>
                    <th>Theme</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($u = $users->fetch_assoc()): 
                    $user_icon = getDeptIcon($u['dept_code'], $u['dept_name']);
                    $user_theme_display = $u['theme_preference'] ?: 'dark';
                    $theme_labels = [
                        'dark' => '🌙 Dark',
                        'blue' => '🔵 Blue',
                        'green' => '🟢 Green',
                        'purple' => '🟣 Purple',
                        'pink' => '🌸 Pink',
                        'orange' => '🟠 Orange',
                        'teal' => '🩵 Teal'
                    ];
                    $theme_label = $theme_labels[$user_theme_display] ?? '🌙 Dark';
                ?>
                <tr>
                    <td>
                        <div class="user-info-cell">
                            <div class="user-avatar-cell">
                                <i class="fas <?php echo $user_icon; ?>"></i>
                            </div>
                            <div class="user-details-cell">
                                <div class="user-name"><?php echo htmlspecialchars($u['full_name']); ?></div>
                                <div class="user-username">@<?php echo htmlspecialchars($u['username']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span class="dept-tag-cell"><i class="fas <?php echo $user_icon; ?>"></i> <?php echo htmlspecialchars($u['dept_name'] ?? 'N/A'); ?></span></td>
                    <td>
                        <?php if($u['role'] == 'admin'): ?>
                            <span class="role-badge-admin"><i class="fas fa-crown"></i> Administrator</span>
                        <?php else: ?>
                            <span class="role-badge-staff"><i class="fas fa-user"></i> Staff</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span style="background:var(--input-bg);padding:4px 10px;border-radius:20px;font-size:0.7rem;border:1px solid var(--input-border);">
                            <?php echo $theme_label; ?>
                        </span>
                    </td>
                    <td>
                        <?php if($u['status'] == 'pending'): ?>
                            <span class="status-badge status-pending"><i class="fas fa-clock"></i> Pending</span>
                        <?php elseif($u['status'] == 'active'): ?>
                            <span class="status-badge status-active"><i class="fas fa-check-circle"></i> Active</span>
                        <?php else: ?>
                            <span class="status-badge status-inactive"><i class="fas fa-ban"></i> Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                    <td>
                        <div class="action-buttons">
                            <?php if($u['status'] == 'pending'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                                    <button type="submit" name="approve_user" class="btn btn-success btn-sm">
                                        <i class="fas fa-check-circle"></i> Approve
                                    </button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                                    <button type="submit" name="reject_user" class="btn btn-danger btn-sm">
                                        <i class="fas fa-times-circle"></i> Reject
                                    </button>
                                </form>
                            <?php else: ?>
                                <button onclick="openEditModal(<?php echo $u['user_id']; ?>, '<?php echo addslashes($u['full_name']); ?>', '<?php echo addslashes($u['username']); ?>', '<?php echo addslashes($u['email']); ?>', <?php echo $u['dept_id']; ?>, '<?php echo $u['role']; ?>', '<?php echo $u['theme_preference'] ?? 'dark'; ?>')" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                                    <button type="submit" name="toggle_status" class="btn <?php echo $u['status'] == 'active' ? 'btn-warning' : 'btn-success'; ?> btn-sm">
                                        <i class="fas fa-<?php echo $u['status'] == 'active' ? 'user-slash' : 'check-circle'; ?>"></i>
                                        <?php echo $u['status'] == 'active' ? 'Deactivate' : 'Activate'; ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="editUserModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit User</h3>
            <span class="modal-close" onclick="closeEditModal()">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" name="user_id" id="edit_user_id">
            <div class="form-group">
                <label>Full Name <span class="required">*</span></label>
                <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Username <span class="required">*</span></label>
                <input type="text" name="username" id="edit_username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email <span style="font-weight:400;color:var(--text-secondary);">(Optional)</span></label>
                <input type="email" name="email" id="edit_email" class="form-control">
            </div>
            <div class="form-group">
                <label>Department <span class="required">*</span></label>
                <select name="dept_id" id="edit_dept_id" class="form-control" required>
                    <?php $depts = $conn->query("SELECT * FROM departments ORDER BY dept_name"); 
                    while($d = $depts->fetch_assoc()): ?>
                        <option value="<?php echo $d['dept_id']; ?>"><?php echo htmlspecialchars($d['dept_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Role <span class="required">*</span></label>
                <select name="role" id="edit_role" class="form-control" required>
                    <option value="staff">Staff</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
            <div class="form-group">
                <label>Theme Preference</label>
                <select name="theme_preference" id="edit_theme" class="form-control">
                    <option value="dark">🌙 Dark</option>
                    <option value="blue">🔵 Blue</option>
                    <option value="green">🟢 Green</option>
                    <option value="purple">🟣 Purple</option>
                    <option value="pink">🌸 Pink</option>
                    <option value="orange">🟠 Orange</option>
                    <option value="teal">🩵 Teal</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeEditModal()">Cancel</button>
                <button type="submit" name="update_user_profile" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(userId, fullName, username, email, deptId, role, theme) {
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_full_name').value = fullName;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_dept_id').value = deptId;
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_theme').value = theme || 'dark';
    document.getElementById('editUserModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editUserModal').classList.remove('active');
}

document.getElementById('editUserModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>

