<?php
/**
 * Handles all POST actions / form submissions and logout.
 * Must run before any HTML output.
 */

// ============================================
// HANDLE ALL POST REQUESTS
// ============================================

// ==================== HANDLE ADD REQUEST TYPE ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_request_type']) && isLoggedIn()) {
    $new_type = $conn->real_escape_string(trim($_POST['new_request_type']));
    if (!empty($new_type)) {
        $check = $conn->query("SELECT * FROM request_types WHERE type_name = '$new_type'");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO request_types (type_name) VALUES ('$new_type')");
            $type_added = "✅ New request type '$new_type' added!";
        } else {
            $type_error = "⚠️ Request type already exists!";
        }
    }
}

// ==================== HANDLE REMOVE REQUEST TYPE ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_request_type']) && isLoggedIn()) {
    $type_name = $conn->real_escape_string($_POST['remove_type_name']);
    $default_types = ['Supply/Equipment', 'Document', 'Repair/Maintenance', 'Vehicle', 'Manpower', 
                      'Financial', 'IT/Computer', 'Permit/License', 'Training/Seminar', 'Other'];
    if (!in_array($type_name, $default_types)) {
        $conn->query("DELETE FROM request_types WHERE type_name = '$type_name'");
        $type_removed = "✅ Request type '$type_name' removed!";
    } else {
        $type_error = "⚠️ Cannot remove default request types!";
    }
}

// ==================== HANDLE CREATE REQUEST ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_request']) && isLoggedIn()) {
    $title = $conn->real_escape_string($_POST['title'] ?? '');
    $additional_notes = $conn->real_escape_string($_POST['additional_notes'] ?? '');
    $description = !empty($_POST['description']) ? $conn->real_escape_string($_POST['description']) : NULL;
    
    $request_type = isset($_POST['request_type']) ? $conn->real_escape_string($_POST['request_type']) : '';
    $custom_type = isset($_POST['custom_request_type']) ? $conn->real_escape_string(trim($_POST['custom_request_type'])) : '';
    
    if ($request_type == 'Other' && !empty($custom_type)) {
        $request_type = $custom_type;
        $check = $conn->query("SELECT * FROM request_types WHERE type_name = '$custom_type'");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO request_types (type_name) VALUES ('$custom_type')");
        }
    }
    
    $priority = $conn->real_escape_string($_POST['priority']);
    $from_dept = $_SESSION['dept_id'];
    $user_id = $_SESSION['user_id'];
    
    $deadline = !empty($_POST['deadline_date']) ? $conn->real_escape_string($_POST['deadline_date']) : null;
    if (!empty($_POST['deadline_time'])) {
        $deadline = $deadline . ' ' . $conn->real_escape_string($_POST['deadline_time']) . ':00';
    }
    
    $recipient_depts = isset($_POST['recipient_depts']) ? $_POST['recipient_depts'] : [];
    
    if (empty($request_type)) {
        $error = "Request Type is required!";
    } elseif (empty($recipient_depts)) {
        $error = "Please select at least one department to send the request to.";
    } elseif (empty($deadline)) {
        $error = "Deadline is required!";
    } elseif (!isset($_FILES['attachments']) || empty($_FILES['attachments']['name'][0])) {
        $error = "Please upload at least one attachment!";
    } else {
        $request_number = generateRequestNumber($conn);
        
        $query = "INSERT INTO requests (request_number, title, description, request_type, priority, from_dept, requested_by, status, deadline, additional_notes) 
                  VALUES ('$request_number', 'Request #$request_number', " . ($description ? "'$description'" : "NULL") . ", '$request_type', '$priority', $from_dept, $user_id, 'pending', " . ($deadline ? "'$deadline'" : "NULL") . ", '$additional_notes')";
        
        if ($conn->query($query)) {
            $request_id = $conn->insert_id;
            
            $uploaded_files = [];
            if (isset($_FILES['attachments'])) {
                $files = $_FILES['attachments'];
                $file_count = count($files['name']);
                
                for ($i = 0; $i < $file_count; $i++) {
                    if (!empty($files['name'][$i])) {
                        $file = [
                            'name' => $files['name'][$i],
                            'type' => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error' => $files['error'][$i],
                            'size' => $files['size'][$i]
                        ];
                        
                        if ($file['error'] === UPLOAD_ERR_OK) {
                            $upload_result = handleFileUpload($file, $request_id, $user_id);
                            if ($upload_result) {
                                $file_path = $upload_result['path'];
                                $file_name = $upload_result['name'];
                                $file_size = $upload_result['size'];
                                $file_type = $upload_result['type'];
                                
                                $conn->query("INSERT INTO request_attachments (request_id, file_path, file_name, file_size, file_type, uploaded_by) 
                                             VALUES ($request_id, '$file_path', '$file_name', $file_size, '$file_type', $user_id)");
                                
                                $uploaded_files[] = $upload_result;
                            }
                        }
                    }
                }
            }
            
            $priority_label = getPriorityBadge($priority);
            
            foreach ($recipient_depts as $dept_id) {
                $conn->query("INSERT INTO request_recipients (request_id, dept_id, status) VALUES ($request_id, $dept_id, 'pending')");
                addRequestTracking($conn, $request_id, $dept_id, '📤 Request Sent', $user_id, "Sent to " . getDeptName($conn, $dept_id));
                
                $dept_users = $conn->query("SELECT user_id FROM users WHERE dept_id = $dept_id AND status = 'active'");
                if ($dept_users) {
                    while($recipient = $dept_users->fetch_assoc()) {
                        $message = "📋 NEW REQUEST: $request_type | Priority: $priority_label (Request #: $request_number) - " . count($uploaded_files) . " attachment(s)";
                        addNotification($conn, $recipient['user_id'], $message, $request_id, false, $user_id);
                    }
                }
            }
            
            addRequestTracking($conn, $request_id, $from_dept, '📝 Request Created', $user_id, "Request created by " . $_SESSION['full_name'] . " with " . count($uploaded_files) . " attachment(s)");
            addRequestDetail($conn, $request_id, 'Request Created', $user_id, "Sent to " . count($recipient_depts) . " departments with " . count($uploaded_files) . " attachment(s)");
            
            $admins = $conn->query("SELECT user_id FROM users WHERE role = 'admin' AND status = 'active' AND user_id != $user_id");
            if ($admins) {
                while($admin = $admins->fetch_assoc()) {
                    $message = "📋 NEW MULTI-DEPARTMENT REQUEST: $request_type | Priority: $priority_label (Request #: $request_number) - " . count($uploaded_files) . " attachment(s)";
                    addNotification($conn, $admin['user_id'], $message, $request_id, false, $user_id);
                }
            }
            
            $sender_message = "📤 You sent a new request: $request_type (Request #: $request_number) to " . count($recipient_depts) . " department(s) with " . count($uploaded_files) . " attachment(s)";
            addNotification($conn, $user_id, $sender_message, $request_id, false, $user_id);
            
            $success = "✅ Request #$request_number sent successfully!";
            $show_popup = true;
            $popup_message = "Request #$request_number has been successfully sent to " . count($recipient_depts) . " department(s) with " . count($uploaded_files) . " attachment(s).";
            $page = 'create_request';
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}

// ==================== HANDLE UPDATE RECIPIENT STATUS ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_recipient_status']) && isLoggedIn()) {
    $request_id = intval($_POST['request_id']);
    $recipient_id = intval($_POST['recipient_id']);
    $status = $conn->real_escape_string($_POST['status']);
    $notes = $conn->real_escape_string($_POST['notes'] ?? '');
    $user_id = $_SESSION['user_id'];
    $user_dept = $_SESSION['dept_id'];
    $user_name = $_SESSION['full_name'];
    $user_role = $_SESSION['role'];
    
    $recipient_check = $conn->query("
        SELECT rr.* FROM request_recipients rr 
        WHERE rr.recipient_id = $recipient_id AND rr.dept_id = $user_dept
    ");
    
    if ($recipient_check->num_rows == 0 && $user_role != 'admin') {
        header("Location: ?page=request_details&id=" . $request_id . "&error=1");
        exit();
    } else {
        $conn->query("UPDATE request_recipients SET status = '$status', responded_at = NOW() WHERE recipient_id = $recipient_id");
        
        $req_info = $conn->query("SELECT from_dept, request_number, title, requested_by FROM requests WHERE request_id = $request_id")->fetch_assoc();
        
        $status_icons = [
            'seen' => '👀',
            'approved' => '✅',
            'rejected' => '❌',
            'completed' => '🎉',
            'in_progress' => '🔄'
        ];
        $icon = isset($status_icons[$status]) ? $status_icons[$status] : '📌';
        
        addRequestTracking($conn, $request_id, $user_dept, "$icon Status: $status", $user_id, "Updated by: $user_name - $notes");
        addRequestDetail($conn, $request_id, "Recipient Status Updated", $user_id, "Status: $status. Notes: $notes");
        
        $recipient_info = $conn->query("SELECT d.dept_name FROM request_recipients rr JOIN departments d ON rr.dept_id = d.dept_id WHERE rr.recipient_id = $recipient_id")->fetch_assoc();
        
        $message = "📢 Request #{$req_info['request_number']} - Department {$recipient_info['dept_name']} updated status to '$status'";
        addNotification($conn, $req_info['requested_by'], $message, $request_id, false, $user_id);
        
        $admins = $conn->query("SELECT user_id FROM users WHERE role = 'admin' AND status = 'active' AND user_id != $user_id AND user_id != {$req_info['requested_by']}");
        if ($admins) {
            while($admin = $admins->fetch_assoc()) {
                addNotification($conn, $admin['user_id'], $message, $request_id, false, $user_id);
            }
        }
        
        $user_message = "✅ You updated status to '$status' for Request #{$req_info['request_number']} - Department {$recipient_info['dept_name']}";
        addNotification($conn, $user_id, $user_message, $request_id, false, $user_id);
        
        $overall_status = updateRequestOverallStatus($conn, $request_id);
        
        if ($status == 'completed') {
            addTimeline($conn, $request_id, '🎉 Request Completed by Department', $user_id, "Completed by {$recipient_info['dept_name']} - $notes");
            
            $complete_message = "🎉 Request #{$req_info['request_number']} has been COMPLETED by {$recipient_info['dept_name']}";
            addNotification($conn, $req_info['requested_by'], $complete_message, $request_id, false, $user_id);
            
            if ($admins) {
                $admins->data_seek(0);
                while($admin = $admins->fetch_assoc()) {
                    addNotification($conn, $admin['user_id'], $complete_message, $request_id, false, $user_id);
                }
            }
        }
        
        addRequestTracking($conn, $request_id, $user_dept, "📊 Overall Status: $overall_status", $user_id, "System updated overall status");
        
        header("Location: ?page=request_details&id=" . $request_id . "&updated=1");
        exit();
    }
}

// ==================== HANDLE MARK AS SEEN ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_seen']) && isLoggedIn()) {
    $recipient_id = intval($_POST['recipient_id']);
    $request_id = intval($_POST['request_id']);
    $user_id = $_SESSION['user_id'];
    $user_dept = $_SESSION['dept_id'];
    $user_name = $_SESSION['full_name'];
    $user_role = $_SESSION['role'];
    
    $recipient_check = $conn->query("
        SELECT rr.* FROM request_recipients rr 
        WHERE rr.recipient_id = $recipient_id AND rr.dept_id = $user_dept
    ");
    
    if ($recipient_check->num_rows == 0 && $user_role != 'admin') {
        header("Location: ?page=request_details&id=" . $request_id . "&error=1");
        exit();
    } else {
        $conn->query("UPDATE request_recipients SET status = 'seen', seen_at = NOW() WHERE recipient_id = $recipient_id");
        
        addRequestTracking($conn, $request_id, $user_dept, '👀 Request Seen', $user_id, "Viewed by: $user_name - " . getDeptName($conn, $user_dept));
        
        $overall_status = updateRequestOverallStatus($conn, $request_id);
        
        if ($overall_status == 'seen') {
            $req_info = $conn->query("SELECT requested_by, request_number FROM requests WHERE request_id = $request_id")->fetch_assoc();
            if ($req_info && $req_info['requested_by'] != $user_id) {
                $message = "✅ All departments have seen your request #{$req_info['request_number']}";
                addNotification($conn, $req_info['requested_by'], $message, $request_id, false, $user_id);
            }
        }
        
        addRequestTracking($conn, $request_id, $user_dept, "📊 Overall Status: $overall_status", $user_id, "System updated overall status");
        
        header("Location: ?page=request_details&id=" . $request_id . "&updated=1");
        exit();
    }
}

// ==================== HANDLE DELETE REQUEST ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_request']) && isLoggedIn()) {
    $request_id = intval($_POST['request_id']);
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['role'];
    $dept_id = $_SESSION['dept_id'];
    
    $check = $conn->query("SELECT r.* FROM requests r WHERE r.request_id = $request_id");
    $request = $check->fetch_assoc();
    
    if ($request) {
        $can_delete = false;
        if ($user_role == 'admin') {
            $can_delete = true;
        } elseif ($request['requested_by'] == $user_id) {
            $can_delete = true;
        } elseif ($request['from_dept'] == $dept_id) {
            $can_delete = true;
        }
        
        if ($can_delete) {
            $conn->query("INSERT INTO deleted_requests (original_request_id, request_number, title, description, 
                          request_type, priority, from_dept, requested_by, deleted_by, status_at_deletion)
                          SELECT request_id, request_number, title, description, request_type, priority, 
                                 from_dept, requested_by, $user_id, status
                          FROM requests WHERE request_id = $request_id");
            
            addTimeline($conn, $request_id, 'Request Deleted', $user_id, "Request was deleted");
            $conn->query("DELETE FROM requests WHERE request_id = $request_id");
            
            $delete_success = "Request deleted successfully!";
            $page = 'view_requests';
        } else {
            $delete_error = "You don't have permission to delete this request!";
        }
    } else {
        $delete_error = "Request not found!";
    }
}

// ==================== HANDLE RESTORE REQUEST ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['restore_request']) && isLoggedIn()) {
    $delete_id = intval($_POST['delete_id']);
    $user_id = $_SESSION['user_id'];
    
    $deleted = $conn->query("SELECT * FROM deleted_requests WHERE delete_id = $delete_id")->fetch_assoc();
    
    if ($deleted) {
        $conn->query("INSERT INTO requests (request_number, title, description, request_type, priority, 
                      from_dept, requested_by, status, created_at) 
                      VALUES ('{$deleted['request_number']}', '{$deleted['title']}', '{$deleted['description']}',
                              '{$deleted['request_type']}', '{$deleted['priority']}', {$deleted['from_dept']},
                              {$deleted['requested_by']}, '{$deleted['status_at_deletion']}',
                              '{$deleted['deleted_at']}')");
        
        $new_request_id = $conn->insert_id;
        addTimeline($conn, $new_request_id, 'Request Restored', $user_id, "Restored from deleted items");
        $conn->query("DELETE FROM deleted_requests WHERE delete_id = $delete_id");
        
        $restore_success = "Request restored successfully!";
        $page = 'recently_deleted';
    }
}

// ==================== HANDLE PERMANENT DELETE ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['permanent_delete']) && isLoggedIn() && $_SESSION['role'] == 'admin') {
    $delete_id = intval($_POST['delete_id']);
    $conn->query("DELETE FROM deleted_requests WHERE delete_id = $delete_id");
    $permanent_success = "Request permanently deleted!";
    $page = 'recently_deleted';
}

// ==================== ADD DEPARTMENT HANDLERS ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_department']) && isLoggedIn() && $_SESSION['role'] == 'admin') {
    $dept_code = strtoupper($conn->real_escape_string($_POST['dept_code']));
    $dept_name = $conn->real_escape_string($_POST['dept_name']);
    $dept_description = $conn->real_escape_string($_POST['dept_description'] ?? '');
    
    $check = $conn->query("SELECT dept_id FROM departments WHERE dept_code = '$dept_code' OR dept_name = '$dept_name'");
    if ($check && $check->num_rows > 0) {
        $dept_error = "Department code or name already exists!";
    } else {
        $insert = $conn->query("INSERT INTO departments (dept_code, dept_name, description) VALUES ('$dept_code', '$dept_name', '$dept_description')");
        if ($insert) {
            $dept_success = "New department '{$dept_name}' added successfully!";
            $page = 'departments';
        } else {
            $dept_error = "Database error: " . $conn->error;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_department']) && isLoggedIn() && $_SESSION['role'] == 'admin') {
    $dept_id = intval($_POST['dept_id']);
    $dept_code = strtoupper($conn->real_escape_string($_POST['dept_code']));
    $dept_name = $conn->real_escape_string($_POST['dept_name']);
    $dept_description = $conn->real_escape_string($_POST['dept_description'] ?? '');
    
    $conn->query("UPDATE departments SET dept_code = '$dept_code', dept_name = '$dept_name', description = '$dept_description' WHERE dept_id = $dept_id");
    $dept_success = "Department updated successfully!";
    $page = 'departments';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_department']) && isLoggedIn() && $_SESSION['role'] == 'admin') {
    $dept_id = intval($_POST['dept_id']);
    
    $users_check = $conn->query("SELECT COUNT(*) as c FROM users WHERE dept_id = $dept_id");
    if ($users_check && $users_check->fetch_assoc()['c'] > 0) {
        $dept_error = "Cannot delete department with existing users. Reassign users first.";
    } else {
        $conn->query("DELETE FROM departments WHERE dept_id = $dept_id");
        $dept_success = "Department deleted successfully!";
        $page = 'departments';
    }
}

// ==================== UPDATE SYSTEM SETTINGS ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings']) && isLoggedIn() && $_SESSION['role'] == 'admin') {
    $system_title = $conn->real_escape_string($_POST['system_title']);
    $system_theme = $conn->real_escape_string($_POST['system_theme']);
    
    updateSetting($conn, 'system_title', $system_title);
    updateSetting($conn, 'system_theme', $system_theme);
    
    if (isset($_FILES['system_logo']) && $_FILES['system_logo']['error'] === UPLOAD_ERR_OK) {
        $logo_dir = 'image/';
        if (!file_exists($logo_dir)) {
            mkdir($logo_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['system_logo']['name'], PATHINFO_EXTENSION);
        $filename = 'EST.' . $ext;
        $filepath = $logo_dir . $filename;
        
        if (move_uploaded_file($_FILES['system_logo']['tmp_name'], $filepath)) {
            updateSetting($conn, 'system_logo', $filepath);
            $settings_success = "Settings updated successfully! Logo uploaded!";
        } else {
            $settings_error = "Failed to upload logo. Please check folder permissions.";
        }
    }
    
    if (isset($_FILES['login_bg']) && $_FILES['login_bg']['error'] === UPLOAD_ERR_OK) {
        $bg_dir = 'image/';
        if (!file_exists($bg_dir)) {
            mkdir($bg_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['login_bg']['name'], PATHINFO_EXTENSION);
        $filename = 'login_bg.' . $ext;
        $filepath = $bg_dir . $filename;
        
        if (move_uploaded_file($_FILES['login_bg']['tmp_name'], $filepath)) {
            updateSetting($conn, 'login_bg', $filepath);
            if (!isset($settings_success)) {
                $settings_success = "Settings updated successfully! Login background uploaded!";
            }
        } else {
            $settings_error = "Failed to upload login background. Please check folder permissions.";
        }
    }
    
    if (!isset($settings_success) && !isset($settings_error)) {
        $settings_success = "Settings updated successfully!";
    }
    
    $page = 'settings';
}

// ==================== USER PREFERENCES ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_preferences']) && isLoggedIn()) {
    $theme = $conn->real_escape_string($_POST['theme']);
    $user_id = $_SESSION['user_id'];
    
    updateUserTheme($conn, $user_id, $theme);
    $_SESSION['theme_preference'] = $theme;
    
    $pref_success = "Theme preference saved successfully!";
    $page = 'profile';
}

// ==================== SIGN UP ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = !empty($_POST['email']) ? $conn->real_escape_string($_POST['email']) : NULL;
    $dept_id = intval($_POST['dept_id']);
    
    $check = $conn->query("SELECT * FROM users WHERE username = '$username'");
    if ($check && $check->num_rows > 0) {
        $signup_error = "Username already exists!";
    } elseif ($password != $confirm_password) {
        $signup_error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $signup_error = "Password must be at least 6 characters!";
    } else {
        if ($conn->query("INSERT INTO users (username, password, full_name, email, dept_id, role, status, theme_preference) 
                     VALUES ('$username', '$password', '$full_name', " . ($email ? "'$email'" : "NULL") . ", $dept_id, 'staff', 'pending', 'dark')")) {
            $signup_success = "Account created successfully! Please wait for admin approval.";
            
            $admins = $conn->query("SELECT user_id FROM users WHERE role = 'admin' AND status = 'active'");
            if ($admins && $admins->num_rows > 0) {
                while($admin = $admins->fetch_assoc()) {
                    $message = "🆕 New user registration pending approval: " . $full_name . " (" . $username . ")";
                    addNotification($conn, $admin['user_id'], $message, null, true);
                }
            }
            $page = 'login';
        } else {
            $signup_error = "Database error: " . $conn->error;
        }
    }
}

// ==================== LOGIN ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    $result = $conn->query("SELECT * FROM users WHERE username = '$username'");
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password == $user['password']) {
            if ($user['status'] == 'pending') {
                $error = '⏳ Your account is pending approval. Please wait for admin confirmation.';
            } elseif ($user['status'] == 'rejected') {
                $error = '❌ Your account has been rejected. Please contact the administrator.';
            } elseif ($user['status'] == 'active') {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['dept_id'] = $user['dept_id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['theme_preference'] = $user['theme_preference'] ?? 'dark';
                $conn->query("UPDATE users SET last_login = NOW() WHERE user_id = {$user['user_id']}");
                $page = 'dashboard';
            } else {
                $error = 'Account is inactive. Please contact administrator.';
            }
        } else {
            $error = 'Invalid password';
        }
    } else {
        $error = 'User not found';
    }
}

// ==================== FORGOT PASSWORD ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['forgot_password'])) {
    $username = $conn->real_escape_string($_POST['username']);
    
    $result = $conn->query("SELECT password, full_name, email FROM users WHERE username = '$username' AND status = 'active'");
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $forgot_success = "🔐 Password reset link has been sent to your email address: <strong>" . htmlspecialchars($user['email']) . "</strong><br><small>Please check your email and follow the instructions to reset your password.</small>";
    } else {
        $forgot_error = "❌ Username not found or account is inactive! Please check your username.";
    }
}

// ==================== APPROVE USER ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve_user']) && isLoggedIn() && $_SESSION['role'] == 'admin') {
    $user_id = intval($_POST['user_id']);
    $conn->query("UPDATE users SET status = 'active' WHERE user_id = $user_id");
    $message = "✅ Your account has been APPROVED by Administrator. You can now login to the LGU Support Hub.";
    addNotification($conn, $user_id, $message, null, true);
    $approve_success = "User approved successfully!";
    $page = 'users';
}

// ==================== REJECT USER ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reject_user']) && isLoggedIn() && $_SESSION['role'] == 'admin') {
    $user_id = intval($_POST['user_id']);
    $message = "❌ Your account has been REJECTED by Administrator. Please contact the administrator for more information.";
    addNotification($conn, $user_id, $message, null, true);
    $conn->query("DELETE FROM users WHERE user_id = $user_id");
    $reject_success = "User rejected and deleted successfully!";
    $page = 'users';
}

// ==================== CHANGE PASSWORD ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password']) && isLoggedIn()) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['user_id'];
    
    $result = $conn->query("SELECT password FROM users WHERE user_id = $user_id");
    $user = $result->fetch_assoc();
    
    if ($current_password != $user['password']) {
        $password_error = "Current password is incorrect";
    } elseif ($new_password != $confirm_password) {
        $password_error = "New passwords do not match";
    } elseif (strlen($new_password) < 6) {
        $password_error = "Password must be at least 6 characters";
    } else {
        $conn->query("UPDATE users SET password = '$new_password' WHERE user_id = $user_id");
        $password_success = "Password changed successfully! You will be logged out.";
        $page = 'change_password';
        echo "<script>setTimeout(function() { window.location.href = '?logout=1'; }, 2000);</script>";
    }
}

// ==================== UPDATE PROFILE ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile']) && isLoggedIn()) {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = !empty($_POST['email']) ? $conn->real_escape_string($_POST['email']) : NULL;
    $username = $conn->real_escape_string($_POST['username']);
    $dept_id = intval($_POST['dept_id']);
    $user_id = $_SESSION['user_id'];
    
    $check = $conn->query("SELECT user_id FROM users WHERE username = '$username' AND user_id != $user_id");
    if ($check && $check->num_rows > 0) {
        $profile_error = "Username already taken!";
    } else {
        $conn->query("UPDATE users SET full_name = '$full_name', email = " . ($email ? "'$email'" : "NULL") . ", username = '$username', dept_id = $dept_id WHERE user_id = $user_id");
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email'] = $email;
        $_SESSION['username'] = $username;
        $_SESSION['dept_id'] = $dept_id;
        $profile_success = "Profile updated successfully!";
        $page = 'profile';
    }
}

// ==================== UPDATE USER ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user_profile']) && isLoggedIn() && $_SESSION['role'] == 'admin') {
    $edit_user_id = intval($_POST['user_id']);
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = !empty($_POST['email']) ? $conn->real_escape_string($_POST['email']) : NULL;
    $username = $conn->real_escape_string($_POST['username']);
    $role = $conn->real_escape_string($_POST['role']);
    $dept_id = intval($_POST['dept_id']);
    $theme = $conn->real_escape_string($_POST['theme_preference'] ?? 'dark');
    
    $conn->query("UPDATE users SET full_name = '$full_name', email = " . ($email ? "'$email'" : "NULL") . ", username = '$username', role = '$role', dept_id = $dept_id, theme_preference = '$theme' WHERE user_id = $edit_user_id");
    $edit_success = "User updated successfully!";
    $page = 'users';
}

// ==================== DELETE ACCOUNT ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_account']) && isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    
    $conn->query("DELETE FROM notifications WHERE user_id = $user_id");
    $conn->query("DELETE FROM request_details WHERE action_by = $user_id");
    $conn->query("DELETE FROM request_tracking WHERE action_by = $user_id");
    $conn->query("DELETE FROM request_timeline WHERE action_by = $user_id");
    $conn->query("DELETE FROM requests WHERE requested_by = $user_id");
    $conn->query("DELETE FROM user_preferences WHERE user_id = $user_id");
    $conn->query("DELETE FROM users WHERE user_id = $user_id");
    
    session_destroy();
    header("Location: ?page=login&deleted=1");
    exit();
}

// ==================== TOGGLE STATUS ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_status']) && isLoggedIn() && $_SESSION['role'] == 'admin') {
    $user_id = intval($_POST['user_id']);
    $current_status = $conn->query("SELECT status FROM users WHERE user_id = $user_id")->fetch_assoc()['status'];
    $new_status = ($current_status == 'active') ? 'inactive' : 'active';
    
    $conn->query("UPDATE users SET status = '$new_status' WHERE user_id = $user_id");
    $status_success = "User status updated to " . ucfirst($new_status) . "!";
    $page = 'users';
}

// ==================== MARK NOTIFICATION READ ====================
if (isset($_GET['mark_read']) && isLoggedIn()) {
    $notif_id = intval($_GET['mark_read']);
    $user_id = $_SESSION['user_id'];
    
    $conn->query("UPDATE notifications SET is_read = 1 WHERE notif_id = $notif_id AND user_id = $user_id");
    
    $notif_info = $conn->query("SELECT request_id FROM notifications WHERE notif_id = $notif_id")->fetch_assoc();
    if ($notif_info && $notif_info['request_id']) {
        header("Location: ?page=request_details&id=" . $notif_info['request_id']);
    } else {
        header("Location: ?page=notifications");
    }
    exit();
}

// ==================== MARK ALL NOTIFICATIONS READ ====================
if (isset($_GET['mark_all_read']) && isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $user_id AND is_read = 0 AND (sender_id IS NULL OR sender_id != $user_id)");
    header("Location: ?page=notifications");
    exit();
}

// ==================== LOGOUT ====================
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ?page=login");
    exit();
}
