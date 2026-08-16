<?php
/**
 * Page: profile
 * Included by index.php when ?page=profile
 */
    $user = $conn->query("SELECT u.*, d.dept_name, d.dept_code FROM users u LEFT JOIN departments d ON u.dept_id = d.dept_id WHERE u.user_id = {$_SESSION['user_id']}")->fetch_assoc();
    $depts = $conn->query("SELECT * FROM departments ORDER BY dept_name");
    $user_icon = getDeptIcon($user['dept_code'], $user['dept_name']);
    $user_theme = getUserTheme($conn, $_SESSION['user_id']) ?: 'dark';
    
    if (isset($profile_success)): ?>
        <div class="alert alert-success"><?php echo $profile_success; ?></div>
    <?php endif; ?>
    <?php if (isset($profile_error)): ?>
        <div class="alert alert-danger"><?php echo $profile_error; ?></div>
    <?php endif; ?>
    <?php if (isset($pref_success)): ?>
        <div class="alert alert-success"><?php echo $pref_success; ?></div>
    <?php endif; ?>
<h2><i class="fas fa-user-circle" style="color:var(--accent);"></i> My Profile</h2>
<p>Manage your personal information.</p>

<div class="card">
    <div class="profile-header" style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
        <div class="profile-avatar" style="width:80px;height:80px;border-radius:50%;background:var(--accent-gradient);display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:white;">
            <i class="fas <?php echo $user_icon; ?>"></i>
        </div>
        <div class="profile-info">
            <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
            <p><i class="fas fa-tag"></i> Username: <?php echo htmlspecialchars($user['username']); ?></p>
            <p><i class="fas <?php echo $user_icon; ?>"></i> Department: <?php echo htmlspecialchars($user['dept_name']); ?></p>
            <p><i class="fas fa-briefcase"></i> Role: <?php echo ucfirst($user['role']); ?></p>
            <p><i class="fas fa-envelope"></i> Email: <?php echo $user['email'] ?? 'Not set'; ?></p>
            <p><i class="fas fa-calendar"></i> Member since: <?php echo date('F d, Y', strtotime($user['created_at'] ?? 'now')); ?></p>
            <p><i class="fas fa-id-card"></i> Status: <span class="status-badge <?php echo $user['status'] == 'active' ? 'status-active' : 'status-pending'; ?>"><?php echo ucfirst($user['status']); ?></span></p>
        </div>
    </div>
</div>

<div class="card">
    <h3><i class="fas fa-palette"></i> Theme Preference</h3>
    <form method="POST">
        <div class="form-group theme-selector-dropdown">
            <label>Select Theme</label>
            <select name="theme" class="form-control" id="themeSelect">
                <option value="dark" <?php echo $user_theme == 'dark' ? 'selected' : ''; ?>>🌙 Dark</option>
                <option value="blue" <?php echo $user_theme == 'blue' ? 'selected' : ''; ?>>🔵 Blue</option>
                <option value="green" <?php echo $user_theme == 'green' ? 'selected' : ''; ?>>🟢 Green</option>
                <option value="purple" <?php echo $user_theme == 'purple' ? 'selected' : ''; ?>>🟣 Purple</option>
                <option value="pink" <?php echo $user_theme == 'pink' ? 'selected' : ''; ?>>🌸 Pink</option>
                <option value="orange" <?php echo $user_theme == 'orange' ? 'selected' : ''; ?>>🟠 Orange</option>
                <option value="teal" <?php echo $user_theme == 'teal' ? 'selected' : ''; ?>>🩵 Teal</option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" name="save_preferences" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Theme
            </button>
        </div>
    </form>
</div>

<div class="card">
    <h3><i class="fas fa-edit"></i> Edit Profile</h3>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Full Name <span class="required">*</span></label>
                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Username <span class="required">*</span></label>
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Email <span style="font-weight:400;color:var(--text-secondary);">(Optional)</span></label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Department <span class="required">*</span></label>
                <select name="dept_id" class="form-control" required>
                    <?php while($d = $depts->fetch_assoc()): ?>
                        <option value="<?php echo $d['dept_id']; ?>" <?php echo $d['dept_id'] == $user['dept_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($d['dept_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name="update_profile" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Profile
            </button>
            <a href="?page=change_password" class="btn btn-secondary">
                <i class="fas fa-key"></i> Change Password
            </a>
        </div>
    </form>
</div>

<div class="card danger-zone">
    <h3><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
    <p style="color:var(--text-secondary);">Once you delete your account, there is no going back.</p>
    <form method="POST" onsubmit="return confirm('⚠️ WARNING: This will permanently delete your account!');">
        <button type="submit" name="delete_account" class="btn btn-danger">
            <i class="fas fa-trash-alt"></i> Delete My Account
        </button>
    </form>
</div>

