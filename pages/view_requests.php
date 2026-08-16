<?php
/**
 * Page: view_requests
 * Included by index.php when ?page=view_requests
 */
    $dept_id = $_SESSION['dept_id'];
    $role = $_SESSION['role'];
    $user_id = $_SESSION['user_id'];
    
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $filter = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';
    
    $where = [];
    if ($role != 'admin') {
        $where[] = "(r.requested_by = $user_id OR EXISTS (SELECT 1 FROM request_recipients rr WHERE rr.request_id = r.request_id AND rr.dept_id = $dept_id))";
    }
    
    if (!empty($search)) {
        $where[] = "(r.request_number LIKE '%$search%' OR r.title LIKE '%$search%' OR r.request_type LIKE '%$search%' OR u.full_name LIKE '%$search%')";
    }
    if (!empty($filter)) {
        $where[] = "r.status = '$filter'";
    }
    $where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    
    $requests = $conn->query("
        SELECT DISTINCT r.*, d.dept_name as from_name, u.full_name as requester,
               (SELECT COUNT(*) FROM request_recipients rr WHERE rr.request_id = r.request_id) as recipient_count
        FROM requests r
        JOIN departments d ON r.from_dept = d.dept_id
        JOIN users u ON r.requested_by = u.user_id
        $where_sql
        ORDER BY r.created_at DESC
    ");
    
    $all_requests = $conn->query("SELECT request_number, title, request_type FROM requests ORDER BY created_at DESC LIMIT 50");
    $suggestions = [];
    while($row = $all_requests->fetch_assoc()) {
        $suggestions[] = $row['request_number'] . " - " . $row['title'] . " (" . $row['request_type'] . ")";
    }
    $suggestions_json = json_encode($suggestions);
?>
<h2><i class="fas fa-list" style="color:var(--accent);"></i> All Requests</h2>
<p>View and manage all internal requests with department tracking.</p>

<?php if (isset($delete_success)): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $delete_success; ?></div>
<?php endif; ?>
<?php if (isset($delete_error)): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $delete_error; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-filter"></i> Filter Requests</h3>
        <div style="display:flex;gap:8px;">
            <a href="?page=create_request" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> New Request
            </a>
            <a href="?page=documentation" class="btn btn-success btn-sm">
                <i class="fas fa-archive"></i> Documentation
            </a>
            <a href="?page=recently_deleted" class="btn btn-outline btn-sm">
                <i class="fas fa-trash-restore"></i> Deleted
            </a>
        </div>
    </div>
    
    <div class="search-container" style="position:relative;">
        <div class="search-input-group">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" class="search-input" value="<?php echo htmlspecialchars($search); ?>" autocomplete="off" placeholder="Search by request number, title, type, or requester...">
            <div id="searchSuggestions" class="autocomplete-suggestions"></div>
        </div>
        <select id="statusFilter" class="filter-select">
            <option value="">All Status</option>
            <option value="pending" <?php echo $filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="seen" <?php echo $filter == 'seen' ? 'selected' : ''; ?>>Seen</option>
            <option value="approved" <?php echo $filter == 'approved' ? 'selected' : ''; ?>>Approved</option>
            <option value="rejected" <?php echo $filter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            <option value="in_progress" <?php echo $filter == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
            <option value="completed" <?php echo $filter == 'completed' ? 'selected' : ''; ?>>Completed</option>
        </select>
        <button id="searchBtn" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
        <button id="resetBtn" class="btn btn-outline"><i class="fas fa-times"></i> Reset</button>
    </div>
</div>

<div class="card">
    <?php if ($requests && $requests->num_rows > 0): ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th># / Type</th>
                    <th>From</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Recipients</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($r = $requests->fetch_assoc()): 
                    $recipients = $conn->query("
                        SELECT d.dept_name, rr.status 
                        FROM request_recipients rr 
                        JOIN departments d ON rr.dept_id = d.dept_id 
                        WHERE rr.request_id = {$r['request_id']}
                    ");
                    $recipient_count = $recipients->num_rows;
                    $is_sender = ($r['requested_by'] == $user_id);
                ?>
                <tr>
                    <td>
                        <div><strong><?php echo htmlspecialchars($r['request_number']); ?></strong></div>
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
                        <span class="track-badge">
                            <i class="fas fa-users"></i>
                            <?php echo $recipient_count; ?> dept(s)
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($r['created_at'])); ?></td>
                    <td>
                        <div style="display:flex;gap:4px;flex-wrap:wrap;">
                            <a href="?page=request_details&id=<?php echo $r['request_id']; ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-eye"></i> Track
                            </a>
                            <?php 
                            $attachments_check = $conn->query("SELECT COUNT(*) as c FROM request_attachments WHERE request_id = {$r['request_id']}");
                            $has_attachments = $attachments_check && $attachments_check->fetch_assoc()['c'] > 0;
                            ?>
                            <?php if ($has_attachments): ?>
                            <a href="?page=request_details&id=<?php echo $r['request_id']; ?>#attachments" class="btn btn-primary btn-sm" title="View Attachments">
                                <i class="fas fa-paperclip"></i>
                            </a>
                            <?php endif; ?>
                            <?php if ($role == 'admin' || $r['requested_by'] == $user_id || $r['from_dept'] == $dept_id): ?>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this request? It will be moved to Recently Deleted.');">
                                <input type="hidden" name="request_id" value="<?php echo $r['request_id']; ?>">
                                <button type="submit" name="delete_request" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i>
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
        <i class="fas fa-inbox"></i>
        <h4>No Requests Found</h4>
        <p>No requests match your search criteria.</p>
        <a href="?page=create_request" class="btn btn-primary" style="margin-top:12px;">
            <i class="fas fa-plus"></i> Create Request
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
const suggestions = <?php echo $suggestions_json; ?>;
const searchInput = document.getElementById('searchInput');
const suggestionsDiv = document.getElementById('searchSuggestions');

searchInput.addEventListener('input', function() {
    const value = this.value.toLowerCase();
    if (value.length < 2) {
        suggestionsDiv.style.display = 'none';
        return;
    }
    
    const filtered = suggestions.filter(s => s.toLowerCase().includes(value));
    if (filtered.length > 0) {
        suggestionsDiv.innerHTML = filtered.map(s => `<div class="autocomplete-suggestion">${s}</div>`).join('');
        suggestionsDiv.style.display = 'block';
        
        document.querySelectorAll('.autocomplete-suggestion').forEach(el => {
            el.addEventListener('click', function() {
                searchInput.value = this.textContent;
                suggestionsDiv.style.display = 'none';
                document.getElementById('searchBtn').click();
            });
        });
    } else {
        suggestionsDiv.style.display = 'none';
    }
});

document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target)) {
        suggestionsDiv.style.display = 'none';
    }
});

document.getElementById('searchBtn').addEventListener('click', function() {
    var search = document.getElementById('searchInput').value;
    var filter = document.getElementById('statusFilter').value;
    window.location.href = '?page=view_requests&search=' + encodeURIComponent(search) + '&filter=' + encodeURIComponent(filter);
});

document.getElementById('resetBtn').addEventListener('click', function() {
    window.location.href = '?page=view_requests';
});

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('searchBtn').click();
    }
});
</script>

