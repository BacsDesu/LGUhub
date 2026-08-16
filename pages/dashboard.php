<?php
/**
 * Page: dashboard
 * Included by index.php when ?page=dashboard
 */
    $dept_id = $_SESSION['dept_id'];
    $role = $_SESSION['role'];
    $user_id = $_SESSION['user_id'];
    
    if ($role == 'admin') {
        $all_stats = getRequestStatusSummary($conn);
    } else {
        $total = 0;
        $pending = 0;
        $approved = 0;
        $rejected = 0;
        $in_progress = 0;
        $completed = 0;
        
        $result = $conn->query("
            SELECT COUNT(*) as c FROM requests r 
            WHERE r.requested_by = $user_id
            OR EXISTS (SELECT 1 FROM request_recipients rr WHERE rr.request_id = r.request_id AND rr.dept_id = $dept_id)
        ");
        if ($result) $total = $result->fetch_assoc()['c'];
        
        $result = $conn->query("
            SELECT COUNT(*) as c FROM requests r 
            WHERE (r.requested_by = $user_id
            OR EXISTS (SELECT 1 FROM request_recipients rr WHERE rr.request_id = r.request_id AND rr.dept_id = $dept_id))
            AND r.status = 'pending'
        ");
        if ($result) $pending = $result->fetch_assoc()['c'];
        
        $result = $conn->query("
            SELECT COUNT(*) as c FROM requests r 
            WHERE (r.requested_by = $user_id
            OR EXISTS (SELECT 1 FROM request_recipients rr WHERE rr.request_id = r.request_id AND rr.dept_id = $dept_id))
            AND r.status = 'approved'
        ");
        if ($result) $approved = $result->fetch_assoc()['c'];
        
        $result = $conn->query("
            SELECT COUNT(*) as c FROM requests r 
            WHERE (r.requested_by = $user_id
            OR EXISTS (SELECT 1 FROM request_recipients rr WHERE rr.request_id = r.request_id AND rr.dept_id = $dept_id))
            AND r.status = 'rejected'
        ");
        if ($result) $rejected = $result->fetch_assoc()['c'];
        
        $result = $conn->query("
            SELECT COUNT(*) as c FROM requests r 
            WHERE (r.requested_by = $user_id
            OR EXISTS (SELECT 1 FROM request_recipients rr WHERE rr.request_id = r.request_id AND rr.dept_id = $dept_id))
            AND r.status = 'in_progress'
        ");
        if ($result) $in_progress = $result->fetch_assoc()['c'];
        
        $result = $conn->query("
            SELECT COUNT(*) as c FROM requests r 
            WHERE (r.requested_by = $user_id
            OR EXISTS (SELECT 1 FROM request_recipients rr WHERE rr.request_id = r.request_id AND rr.dept_id = $dept_id))
            AND r.status = 'completed'
        ");
        if ($result) $completed = $result->fetch_assoc()['c'];
        
        $all_stats = [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'in_progress' => $in_progress,
            'completed' => $completed
        ];
    }
    
    $pending_users = 0;
    if ($role == 'admin') {
        $pending_users = $conn->query("SELECT COUNT(*) as c FROM users WHERE status = 'pending'")->fetch_assoc()['c'];
    }
    
    if ($role == 'admin') {
        $recent = $conn->query("
            SELECT r.*, d.dept_name as from_name, u.full_name as requester,
                   (SELECT COUNT(*) FROM request_recipients rr WHERE rr.request_id = r.request_id) as recipient_count
            FROM requests r
            JOIN departments d ON r.from_dept = d.dept_id
            JOIN users u ON r.requested_by = u.user_id
            ORDER BY r.created_at DESC LIMIT 8
        ");
    } else {
        $recent = $conn->query("
            SELECT DISTINCT r.*, d.dept_name as from_name, u.full_name as requester,
                   (SELECT COUNT(*) FROM request_recipients rr WHERE rr.request_id = r.request_id) as recipient_count
            FROM requests r
            JOIN departments d ON r.from_dept = d.dept_id
            JOIN users u ON r.requested_by = u.user_id
            WHERE r.requested_by = $user_id
            OR EXISTS (SELECT 1 FROM request_recipients rr WHERE rr.request_id = r.request_id AND rr.dept_id = $dept_id)
            ORDER BY r.created_at DESC LIMIT 8
        ");
    }
?>
<h2>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! 👋</h2>
<p>
    <i class="fas fa-building" style="color:var(--accent);"></i> 
    <?php echo htmlspecialchars(getDeptName($conn, $_SESSION['dept_id'])); ?> 
    <i class="fas fa-user-tag"></i> <?php echo ucfirst($_SESSION['role']); ?>
</p>

<div class="stats-grid">
    <div class="stat-card stat-total">
        <span class="stat-icon"><i class="fas fa-file-alt"></i></span>
        <div class="stat-number"><?php echo $all_stats['total']; ?></div>
        <div class="stat-label">Total Requests</div>
    </div>
    <div class="stat-card stat-pending">
        <span class="stat-icon"><i class="fas fa-clock"></i></span>
        <div class="stat-number"><?php echo $all_stats['pending']; ?></div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card stat-approved">
        <span class="stat-icon"><i class="fas fa-check-circle"></i></span>
        <div class="stat-number"><?php echo $all_stats['approved']; ?></div>
        <div class="stat-label">Approved</div>
    </div>
    <div class="stat-card stat-rejected">
        <span class="stat-icon"><i class="fas fa-times-circle"></i></span>
        <div class="stat-number"><?php echo $all_stats['rejected']; ?></div>
        <div class="stat-label">Rejected</div>
    </div>
    <div class="stat-card stat-in_progress">
        <span class="stat-icon"><i class="fas fa-spinner"></i></span>
        <div class="stat-number"><?php echo $all_stats['in_progress']; ?></div>
        <div class="stat-label">In Progress</div>
    </div>
    <div class="stat-card stat-completed">
        <span class="stat-icon"><i class="fas fa-flag-checkered"></i></span>
        <div class="stat-number"><?php echo $all_stats['completed']; ?></div>
        <div class="stat-label">Completed</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-bolt" style="color:var(--accent);"></i> Quick Actions</h3>
        <?php if ($role == 'admin' && $pending_users > 0): ?>
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="background:rgba(201,138,30,0.15);color:#C98A1E;padding:4px 14px;border-radius:var(--radius-pill);font-size:0.75rem;font-weight:600;">
                <i class="fas fa-users"></i> <?php echo $pending_users; ?> pending users
            </span>
            <a href="?page=users" class="btn btn-primary btn-sm">
                <i class="fas fa-users-cog"></i> Manage
            </a>
        </div>
        <?php endif; ?>
    </div>
    <div class="quick-actions">
        <a href="?page=create_request" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Create Request
        </a>
        <a href="?page=view_requests" class="btn btn-secondary">
            <i class="fas fa-list"></i> View All
        </a>
        <a href="?page=documentation" class="btn btn-success">
            <i class="fas fa-archive"></i> Documentation
        </a>
        <a href="?page=recently_deleted" class="btn btn-outline">
            <i class="fas fa-trash-restore"></i> Deleted
        </a>
        <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="?page=settings" class="btn btn-outline">
            <i class="fas fa-cog"></i> Settings
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-clock"></i> Recent Requests</h3>
        <div style="display:flex;gap:8px;">
            <a href="?page=create_request" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> New Request
            </a>
            <a href="?page=view_requests" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> View All
            </a>
        </div>
    </div>
    
    <?php if ($recent && $recent->num_rows > 0): ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Request #</th>
                    <th>Type</th>
                    <th>From</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Recipients</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($r = $recent->fetch_assoc()): 
                    $is_sender = ($r['requested_by'] == $user_id);
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($r['request_number']); ?></strong></td>
                    <td><span class="request-type-tag"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($r['request_type']); ?></span></td>
                    <td>
                        <?php if ($is_sender): ?>
                            <span style="color:var(--accent);"><i class="fas fa-paper-plane"></i> Sent by you</span>
                        <?php else: ?>
                            <?php echo htmlspecialchars($r['from_name']); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo getPriorityBadge($r['priority']); ?></td>
                    <td><?php echo getStatusBadge($r['status']); ?></td>
                    <td>
                        <span style="font-size:0.8rem;color:var(--text-secondary);">
                            <i class="fas fa-users"></i> <?php echo $r['recipient_count']; ?> dept(s)
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($r['created_at'])); ?></td>
                    <td>
                        <a href="?page=request_details&id=<?php echo $r['request_id']; ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-eye"></i> Track
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h4>No Requests Yet</h4>
        <p>Start by creating your first request.</p>
        <a href="?page=create_request" class="btn btn-primary" style="margin-top:12px;">
            <i class="fas fa-plus"></i> Create Request
        </a>
    </div>
    <?php endif; ?>
</div>

