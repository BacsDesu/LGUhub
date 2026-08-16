<?php
/**
 * Page: departments
 * Included by index.php when ?page=departments
 */
    $all_depts = $conn->query("SELECT * FROM departments ORDER BY dept_name ASC");
    $total_depts = $all_depts->num_rows;
    $is_admin = ($_SESSION['role'] == 'admin');
    
    if (isset($dept_success)): ?>
        <div class="alert alert-success"><?php echo $dept_success; ?></div>
    <?php endif; ?>
    <?php if (isset($dept_error)): ?>
        <div class="alert alert-danger"><?php echo $dept_error; ?></div>
    <?php endif; ?>
<h2><i class="fas fa-building" style="color:var(--accent);"></i> Departments</h2>
<p>List of all LGU departments and offices with active user status.</p>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Department Directory</h3>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <span style="background:var(--input-bg);padding:4px 16px;border-radius:50px;font-size:0.8rem;color:var(--accent);border:1px solid var(--input-border);">
                <i class="fas fa-building"></i> <?php echo $total_depts; ?> Departments
            </span>
            <?php if ($is_admin): ?>
            <button onclick="openAddDeptModal()" class="add-dept-btn">
                <i class="fas fa-plus-circle"></i> Add Department
            </button>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="table-container">
        <table class="data-table" id="deptTable">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th style="width:120px;">Code</th>
                    <th>Department Name</th>
                    <th>Description</th>
                    <th style="width:140px;">Status</th>
                    <?php if ($is_admin): ?>
                    <th style="width:140px;">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                $counter = 1;
                if ($all_depts && $all_depts->num_rows > 0):
                    while($dept = $all_depts->fetch_assoc()): 
                        $dept_icon = getDeptIcon($dept['dept_code'], $dept['dept_name']);
                        
                        $dept_users = $conn->query("SELECT COUNT(*) as c FROM users WHERE dept_id = {$dept['dept_id']} AND status = 'active'");
                        $active_users = $dept_users ? $dept_users->fetch_assoc()['c'] : 0;
                        
                        $total_dept_users = $conn->query("SELECT COUNT(*) as c FROM users WHERE dept_id = {$dept['dept_id']}");
                        $total_users = $total_dept_users ? $total_dept_users->fetch_assoc()['c'] : 0;
                        
                        if ($total_users > 0 && $active_users > 0) {
                            $status_label = 'Active';
                            $status_class = 'status-active';
                            $status_icon = 'fa-check-circle';
                        } elseif ($total_users > 0 && $active_users == 0) {
                            $status_label = 'Inactive';
                            $status_class = 'status-inactive';
                            $status_icon = 'fa-user-slash';
                        } else {
                            $status_label = 'No Users';
                            $status_class = 'status-pending';
                            $status_icon = 'fa-users-slash';
                        }
                ?>
                <tr>
                    <td><span class="dept-number"><?php echo str_pad($counter, 2, '0', STR_PAD_LEFT); ?></span></td>
                    <td><span class="dept-code-badge"><i class="fas <?php echo $dept_icon; ?>"></i> <?php echo htmlspecialchars($dept['dept_code']); ?></span></td>
                    <td>
                        <div class="dept-name-cell">
                            <div class="dept-icon"><i class="fas <?php echo $dept_icon; ?>"></i></div>
                            <span><?php echo htmlspecialchars($dept['dept_name']); ?></span>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($dept['description'] ?? 'No description'); ?></td>
                    <td>
                        <span class="status-badge <?php echo $status_class; ?>" title="<?php echo $active_users; ?>/<?php echo $total_users; ?> users active">
                            <i class="fas <?php echo $status_icon; ?>"></i> 
                            <?php echo $status_label; ?>
                            <?php if ($total_users > 0): ?>
                                <small style="font-size:0.6rem;opacity:0.7;">(<?php echo $active_users; ?>/<?php echo $total_users; ?>)</small>
                            <?php endif; ?>
                        </span>
                    </td>
                    <?php if ($is_admin): ?>
                    <td>
                        <div class="action-buttons">
                            <button onclick="openEditDeptModal(<?php echo $dept['dept_id']; ?>, '<?php echo addslashes($dept['dept_code']); ?>', '<?php echo addslashes($dept['dept_name']); ?>', '<?php echo addslashes($dept['description'] ?? ''); ?>')" class="btn btn-secondary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                <input type="hidden" name="dept_id" value="<?php echo $dept['dept_id']; ?>">
                                <button type="submit" name="delete_department" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php 
                        $counter++;
                    endwhile; 
                else: 
                ?>
                <tr><td colspan="<?php echo $is_admin ? '6' : '5'; ?>" class="text-center" style="color:var(--text-secondary);">No departments found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="addDeptModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle"></i> Add New Department</h3>
            <span class="modal-close" onclick="closeAddDeptModal()">&times;</span>
        </div>
        <form method="POST">
            <div class="form-group">
                <label>Department Code <span class="required">*</span></label>
                <input type="text" name="dept_code" class="form-control" placeholder="e.g., NEWDEPT" required>
            </div>
            <div class="form-group">
                <label>Department Name <span class="required">*</span></label>
                <input type="text" name="dept_name" class="form-control" placeholder="e.g., New Department" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="dept_description" class="form-control" rows="3" placeholder="Brief description..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeAddDeptModal()">Cancel</button>
                <button type="submit" name="add_department" class="btn btn-primary">
                    <i class="fas fa-save"></i> Add Department
                </button>
            </div>
        </form>
    </div>
</div>

<div id="editDeptModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Department</h3>
            <span class="modal-close" onclick="closeEditDeptModal()">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" name="dept_id" id="edit_dept_id">
            <div class="form-group">
                <label>Department Code <span class="required">*</span></label>
                <input type="text" name="dept_code" id="edit_dept_code" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Department Name <span class="required">*</span></label>
                <input type="text" name="dept_name" id="edit_dept_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="dept_description" id="edit_dept_description" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeEditDeptModal()">Cancel</button>
                <button type="submit" name="update_department" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddDeptModal() { document.getElementById('addDeptModal').classList.add('active'); }
function closeAddDeptModal() { document.getElementById('addDeptModal').classList.remove('active'); }

function openEditDeptModal(id, code, name, description) {
    document.getElementById('edit_dept_id').value = id;
    document.getElementById('edit_dept_code').value = code;
    document.getElementById('edit_dept_name').value = name;
    document.getElementById('edit_dept_description').value = description;
    document.getElementById('editDeptModal').classList.add('active');
}
function closeEditDeptModal() { document.getElementById('editDeptModal').classList.remove('active'); }

document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>

