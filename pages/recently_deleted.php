<?php
/**
 * Page: recently_deleted
 * Included by index.php when ?page=recently_deleted
 */
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    $dept_id = $_SESSION['dept_id'];
    
    if ($role == 'admin') {
        $deleted = $conn->query("
            SELECT d.*, u1.full_name as deleted_by_name, u2.full_name as requested_by_name, 
                   d1.dept_name as from_dept_name
            FROM deleted_requests d 
            LEFT JOIN users u1 ON d.deleted_by = u1.user_id 
            LEFT JOIN users u2 ON d.requested_by = u2.user_id 
            LEFT JOIN departments d1 ON d.from_dept = d1.dept_id 
            ORDER BY d.deleted_at DESC
        ");
    } else {
        $deleted = $conn->query("
            SELECT d.*, u1.full_name as deleted_by_name, u2.full_name as requested_by_name, 
                   d1.dept_name as from_dept_name
            FROM deleted_requests d 
            LEFT JOIN users u1 ON d.deleted_by = u1.user_id 
            LEFT JOIN users u2 ON d.requested_by = u2.user_id 
            LEFT JOIN departments d1 ON d.from_dept = d1.dept_id 
            WHERE d.deleted_by = $user_id OR d.requested_by = $user_id OR d.from_dept = $dept_id
            ORDER BY d.deleted_at DESC
        ");
    }
    
    if (isset($restore_success)): ?>
        <div class="alert alert-success"><?php echo $restore_success; ?></div>
    <?php endif; ?>
    <?php if (isset($permanent_success)): ?>
        <div class="alert alert-success"><?php echo $permanent_success; ?></div>
    <?php endif; ?>
<h2><i class="fas fa-trash-restore" style="color:var(--accent);"></i> Recently Deleted Requests</h2>
<p>Deleted requests can be restored within 30 days.</p>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-trash-alt"></i> Deleted Items</h3>
        <a href="?page=view_requests" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Active Requests
        </a>
    </div>
    
    <?php if ($deleted && $deleted->num_rows > 0): ?>
    <div class="table-container">
        <table class="data-table" id="deletedTable">
            <thead>
                <tr>
                    <th>Request #</th>
                    <th>Type</th>
                    <th>From</th>
                    <th>Deleted By</th>
                    <th>Deleted Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($d = $deleted->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($d['request_number']); ?></strong></td>
                    <td><span class="request-type-tag"><?php echo htmlspecialchars($d['request_type']); ?></span></td>
                    <td><?php echo htmlspecialchars($d['from_dept_name'] ?? 'Unknown'); ?></td>
                    <td><?php echo htmlspecialchars($d['deleted_by_name'] ?? 'Unknown'); ?></td>
                    <td><?php echo date('M d, Y h:i A', strtotime($d['deleted_at'])); ?></td>
                    <td>
                        <div class="action-buttons">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $d['delete_id']; ?>">
                                <button type="submit" name="restore_request" class="btn btn-success btn-sm" onclick="return confirm('Restore this request?')">
                                    <i class="fas fa-trash-restore"></i> Restore
                                </button>
                            </form>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $d['delete_id']; ?>">
                                <button type="submit" name="permanent_delete" class="btn btn-danger btn-sm" onclick="return confirm('⚠️ Permanently delete this request? This action cannot be undone!')">
                                    <i class="fas fa-times-circle"></i> Permanent
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
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-trash-alt"></i>
        <h4>No Deleted Requests</h4>
        <p>No deleted requests found in the archive.</p>
        <a href="?page=view_requests" class="btn btn-primary" style="margin-top:12px;">
            <i class="fas fa-arrow-left"></i> Go to Active Requests
        </a>
    </div>
    <?php endif; ?>
</div>

