<?php
/**
 * Shared helper functions used across the app.
 */

// ==================== FUNCTIONS ====================
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getSetting($conn, $key) {
    $result = $conn->query("SELECT setting_value FROM system_settings WHERE setting_key = '$key'");
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc()['setting_value'];
    }
    return null;
}

function updateSetting($conn, $key, $value) {
    $value = $conn->real_escape_string($value);
    $conn->query("UPDATE system_settings SET setting_value = '$value', updated_at = NOW() WHERE setting_key = '$key'");
    if ($conn->affected_rows == 0) {
        $conn->query("INSERT INTO system_settings (setting_key, setting_value) VALUES ('$key', '$value')");
    }
}

function getDeptName($conn, $dept_id) {
    if (!$dept_id) return 'Unknown';
    $result = $conn->query("SELECT dept_name FROM departments WHERE dept_id = $dept_id");
    return $result->num_rows > 0 ? $result->fetch_assoc()['dept_name'] : 'Unknown';
}

function getDeptCode($conn, $dept_id) {
    if (!$dept_id) return '';
    $result = $conn->query("SELECT dept_code FROM departments WHERE dept_id = $dept_id");
    return $result->num_rows > 0 ? $result->fetch_assoc()['dept_code'] : '';
}

function generateRequestNumber($conn) {
    $year = date('Y');
    $month = date('m');
    $result = $conn->query("SELECT request_number FROM requests WHERE request_number LIKE 'REQ-$year$month-%' ORDER BY request_number DESC LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $last = $result->fetch_assoc()['request_number'];
        $last_num = intval(substr($last, -4));
        $new_num = $last_num + 1;
    } else {
        $new_num = 1;
    }
    return "REQ-" . $year . $month . "-" . str_pad($new_num, 4, '0', STR_PAD_LEFT);
}

function addNotification($conn, $user_id, $message, $request_id = null, $is_system = false, $sender_id = null) {
    $message = strip_tags($message);
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, request_id, message, is_system, sender_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisii", $user_id, $request_id, $message, $is_system, $sender_id);
    return $stmt->execute();
}

function addTimeline($conn, $request_id, $action, $action_by, $notes = '') {
    $stmt = $conn->prepare("INSERT INTO request_timeline (request_id, action, action_by, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $request_id, $action, $action_by, $notes);
    return $stmt->execute();
}

function addRequestDetail($conn, $request_id, $action, $action_by, $notes = '') {
    $stmt = $conn->prepare("INSERT INTO request_details (request_id, action, action_by, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $request_id, $action, $action_by, $notes);
    return $stmt->execute();
}

function addRequestTracking($conn, $request_id, $dept_id, $action, $action_by, $notes = '') {
    $stmt = $conn->prepare("INSERT INTO request_tracking (request_id, dept_id, action, action_by, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisis", $request_id, $dept_id, $action, $action_by, $notes);
    return $stmt->execute();
}

function getRequestRecipients($conn, $request_id) {
    return $conn->query("
        SELECT rr.*, d.dept_name, d.dept_code
        FROM request_recipients rr
        JOIN departments d ON rr.dept_id = d.dept_id
        WHERE rr.request_id = $request_id
        ORDER BY rr.recipient_id
    ");
}

function getRequestTracking($conn, $request_id) {
    return $conn->query("
        SELECT rt.*, d.dept_name, u.full_name as action_by_name
        FROM request_tracking rt
        JOIN departments d ON rt.dept_id = d.dept_id
        JOIN users u ON rt.action_by = u.user_id
        WHERE rt.request_id = $request_id
        ORDER BY rt.created_at ASC
    ");
}

function getRequestStatusSummary($conn, $dept_id = null, $user_id = null) {
    $where = "";
    if ($dept_id) {
        $where = "WHERE from_dept = $dept_id";
    }
    
    $total = 0;
    $pending = 0;
    $approved = 0;
    $rejected = 0;
    $in_progress = 0;
    $completed = 0;
    
    $result = $conn->query("SELECT COUNT(*) as c FROM requests $where");
    if ($result) $total = $result->fetch_assoc()['c'];
    
    $pending_where = $dept_id ? "WHERE from_dept = $dept_id AND status = 'pending'" : "WHERE status = 'pending'";
    $result = $conn->query("SELECT COUNT(*) as c FROM requests $pending_where");
    if ($result) $pending = $result->fetch_assoc()['c'];
    
    $approved_where = $dept_id ? "WHERE from_dept = $dept_id AND status = 'approved'" : "WHERE status = 'approved'";
    $result = $conn->query("SELECT COUNT(*) as c FROM requests $approved_where");
    if ($result) $approved = $result->fetch_assoc()['c'];
    
    $rejected_where = $dept_id ? "WHERE from_dept = $dept_id AND status = 'rejected'" : "WHERE status = 'rejected'";
    $result = $conn->query("SELECT COUNT(*) as c FROM requests $rejected_where");
    if ($result) $rejected = $result->fetch_assoc()['c'];
    
    $in_progress_where = $dept_id ? "WHERE from_dept = $dept_id AND status = 'in_progress'" : "WHERE status = 'in_progress'";
    $result = $conn->query("SELECT COUNT(*) as c FROM requests $in_progress_where");
    if ($result) $in_progress = $result->fetch_assoc()['c'];
    
    $completed_where = $dept_id ? "WHERE from_dept = $dept_id AND status = 'completed'" : "WHERE status = 'completed'";
    $result = $conn->query("SELECT COUNT(*) as c FROM requests $completed_where");
    if ($result) $completed = $result->fetch_assoc()['c'];
    
    return [
        'total' => $total,
        'pending' => $pending,
        'approved' => $approved,
        'rejected' => $rejected,
        'in_progress' => $in_progress,
        'completed' => $completed
    ];
}

function getStatusBadge($status) {
    $classes = [
        'pending' => 'status-pending',
        'seen' => 'status-seen',
        'approved' => 'status-approved',
        'rejected' => 'status-rejected',
        'in_progress' => 'status-in_progress',
        'completed' => 'status-completed'
    ];
    $class = isset($classes[$status]) ? $classes[$status] : 'status-pending';
    $icons = [
        'pending' => 'fa-clock',
        'seen' => 'fa-eye',
        'approved' => 'fa-check',
        'rejected' => 'fa-xmark',
        'in_progress' => 'fa-rotate',
        'completed' => 'fa-flag-checkered'
    ];
    $icon = isset($icons[$status]) ? $icons[$status] : 'fa-circle';
    $labels = [
        'pending' => 'Pending',
        'seen' => 'Seen',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'in_progress' => 'In Progress',
        'completed' => 'Completed'
    ];
    $label = isset($labels[$status]) ? $labels[$status] : ucfirst($status);
    return "<span class='status-badge $class'><i class='fas $icon'></i> $label</span>";
}

function getRecipientStatusBadge($status) {
    return getStatusBadge($status);
}

function getPriorityBadge($priority) {
    $classes = [
        'low' => 'priority-low',
        'medium' => 'priority-medium',
        'high' => 'priority-high'
    ];
    $class = isset($classes[$priority]) ? $classes[$priority] : 'priority-medium';
    $labels = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High'
    ];
    $label = isset($labels[$priority]) ? $labels[$priority] : ucfirst($priority);
    return "<span class='priority-badge $class'>$label</span>";
}

function getDeptIcon($dept_code, $dept_name) {
    $icons = [
        'MAYOR' => 'fa-landmark', 'VICE' => 'fa-user-tie', 'SB' => 'fa-gavel',
        'HRMO' => 'fa-users', 'GSO' => 'fa-boxes', 'ENGINEERING' => 'fa-hard-hat',
        'HEALTH' => 'fa-heartbeat', 'SWDO' => 'fa-hand-holding-heart',
        'TREASURY' => 'fa-coins', 'BUDGET' => 'fa-calculator',
        'ASSESSOR' => 'fa-file-invoice', 'AGRICULTURE' => 'fa-seedling',
        'TOURISM' => 'fa-umbrella-beach', 'DRRM' => 'fa-shield-alt',
        'PEACE' => 'fa-balance-scale', 'YOUTH' => 'fa-child',
        'WOMEN' => 'fa-venus-mars', 'BARANGAY' => 'fa-home',
        'PLANNING' => 'fa-drafting-compass', 'ACCOUNTING' => 'fa-calculator'
    ];
    
    if (isset($icons[$dept_code])) return $icons[$dept_code];
    
    $name_lower = strtolower($dept_name);
    if (strpos($name_lower, 'health') !== false) return 'fa-heartbeat';
    if (strpos($name_lower, 'social') !== false) return 'fa-hand-holding-heart';
    if (strpos($name_lower, 'engineer') !== false) return 'fa-hard-hat';
    if (strpos($name_lower, 'treasury') !== false) return 'fa-coins';
    if (strpos($name_lower, 'budget') !== false) return 'fa-calculator';
    if (strpos($name_lower, 'assessor') !== false) return 'fa-file-invoice';
    if (strpos($name_lower, 'agriculture') !== false) return 'fa-seedling';
    if (strpos($name_lower, 'tourism') !== false) return 'fa-umbrella-beach';
    
    return 'fa-building';
}

function getUserDeptIcon($conn, $user_id) {
    $result = $conn->query("SELECT d.dept_code, d.dept_name FROM users u LEFT JOIN departments d ON u.dept_id = d.dept_id WHERE u.user_id = $user_id");
    if ($result && $result->num_rows > 0) {
        $dept = $result->fetch_assoc();
        return getDeptIcon($dept['dept_code'], $dept['dept_name']);
    }
    return 'fa-building';
}

function getUserTheme($conn, $user_id) {
    $result = $conn->query("SELECT theme_preference FROM users WHERE user_id = $user_id");
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc()['theme_preference'];
    }
    return 'dark';
}

function updateUserTheme($conn, $user_id, $theme) {
    $conn->query("UPDATE users SET theme_preference = '$theme' WHERE user_id = $user_id");
}

function handleFileUpload($file, $request_id, $user_id = null) {
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'req_' . $request_id . '_' . time() . '_' . uniqid() . '.' . $ext;
        $filepath = $upload_dir . $filename;
        $file_size = $file['size'];
        $file_type = $file['type'];
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'path' => $filepath, 
                'name' => $file['name'],
                'size' => $file_size,
                'type' => $file_type
            ];
        }
    }
    return null;
}

function getRequestAttachments($conn, $request_id) {
    return $conn->query("
        SELECT * FROM request_attachments 
        WHERE request_id = $request_id 
        ORDER BY uploaded_at DESC
    ");
}

function getFileIcon($file_type) {
    if (strpos($file_type, 'image/') !== false) return 'fa-image';
    if (strpos($file_type, 'pdf') !== false) return 'fa-file-pdf';
    if (strpos($file_type, 'word') !== false || strpos($file_type, 'doc') !== false) return 'fa-file-word';
    if (strpos($file_type, 'excel') !== false || strpos($file_type, 'sheet') !== false) return 'fa-file-excel';
    if (strpos($file_type, 'text') !== false) return 'fa-file-alt';
    return 'fa-file';
}

function getSystemTheme($conn) {
    $result = $conn->query("SELECT setting_value FROM system_settings WHERE setting_key = 'system_theme'");
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc()['setting_value'];
    }
    return 'dark';
}

// ==================== GET UNREAD NOTIFICATIONS COUNT ====================
function getUnreadCount($conn, $user_id) {
    $result = $conn->query("
        SELECT COUNT(*) as c FROM notifications 
        WHERE user_id = $user_id 
        AND is_read = 0 
        AND (sender_id IS NULL OR sender_id != $user_id)
    ");
    return $result ? $result->fetch_assoc()['c'] : 0;
}

// ==================== GET OVERALL REQUEST STATUS ====================
function getOverallStatus($conn, $request_id) {
    $check = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'seen' THEN 1 ELSE 0 END) as seen_count,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
            SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_count,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count
        FROM request_recipients 
        WHERE request_id = $request_id
    ");
    $stats = $check->fetch_assoc();
    $total = $stats['total'];
    
    if ($total == 0) return 'pending';
    
    // Check if all completed
    if ($stats['completed_count'] == $total) return 'completed';
    
    // Check if all rejected
    if ($stats['rejected_count'] == $total) return 'rejected';
    
    // Check if all in_progress
    if ($stats['in_progress_count'] == $total) return 'in_progress';
    
    // Check if all approved
    if ($stats['approved_count'] == $total) return 'approved';
    
    // Check if all seen
    $seen_total = $stats['seen_count'] + $stats['approved_count'] + $stats['rejected_count'] + $stats['in_progress_count'] + $stats['completed_count'];
    if ($seen_total == $total) return 'seen';
    
    // Mixed statuses - determine highest progress level
    if ($stats['completed_count'] > 0 && $stats['completed_count'] < $total) return 'in_progress';
    if ($stats['in_progress_count'] > 0) return 'in_progress';
    if ($stats['approved_count'] > 0 && $stats['approved_count'] < $total) return 'approved';
    if ($stats['seen_count'] > 0 && $seen_total < $total) return 'seen';
    
    // Check if any have responded
    $responded = $stats['seen_count'] + $stats['approved_count'] + $stats['rejected_count'] + $stats['in_progress_count'] + $stats['completed_count'];
    if ($responded > 0 && $responded < $total) {
        if ($stats['approved_count'] > 0 || $stats['in_progress_count'] > 0 || $stats['completed_count'] > 0) {
            return 'in_progress';
        }
        return 'seen';
    }
    
    return 'pending';
}

// ==================== UPDATE REQUEST OVERALL STATUS ====================
function updateRequestOverallStatus($conn, $request_id) {
    $overall_status = getOverallStatus($conn, $request_id);
    $conn->query("UPDATE requests SET status = '$overall_status' WHERE request_id = $request_id");
    return $overall_status;
}

// ==================== GET RECIPIENT TRACKING FOR TIMELINE ====================
function getRecipientTracking($conn, $request_id, $dept_id) {
    return $conn->query("
        SELECT rt.*, d.dept_name, u.full_name as action_by_name
        FROM request_tracking rt
        JOIN departments d ON rt.dept_id = d.dept_id
        JOIN users u ON rt.action_by = u.user_id
        WHERE rt.request_id = $request_id AND rt.dept_id = $dept_id
        ORDER BY rt.created_at ASC
    ");
}

