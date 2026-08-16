<?php
/**
 * Page: notifications
 * Included by index.php when ?page=notifications
 */
    $user_id = $_SESSION['user_id'];
    
    $notifs = $conn->query("
        SELECT n.*, r.request_number, r.title, r.request_id, r.priority
        FROM notifications n 
        LEFT JOIN requests r ON n.request_id = r.request_id 
        WHERE n.user_id = $user_id 
        ORDER BY n.created_at DESC
    ");
    $unread_count = getUnreadCount($conn, $user_id);
?>
<h2><i class="fas fa-bell" style="color:var(--accent);"></i> Notifications</h2>
<p>
    <span style="background:var(--accent);color:white;padding:2px 12px;border-radius:20px;font-size:0.7rem;font-weight:700;">
        <i class="fas fa-circle" style="font-size:0.5rem;"></i> <?php echo $unread_count; ?> unread
    </span>
    <span style="margin-left:12px;font-size:0.8rem;color:var(--text-secondary);">
        <i class="fas fa-info-circle"></i> Click "View Request" to mark as read
    </span>
</p>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> All Notifications</h3>
        <?php if ($unread_count > 0): ?>
        <a href="?mark_all_read=1" class="btn btn-success btn-sm" onclick="return confirm('Mark all notifications as read?')">
            <i class="fas fa-check-double"></i> Mark All as Read
        </a>
        <?php endif; ?>
    </div>
    
    <?php if ($notifs && $notifs->num_rows > 0): ?>
        <?php while($n = $notifs->fetch_assoc()): 
            $is_unread = (!$n['is_read'] || $n['is_read'] === null || $n['is_read'] == 0);
            $is_self = ($n['sender_id'] == $user_id);
            
            if ($is_self) {
                $notif_class = 'notification-self';
                $badge_text = 'SENT';
                $badge_color = '#C98A1E';
            } elseif ($is_unread) {
                $notif_class = 'notification-unread';
                $badge_text = 'UNREAD';
                $badge_color = 'var(--accent)';
            } else {
                $notif_class = 'notification-read';
                $badge_text = 'READ';
                $badge_color = '#2E9E5B';
            }
            
            $clean_message = strip_tags($n['message']);
            $title = substr($clean_message, 0, 60);
            if (strlen($clean_message) > 60) $title .= '...';
        ?>
        <div class="notification-item <?php echo $notif_class; ?>">
            <div>
                <div class="notification-title">
                    <?php if ($is_self): ?>
                        <span style="color:#C98A1E;font-size:0.7rem;">[You]</span>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($title); ?>
                    <span class="notification-badge" style="background:<?php echo $badge_color; ?>;">
                        <?php echo $badge_text; ?>
                    </span>
                </div>
                <div class="notification-message">
                    <?php echo htmlspecialchars($clean_message); ?>
                </div>
                <div class="notification-time">
                    <i class="fas fa-clock"></i> <?php echo date('F d, Y h:i A', strtotime($n['created_at'])); ?>
                    <?php if ($n['request_id']): ?>
                        <span style="margin-left:12px;">
                            <i class="fas fa-tag"></i> Request #<?php echo htmlspecialchars($n['request_number']); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <?php if ($n['request_id']): ?>
                    <?php if ($is_unread && !$is_self): ?>
                        <a href="?mark_read=<?php echo $n['notif_id']; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> View Request
                        </a>
                    <?php else: ?>
                        <a href="?page=request_details&id=<?php echo $n['request_id']; ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-eye"></i> View Request
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-bell-slash"></i>
        <h4>No Notifications</h4>
        <p>You're all caught up!</p>
    </div>
    <?php endif; ?>
</div>

