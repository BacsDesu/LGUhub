<?php
/**
 * Shared header: <head>, dynamic per-theme CSS variables, sidebar + topbar.
 * Included by index.php on every page except the raw redirect cases.
 */
$request_types_result = $conn->query("SELECT * FROM request_types ORDER BY type_name");
$request_types = [];
while($row = $request_types_result->fetch_assoc()) {
    $request_types[] = $row['type_name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $system_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Dynamic per-theme CSS variables — depend on session theme, so generated inline */
        :root {
            --sidebar-bg: <?php echo $current_theme_colors['sidebar']; ?>;
            --sidebar-hover: <?php echo $current_theme_colors['sidebar_hover']; ?>;
            --header-bg: <?php echo $current_theme_colors['header']; ?>;
            --header-border: <?php echo $current_theme_colors['header_border']; ?>;
            --card-bg: <?php echo $current_theme_colors['card_bg']; ?>;
            --card-border: <?php echo $current_theme_colors['card_border']; ?>;
            --text-primary: <?php echo $current_theme_colors['text_primary']; ?>;
            --text-secondary: <?php echo $current_theme_colors['text_secondary']; ?>;
            --input-bg: <?php echo $current_theme_colors['input_bg']; ?>;
            --input-border: <?php echo $current_theme_colors['input_border']; ?>;
            --stat-bg: <?php echo $current_theme_colors['stat_bg']; ?>;
            --accent: <?php echo $current_theme_colors['accent']; ?>;
            --accent-rgb: <?php echo $current_theme_colors['accent_rgb'] ?? '108,99,255'; ?>;
            --accent-gradient: <?php echo $current_theme_colors['accent_gradient']; ?>;
            --sidebar-text: <?php echo $current_theme_colors['sidebar_text']; ?>;
            --sidebar-active: <?php echo $current_theme_colors['sidebar_active']; ?>;
            --header-text: <?php echo $current_theme_colors['header_text']; ?>;
            --login-container-bg: <?php echo $current_theme_colors['login_container']; ?>;
            --login-text: <?php echo $current_theme_colors['login_text']; ?>;
            --login-input-bg: <?php echo $current_theme_colors['login_input_bg']; ?>;
            --bg-color: <?php echo $current_theme_colors['bg_color']; ?>;
            --shadow-color: rgba(0,0,0,0.1);
            --border-light: <?php echo $current_theme_colors['input_border']; ?>;
        }
    </style>
</head>
<body class="page-<?php echo $page; ?>">
<script>
if (localStorage.getItem('sidebarCollapsed') === '1' && window.innerWidth > 768) {
    document.body.classList.add('sidebar-collapsed');
}
</script>

<?php if (isLoggedIn() && $page != 'login'): 
    $unread_count = getUnreadCount($conn, $_SESSION['user_id']);
    $dept_name = getDeptName($conn, $_SESSION['dept_id']);
    $dept_code = getDeptCode($conn, $_SESSION['dept_id']);
    $user_dept_icon = getUserDeptIcon($conn, $_SESSION['user_id']);
?>
    <nav class="sidebar" id="sidebar">
        <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="Collapse sidebar">
            <i class="fas fa-angles-left"></i>
        </button>
        <div class="sidebar-brand">
            <img src="<?php echo $system_logo; ?>" class="logo-img" alt="Logo" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22%3E%3Crect width=%2260%22 height=%2260%22 fill=%22%236C63FF%22/%3E%3Ctext x=%2230%22 y=%2238%22 text-anchor=%22middle%22 fill=%22white%22 font-size=%2230%22 font-weight=%22bold%22 font-family=%22sans-serif%22%3E🏛%3C/text%3E%3C/svg%3E';">
            <div class="sidebar-brand-text">
                <h2><?php echo $system_title; ?></h2>
                <small>Internal Request Management</small>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-label">Main Navigation</li>
            <li><a href="?page=dashboard" class="<?php echo $page == 'dashboard' ? 'active' : ''; ?>" title="Dashboard">
                <span class="icon"><i class="fas fa-chart-pie"></i></span> <span class="link-text">Dashboard</span>
            </a></li>
            <li><a href="?page=create_request" class="<?php echo $page == 'create_request' ? 'active' : ''; ?>" title="New Request">
                <span class="icon"><i class="fas fa-plus-circle"></i></span> <span class="link-text">New Request</span>
            </a></li>
            <li><a href="?page=view_requests" class="<?php echo $page == 'view_requests' ? 'active' : ''; ?>" title="All Requests">
                <span class="icon"><i class="fas fa-list"></i></span> <span class="link-text">All Requests</span>
            </a></li>
            <li><a href="?page=documentation" class="<?php echo $page == 'documentation' ? 'active' : ''; ?>" title="Documentation">
                <span class="icon"><i class="fas fa-archive"></i></span> <span class="link-text">Documentation</span>
            </a></li>
            <li><a href="?page=recently_deleted" class="<?php echo $page == 'recently_deleted' ? 'active' : ''; ?>" title="Recently Deleted">
                <span class="icon"><i class="fas fa-trash-restore"></i></span> <span class="link-text">Recently Deleted</span>
            </a></li>
            
            <li class="menu-label" style="margin-top:12px;">Management</li>
            <li><a href="?page=departments" class="<?php echo $page == 'departments' ? 'active' : ''; ?>" title="Departments">
                <span class="icon"><i class="fas fa-building"></i></span> <span class="link-text">Departments</span>
            </a></li>
            <?php if ($_SESSION['role'] == 'admin'): ?>
            <li><a href="?page=users" class="<?php echo $page == 'users' ? 'active' : ''; ?>" title="Manage Users">
                <span class="icon"><i class="fas fa-users-cog"></i></span> <span class="link-text">Manage Users</span>
            </a></li>
            <li><a href="?page=settings" class="<?php echo $page == 'settings' ? 'active' : ''; ?>" title="Settings">
                <span class="icon"><i class="fas fa-cog"></i></span> <span class="link-text">Settings</span>
            </a></li>
            <?php endif; ?>
            
            <li class="menu-label" style="margin-top:12px;">Account</li>
            <li><a href="?page=profile" class="<?php echo $page == 'profile' ? 'active' : ''; ?>" title="My Profile">
                <span class="icon"><i class="fas fa-user-circle"></i></span> <span class="link-text">My Profile</span>
            </a></li>
            <li><a href="?page=change_password" class="<?php echo $page == 'change_password' ? 'active' : ''; ?>" title="Change Password">
                <span class="icon"><i class="fas fa-key"></i></span> <span class="link-text">Change Password</span>
            </a></li>
            <li><a href="?logout=1" onclick="return confirm('Logout?')" title="Logout">
                <span class="icon"><i class="fas fa-sign-out-alt"></i></span> <span class="link-text">Logout</span>
            </a></li>
        </ul>
    </nav>


    <div class="main-content">
        <header class="header">
            <div class="header-left">
                <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
                <div>
                    <h1>
                        <?php
                        switch($page) {
                            case 'create_request': echo '<i class="fas fa-plus-circle"></i> Create New Request'; break;
                            case 'view_requests': echo '<i class="fas fa-list"></i> All Requests'; break;
                            case 'documentation': echo '<i class="fas fa-archive"></i> Documentation'; break;
                            case 'recently_deleted': echo '<i class="fas fa-trash-restore"></i> Recently Deleted'; break;
                            case 'departments': echo '<i class="fas fa-building"></i> Departments'; break;
                            case 'users': echo '<i class="fas fa-users-cog"></i> User Management'; break;
                            case 'profile': echo '<i class="fas fa-user-circle"></i> My Profile'; break;
                            case 'change_password': echo '<i class="fas fa-key"></i> Change Password'; break;
                            case 'settings': echo '<i class="fas fa-cog"></i> System Settings'; break;
                            case 'request_details': echo '<i class="fas fa-file-alt"></i> Request Details & Tracking'; break;
                            case 'notifications': echo '<i class="fas fa-bell"></i> Notifications'; break;
                            default: echo '<i class="fas fa-chart-pie"></i> Dashboard';
                        }
                        ?>
                    </h1>
                    <div class="breadcrumb">
                        <i class="fas fa-home" style="font-size:0.7rem;"></i> 
                        <?php echo $page == 'dashboard' ? 'Dashboard' : 'Dashboard / ' . ucwords(str_replace('_', ' ', $page)); ?>
                    </div>
                </div>
            </div>
            <div class="header-right">
                <a href="?page=notifications" class="notif-btn" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <?php if ($unread_count > 0): ?>
                    <span class="notif-badge"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
                <div class="user-profile" onclick="window.location.href='?page=profile'">
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 2)); ?></div>
                    <div class="user-info">
                        <div class="name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></div>
                        <div class="role"><i class="fas <?php echo $user_dept_icon; ?>"></i> <?php echo htmlspecialchars($dept_name); ?></div>
                    </div>
                </div>
            </div>
        </header>

        <div class="content">
<?php endif; ?>
