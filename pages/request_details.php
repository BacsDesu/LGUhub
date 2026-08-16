<?php
/**
 * Page: request_details
 * Included by index.php when ?page=request_details
 */
if (!isLoggedIn() || !isset($_GET['id'])) { header('Location: ?page=dashboard'); exit(); }
    $request_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    $user_dept = $_SESSION['dept_id'];
    $role = $_SESSION['role'];
    
    $updated = isset($_GET['updated']) ? true : false;
    $error = isset($_GET['error']) ? true : false;
    
    $access_check = $conn->query("
        SELECT r.* FROM requests r
        WHERE r.request_id = $request_id
        AND (
            r.requested_by = $user_id 
            OR r.from_dept = $user_dept 
            OR '$role' = 'admin'
            OR EXISTS (SELECT 1 FROM request_recipients rr WHERE rr.request_id = r.request_id AND rr.dept_id = $user_dept)
        )
    ");
    
    if ($access_check->num_rows == 0) {
        echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> You don't have permission to view this request.</div>";
    } else {
        $req = $conn->query("
            SELECT r.*, d.dept_name as from_name, u.full_name as requester
            FROM requests r
            JOIN departments d ON r.from_dept = d.dept_id
            JOIN users u ON r.requested_by = u.user_id
            WHERE r.request_id = $request_id
        ")->fetch_assoc();
        
        if (!$req) {
            echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Request not found</div>";
        } else {
            $recipients = getRequestRecipients($conn, $request_id);
            $attachments = getRequestAttachments($conn, $request_id);
            
            $is_sender = ($req['requested_by'] == $user_id || $req['from_dept'] == $user_dept);
            $is_recipient = false;
            $all_recipients = $conn->query("
                SELECT rr.*, d.dept_name, d.dept_code
                FROM request_recipients rr
                JOIN departments d ON rr.dept_id = d.dept_id
                WHERE rr.request_id = $request_id
                ORDER BY rr.recipient_id
            ");
            
            // Get all recipient statuses
            $recipient_statuses = [];
            if ($all_recipients) {
                $all_recipients->data_seek(0);
                while($rr = $all_recipients->fetch_assoc()) {
                    $recipient_statuses[] = $rr['status'];
                    if ($rr['dept_id'] == $user_dept) {
                        $is_recipient = true;
                    }
                }
                $all_recipients->data_seek(0);
            }
            $is_admin = ($role == 'admin');
            $can_update_status = ($is_recipient || $is_admin);
            
            // Count statuses
            $status_counts = [
                'pending' => 0,
                'seen' => 0,
                'approved' => 0,
                'rejected' => 0,
                'in_progress' => 0,
                'completed' => 0
            ];
            
            foreach ($recipient_statuses as $s) {
                if (isset($status_counts[$s])) {
                    $status_counts[$s]++;
                }
            }
            
            $total_recipients = count($recipient_statuses);
            
            // ===== FIXED: Determine display status for Sender/Admin ONLY =====
            $display_status = 'pending';
            $status_label = '';
            
            // ===== FOR SENDER OR ADMIN: Only advance when ALL recipients are in the same status =====
            if ($is_sender || $is_admin) {
                $status_label = 'Overall Progress';
                
                // Check if ALL are completed
                if ($status_counts['completed'] == $total_recipients) {
                    $display_status = 'completed';
                }
                // Check if ALL are in_progress
                elseif ($status_counts['in_progress'] == $total_recipients) {
                    $display_status = 'in_progress';
                }
                // Check if ALL are approved
                elseif ($status_counts['approved'] == $total_recipients) {
                    $display_status = 'approved';
                }
                // Check if ALL are seen
                elseif ($status_counts['seen'] == $total_recipients) {
                    $display_status = 'seen';
                }
                // Check if ALL are rejected
                elseif ($status_counts['rejected'] == $total_recipients) {
                    $display_status = 'rejected';
                }
                // Mixed statuses - stay at the highest level where ALL are synchronized
                else {
                    // Check if ALL have at least seen (no pending)
                    if ($status_counts['pending'] == 0) {
                        // All have seen or higher
                        if ($status_counts['seen'] > 0 && $status_counts['approved'] == 0 && $status_counts['in_progress'] == 0 && $status_counts['completed'] == 0) {
                            // All are seen only
                            $display_status = 'seen';
                        } elseif ($status_counts['approved'] > 0 && $status_counts['in_progress'] == 0 && $status_counts['completed'] == 0) {
                            // Some approved, some seen - still at seen level (not all approved)
                            $display_status = 'seen';
                        } elseif ($status_counts['in_progress'] > 0 && $status_counts['completed'] == 0) {
                            // Some in_progress, some approved/seen - still at approved level
                            $display_status = 'approved';
                        } elseif ($status_counts['completed'] > 0 && $status_counts['completed'] < $total_recipients) {
                            // Some completed, some not - still at in_progress level
                            $display_status = 'in_progress';
                        } else {
                            $display_status = 'seen';
                        }
                    } else {
                        // At least one is pending - stay at created
                        $display_status = 'pending';
                    }
                }
            } 
            // ===== FOR RECIPIENT: Show their own department status (UNCHANGED) =====
            else {
                $status_label = 'Your Department Progress';
                $my_recipient = $conn->query("
                    SELECT status FROM request_recipients 
                    WHERE request_id = $request_id AND dept_id = $user_dept
                ")->fetch_assoc();
                
                if ($my_recipient) {
                    $display_status = $my_recipient['status'];
                } else {
                    $display_status = 'pending';
                }
            }
            
            // Get tracking for timeline
            if ($is_sender || $is_admin) {
                $tracking = $conn->query("
                    SELECT rt.*, d.dept_name, u.full_name as action_by_name
                    FROM request_tracking rt
                    JOIN departments d ON rt.dept_id = d.dept_id
                    JOIN users u ON rt.action_by = u.user_id
                    WHERE rt.request_id = $request_id
                    ORDER BY rt.created_at ASC
                ");
            } else {
                $tracking = $conn->query("
                    SELECT rt.*, d.dept_name, u.full_name as action_by_name
                    FROM request_tracking rt
                    JOIN departments d ON rt.dept_id = d.dept_id
                    JOIN users u ON rt.action_by = u.user_id
                    WHERE rt.request_id = $request_id AND rt.dept_id = $user_dept
                    ORDER BY rt.created_at ASC
                ");
            }
            
            // Get values for display
            $seen_count = $status_counts['seen'] ?? 0;
            $approved_count = $status_counts['approved'] ?? 0;
            $in_progress_count = $status_counts['in_progress'] ?? 0;
            $completed_count = $status_counts['completed'] ?? 0;
            $pending_count = $status_counts['pending'] ?? 0;
?>
<h2><i class="fas fa-file-alt" style="color:var(--accent);"></i> Request Details & Tracking</h2>
<p>
    <a href="?page=view_requests" style="color:var(--accent);text-decoration:none;">
        <i class="fas fa-arrow-left"></i> Back to Requests
    </a>
    <?php if ($is_sender): ?>
        <span style="color:var(--accent);margin-left:12px;">
            <i class="fas fa-paper-plane"></i> You sent this request
        </span>
    <?php endif; ?>
    <?php if ($is_recipient): ?>
        <span style="color:#2E9E5B;margin-left:12px;">
            <i class="fas fa-inbox"></i> You are a recipient
        </span>
    <?php endif; ?>
    <?php if ($is_admin): ?>
        <span style="color:#C98A1E;margin-left:12px;">
            <i class="fas fa-user-shield"></i> Admin View
        </span>
    <?php endif; ?>
</p>

<?php if ($error): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> You don't have permission to perform this action.</div>
<?php endif; ?>

<?php if ($updated): ?>
    <div class="alert alert-success" id="updateAlert"><i class="fas fa-check-circle"></i> Status updated successfully! <span id="closeAlert" style="cursor:pointer;float:right;">&times;</span></div>
    <script>
        setTimeout(function() {
            var alert = document.getElementById('updateAlert');
            if (alert) alert.style.display = 'none';
        }, 3000);
        document.getElementById('closeAlert')?.addEventListener('click', function() {
            this.parentElement.style.display = 'none';
        });
    </script>
<?php endif; ?>

<div class="details-container">
    <div class="card-header" style="margin-bottom:16px;">
        <h3><?php echo htmlspecialchars($req['request_number']); ?></h3>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <?php if ($is_sender || $is_admin): ?>
                <span class="overall-status <?php echo $display_status; ?>">
                    <i class="fas fa-flag"></i> Overall: <?php echo ucfirst($display_status); ?>
                </span>
            <?php endif; ?>
            <?php if ($is_recipient): ?>
                <?php 
                $my_recipient_status = $conn->query("SELECT status FROM request_recipients WHERE request_id = $request_id AND dept_id = $user_dept")->fetch_assoc();
                $my_status = $my_recipient_status ? $my_recipient_status['status'] : 'pending';
                ?>
                <span class="overall-status <?php echo $my_status; ?>">
                    <i class="fas fa-user"></i> Your Status: <?php echo ucfirst($my_status); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="detail-grid">
        <div class="detail-box">
            <div class="detail-label">Request Type</div>
            <div class="detail-value"><span class="request-type-tag"><?php echo htmlspecialchars($req['request_type']); ?></span></div>
        </div>
        <div class="detail-box">
            <div class="detail-label">Priority Level</div>
            <div class="detail-value"><?php echo getPriorityBadge($req['priority']); ?></div>
        </div>
        <div class="detail-box">
            <div class="detail-label">From Department</div>
            <div class="detail-value"><i class="fas fa-building" style="color:var(--accent);"></i> <?php echo htmlspecialchars($req['from_name']); ?></div>
        </div>
        <div class="detail-box">
            <div class="detail-label">Requested By</div>
            <div class="detail-value">
                <i class="fas fa-user" style="color:var(--accent);"></i> 
                <?php echo htmlspecialchars($req['requester']); ?>
                <?php if ($req['requested_by'] == $user_id): ?>
                    <span style="color:var(--accent);font-size:0.7rem;">(You)</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="detail-box">
            <div class="detail-label">Created At</div>
            <div class="detail-value"><i class="fas fa-calendar" style="color:var(--accent);"></i> <?php echo date('F d, Y h:i A', strtotime($req['created_at'])); ?></div>
        </div>
        <div class="detail-box">
            <div class="detail-label">Deadline</div>
            <div class="detail-value">
                <i class="fas fa-calendar-times" style="color:var(--accent);"></i> 
                <?php echo $req['deadline'] ? date('F d, Y h:i A', strtotime($req['deadline'])) : 'Not set'; ?>
            </div>
        </div>
        <div class="detail-box" style="grid-column: 1 / -1;">
            <div class="detail-label">Description</div>
            <div class="detail-value" style="white-space:pre-wrap;line-height:1.6;"><?php echo nl2br(htmlspecialchars($req['description'])); ?></div>
        </div>
    </div>
</div>

<!-- ===== ATTACHMENTS ===== -->
<div class="card" id="attachments">
    <div class="card-header">
        <h3><i class="fas fa-paperclip" style="color:var(--accent);"></i> Attachments</h3>
        <span style="font-size:0.8rem;color:var(--text-secondary);">
            <?php 
            $att_count = $attachments ? $attachments->num_rows : 0;
            echo $att_count . ' file(s)';
            ?>
        </span>
    </div>
    
    <?php if ($attachments && $attachments->num_rows > 0): ?>
    <div class="attachments-grid">
        <?php while($att = $attachments->fetch_assoc()): 
            $file_icon = getFileIcon($att['file_type']);
            $file_size = $att['file_size'] ? round($att['file_size'] / 1024, 1) . ' KB' : 'Unknown';
        ?>
        <div class="attachment-item">
            <div class="file-icon"><i class="fas <?php echo $file_icon; ?>"></i></div>
            <div class="file-name"><?php echo htmlspecialchars($att['file_name']); ?></div>
            <div class="file-size"><?php echo $file_size; ?></div>
            <div class="file-actions">
                <a href="<?php echo $att['file_path']; ?>" target="_blank" class="btn btn-secondary btn-xs">
                    <i class="fas fa-eye"></i> View
                </a>
                <a href="<?php echo $att['file_path']; ?>" download class="btn btn-primary btn-xs">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="text-center" style="padding:20px;color:var(--text-secondary);">
        <i class="fas fa-paperclip" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
        <p>No attachments uploaded for this request.</p>
    </div>
    <?php endif; ?>
</div>

<!-- ===== TRACKING TIMELINE - FIXED FOR SENDER/ADMIN ONLY ===== -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-location-dot" style="color:var(--accent);"></i> Request Tracking Timeline</h3>
        <span style="font-size:0.8rem;color:var(--text-secondary);">
            <i class="fas fa-info-circle"></i> <?php echo $status_label; ?>
        </span>
    </div>
    
    <?php if ($is_sender || $is_admin): ?>
    <div style="width:100%;text-align:center;margin-bottom:12px;font-size:0.7rem;color:var(--text-secondary);">
        <i class="fas fa-users"></i> Progress based on all <?php echo $total_recipients; ?> department(s)
        <span style="display:inline-block;margin-left:12px;background:var(--input-bg);padding:2px 12px;border-radius:20px;">
            <?php echo $pending_count; ?> Pending | <?php echo $seen_count; ?> Seen | <?php echo $approved_count; ?> Approved | <?php echo $in_progress_count; ?> In Progress | <?php echo $completed_count; ?> Completed
        </span>
    </div>
    <?php endif; ?>
    
    <!-- FIXED: Status bar - stays at the highest level where ALL recipients are synchronized -->
    <div class="tracking-status-bar">
        <?php 
        $statuses = [
            ['icon' => 'fa-pen', 'label' => 'Created'],
            ['icon' => 'fa-eye', 'label' => 'Seen'],
            ['icon' => 'fa-check', 'label' => 'Approved'],
            ['icon' => 'fa-spinner', 'label' => 'In Progress'],
            ['icon' => 'fa-flag-checkered', 'label' => 'Completed']
        ];
        $status_map = ['pending' => 0, 'seen' => 1, 'approved' => 2, 'in_progress' => 3, 'completed' => 4];
        
        $current_index = isset($status_map[$display_status]) ? $status_map[$display_status] : 0;
        
        foreach ($statuses as $index => $s):
            $is_completed = $index <= $current_index;
            $is_active = $index == $current_index;
            $class = $is_completed ? 'completed' : ($is_active ? 'active' : '');
        ?>
        <div class="tracking-status-item <?php echo $class; ?>">
            <div class="status-icon">
                <i class="fas <?php echo $s['icon']; ?>"></i>
            </div>
            <span class="status-label"><?php echo $s['label']; ?></span>
            <?php if ($is_completed): ?>
                <span style="font-size:0.5rem;color:var(--accent);">✓</span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="tracking-container">
        <?php 
        if ($tracking && $tracking->num_rows > 0):
            $step_count = 0;
            while($t = $tracking->fetch_assoc()):
                $step_count++;
                $is_completed = true;
                $is_active = ($step_count == $tracking->num_rows);
                
                $icon = 'fa-circle';
                if (strpos($t['action'], 'Request Created') !== false) $icon = 'fa-pen';
                elseif (strpos($t['action'], 'Request Sent') !== false) $icon = 'fa-paper-plane';
                elseif (strpos($t['action'], 'Seen') !== false) $icon = 'fa-eye';
                elseif (strpos($t['action'], 'Approved') !== false) $icon = 'fa-check';
                elseif (strpos($t['action'], 'Rejected') !== false) $icon = 'fa-times';
                elseif (strpos($t['action'], 'Completed') !== false) $icon = 'fa-flag-checkered';
                elseif (strpos($t['action'], 'Progress') !== false) $icon = 'fa-spinner';
                elseif (strpos($t['action'], 'Status') !== false) $icon = 'fa-edit';
                else $icon = 'fa-circle';
        ?>
        <div class="tracking-step <?php echo $is_completed ? 'completed' : ($is_active ? 'active' : 'pending'); ?>">
            <div class="step-dot">
                <i class="fas <?php echo $icon; ?>"></i>
            </div>
            <div class="step-content">
                <div class="step-title">
                    <span class="step-icon"><i class="fas <?php echo $icon; ?>" style="color:var(--accent);"></i></span>
                    <?php echo htmlspecialchars($t['action']); ?>
                    <span class="step-dept">
                        <i class="fas fa-building"></i> <?php echo htmlspecialchars($t['dept_name']); ?>
                    </span>
                </div>
                <div class="step-time">
                    <i class="fas fa-clock"></i> <?php echo date('F d, Y h:i A', strtotime($t['created_at'])); ?>
                    <span style="margin-left:12px;">
                        <i class="fas fa-user"></i> by <?php echo htmlspecialchars($t['action_by_name']); ?>
                    </span>
                </div>
                <?php if ($t['notes']): ?>
                <div class="step-notes">
                    <i class="fas fa-comment"></i> <?php echo nl2br(htmlspecialchars($t['notes'])); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <div class="text-center" style="padding:30px;color:var(--text-secondary);">
            <i class="fas fa-clock" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
            <p>No tracking history available yet.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ===== DEPARTMENT RECIPIENTS WITH DROPDOWN (UNCHANGED) ===== -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users" style="color:var(--accent);"></i> Department Recipients</h3>
        <span style="font-size:0.8rem;color:var(--text-secondary);">
            <i class="fas fa-info-circle"></i> Each department has its own workflow
        </span>
    </div>
    
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Seen At</th>
                    <th>Responded At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recipients && $recipients->num_rows > 0): 
                    while($rr = $recipients->fetch_assoc()): 
                        $is_my_dept = ($rr['dept_id'] == $_SESSION['dept_id']);
                        
                        $status_options = [];
                        switch($rr['status']) {
                            case 'pending':
                                $status_options = ['seen', 'approved', 'rejected'];
                                break;
                            case 'seen':
                                $status_options = ['approved', 'rejected', 'in_progress'];
                                break;
                            case 'approved':
                                $status_options = ['in_progress', 'completed'];
                                break;
                            case 'in_progress':
                                $status_options = ['completed'];
                                break;
                            case 'completed':
                                $status_options = [];
                                break;
                            default:
                                $status_options = ['seen', 'approved', 'rejected', 'in_progress', 'completed'];
                        }
                ?>
                <tr class="<?php echo $updated && $is_my_dept ? 'update-flash' : ''; ?>">
                    <td>
                        <i class="fas fa-building" style="color:var(--accent);"></i>
                        <?php echo htmlspecialchars($rr['dept_name']); ?>
                        <?php if ($is_my_dept): ?>
                            <span style="font-size:0.6rem;color:var(--text-secondary);">(You)</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo getRecipientStatusBadge($rr['status']); ?>
                    </td>
                    <td>
                        <?php if ($rr['seen_at']): ?>
                            <span style="color:#2E9E5B;"><i class="fas fa-check-circle"></i> <?php echo date('M d, Y h:i A', strtotime($rr['seen_at'])); ?></span>
                        <?php else: ?>
                            <span style="color:var(--text-secondary);"><i class="fas fa-clock"></i> Not seen yet</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($rr['responded_at']): ?>
                            <span style="color:#2E9E5B;"><i class="fas fa-check-circle"></i> <?php echo date('M d, Y h:i A', strtotime($rr['responded_at'])); ?></span>
                        <?php else: ?>
                            <span style="color:var(--text-secondary);"><i class="fas fa-clock"></i> Not responded</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($is_my_dept || $is_admin): ?>
                            <?php if ($rr['status'] == 'pending'): ?>
                                <form method="POST" class="dept-update-form">
                                    <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
                                    <input type="hidden" name="recipient_id" value="<?php echo $rr['recipient_id']; ?>">
                                    <button type="submit" name="mark_seen" class="dept-action-btn btn-seen">
                                        <i class="fas fa-eye"></i> Seen
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if (!empty($status_options)): ?>
                                <form method="POST" class="dept-update-form">
                                    <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
                                    <input type="hidden" name="recipient_id" value="<?php echo $rr['recipient_id']; ?>">
                                    <select name="status" class="dept-action-select" required>
                                        <option value="">Update Status</option>
                                        <?php foreach($status_options as $opt): 
                                            $opt_labels = [
                                                'seen' => '👀 Seen',
                                                'approved' => '✅ Approved',
                                                'rejected' => '❌ Rejected',
                                                'in_progress' => '🔄 In Progress',
                                                'completed' => '🎉 Completed'
                                            ];
                                        ?>
                                        <option value="<?php echo $opt; ?>"><?php echo $opt_labels[$opt] ?? ucfirst($opt); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="update_recipient_status" class="btn btn-primary btn-xs">
                                        <i class="fas fa-save"></i> Update
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if ($rr['status'] == 'completed'): ?>
                                <span class="status-done"><i class="fas fa-check-circle"></i> ✓ DONE</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if ($rr['status'] == 'completed'): ?>
                                <span class="status-done"><i class="fas fa-check-circle"></i> ✓ DONE</span>
                            <?php else: ?>
                                <span style="font-size:0.7rem;color:var(--text-secondary);">👀 View only</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="5" class="text-center" style="color:var(--text-secondary);">No recipients found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php } } ?>

