<?php
/**
 * Page: documentation
 * Included by index.php when ?page=documentation
 */
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    $dept_id = $_SESSION['dept_id'];
    
    $date_filter = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : '';
    $status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
    $dept_filter = isset($_GET['dept']) ? intval($_GET['dept']) : 0;
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    
    $conditions = [];
    $conditions[] = "1=1";
    
    if ($role != 'admin') {
        $conditions[] = "(r.requested_by = $user_id OR EXISTS (SELECT 1 FROM request_recipients rr WHERE rr.request_id = r.request_id AND rr.dept_id = $dept_id))";
    }
    if (!empty($date_filter)) {
        $conditions[] = "DATE(r.created_at) = '$date_filter'";
    }
    if (!empty($status_filter)) {
        $conditions[] = "r.status = '$status_filter'";
    }
    // FIXED: Department filter available for all users (removed admin check)
    if ($dept_filter > 0) {
        $conditions[] = "(r.from_dept = $dept_filter OR EXISTS (SELECT 1 FROM request_recipients rr WHERE rr.request_id = r.request_id AND rr.dept_id = $dept_filter))";
    }
    if (!empty($search)) {
        $conditions[] = "(r.request_number LIKE '%$search%' OR r.request_type LIKE '%$search%' OR r.title LIKE '%$search%')";
    }
    
    $where_sql = "WHERE " . implode(" AND ", $conditions);
    
    $requests = $conn->query("
        SELECT r.*, 
               d.dept_name as from_name, 
               u.full_name as requester,
               (SELECT COUNT(*) FROM request_recipients rr WHERE rr.request_id = r.request_id) as recipient_count,
               (SELECT COUNT(*) FROM request_attachments ra WHERE ra.request_id = r.request_id) as attachment_count
        FROM requests r
        JOIN departments d ON r.from_dept = d.dept_id
        JOIN users u ON r.requested_by = u.user_id
        $where_sql
        ORDER BY r.created_at DESC
    ");
    
    $total_conditions = [];
    $total_conditions[] = "1=1";
    
    if ($role != 'admin') {
        $total_conditions[] = "(requested_by = $user_id OR EXISTS (SELECT 1 FROM request_recipients rr WHERE rr.request_id = requests.request_id AND rr.dept_id = $dept_id))";
    }
    if (!empty($date_filter)) {
        $total_conditions[] = "DATE(created_at) = '$date_filter'";
    }
    if (!empty($status_filter)) {
        $total_conditions[] = "status = '$status_filter'";
    }
    if ($dept_filter > 0) {
        $total_conditions[] = "(from_dept = $dept_filter OR EXISTS (SELECT 1 FROM request_recipients rr WHERE rr.request_id = requests.request_id AND rr.dept_id = $dept_filter))";
    }
    if (!empty($search)) {
        $total_conditions[] = "(request_number LIKE '%$search%' OR request_type LIKE '%$search%' OR title LIKE '%$search%')";
    }
    
    $total_where = "WHERE " . implode(" AND ", $total_conditions);
    $total_result = $conn->query("SELECT COUNT(*) as c FROM requests $total_where");
    $total_requests = $total_result ? $total_result->fetch_assoc()['c'] : 0;
    
    $depts = $conn->query("SELECT * FROM departments ORDER BY dept_name");
?>
<h2><i class="fas fa-archive" style="color:var(--accent);"></i> Request Documentation & Archive</h2>
<p>Complete list of all requests with date tracking and status monitoring.</p>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-filter"></i> Filter & Search</h3>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="?page=documentation" class="btn btn-outline btn-sm">
                <i class="fas fa-times"></i> Reset
            </a>
        </div>
    </div>
    
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
        <input type="hidden" name="page" value="documentation">
        
        <div class="search-input-group" style="flex:1;min-width:150px;">
            <i class="fas fa-search"></i>
            <input type="text" name="search" class="search-input" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by request number or type...">
        </div>
        
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <label style="font-size:0.8rem;font-weight:600;color:var(--text-primary);">Date:</label>
            <input type="date" name="date" class="form-control" style="width:auto;min-width:180px;padding:10px 12px;" value="<?php echo $date_filter; ?>">
        </div>
        
        <select name="status" class="filter-select" style="padding:10px 16px;">
            <option value="">All Status</option>
            <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="seen" <?php echo $status_filter == 'seen' ? 'selected' : ''; ?>>Seen</option>
            <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Approved</option>
            <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            <option value="in_progress" <?php echo $status_filter == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
            <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Completed</option>
        </select>
        
        <!-- FIXED: Department filter available for all users -->
        <select name="dept" class="filter-select" style="padding:10px 16px;">
            <option value="0">All Departments</option>
            <?php 
            $depts->data_seek(0);
            while($d = $depts->fetch_assoc()): ?>
            <option value="<?php echo $d['dept_id']; ?>" <?php echo $dept_filter == $d['dept_id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($d['dept_name']); ?>
            </option>
            <?php endwhile; ?>
        </select>
        
        <button type="submit" class="btn btn-primary" id="applyFiltersBtn">
            <i class="fas fa-search"></i> Apply Filters
        </button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Request Archive (<?php echo $total_requests; ?> records)</h3>
        <span style="font-size:0.8rem;color:var(--text-secondary);">
            <i class="fas fa-calendar-alt"></i> 
            <?php if (!empty($date_filter)): ?>
                Filtered by date: <?php echo date('F d, Y', strtotime($date_filter)); ?>
            <?php else: ?>
                All time
            <?php endif; ?>
        </span>
    </div>
    
    <?php if ($requests && $requests->num_rows > 0): ?>
    <div class="table-container">
        <table class="data-table" id="archiveTable">
            <thead>
                <tr>
                    <th>Request #</th>
                    <th>Type</th>
                    <th>From</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Attachments</th>
                    <th>Date Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($r = $requests->fetch_assoc()): 
                    $is_sender = ($r['requested_by'] == $user_id);
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($r['request_number']); ?></strong></td>
                    <td>
                        <span class="request-type-tag"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($r['request_type']); ?></span>
                    </td>
                    <td>
                        <?php if ($is_sender): ?>
                            <span style="color:var(--accent);"><i class="fas fa-paper-plane"></i> You</span>
                        <?php else: ?>
                            <?php echo htmlspecialchars($r['from_name']); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo getPriorityBadge($r['priority']); ?></td>
                    <td><?php echo getStatusBadge($r['status']); ?></td>
                    <td>
                        <?php if ($r['attachment_count'] > 0): ?>
                            <span style="color:var(--accent);"><i class="fas fa-paperclip"></i> <?php echo $r['attachment_count']; ?></span>
                        <?php else: ?>
                            <span style="color:var(--text-secondary);">None</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:0.8rem;">
                        <?php echo date('M d, Y', strtotime($r['created_at'])); ?>
                        <div style="font-size:0.65rem;color:var(--text-secondary);">
                            <?php echo date('h:i A', strtotime($r['created_at'])); ?>
                        </div>
                    </td>
                    <td>
                        <a href="?page=request_details&id=<?php echo $r['request_id']; ?>" class="btn btn-secondary btn-xs">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-archive"></i>
        <h4>No Requests Found</h4>
        <p>No requests match your filter criteria.</p>
        <a href="?page=documentation" class="btn btn-primary" style="margin-top:12px;">
            <i class="fas fa-times"></i> Reset Filters
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.filter-select, input[type="date"]').forEach(el => {
    el.addEventListener('change', function() {});
});

document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('applyFiltersBtn').click();
    }
});
</script>

