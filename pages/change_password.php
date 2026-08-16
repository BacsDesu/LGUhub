<?php
/**
 * Page: change_password
 * Included by index.php when ?page=change_password
 */
    if (isset($password_success)): ?>
        <div class="alert alert-success"><?php echo $password_success; ?></div>
    <?php endif; ?>
    <?php if (isset($password_error)): ?>
        <div class="alert alert-danger"><?php echo $password_error; ?></div>
    <?php endif; ?>
<h2><i class="fas fa-key" style="color:var(--accent);"></i> Change Password</h2>
<p>Update your password for security purposes.</p>

<div class="card">
    <form method="POST">
        <div class="form-group">
            <label>Current Password <span class="required">*</span></label>
            <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>New Password <span class="required">*</span></label>
            <input type="password" name="new_password" class="form-control" required>
            <small style="color:var(--text-secondary);">Minimum 6 characters</small>
        </div>
        <div class="form-group">
            <label>Confirm New Password <span class="required">*</span></label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <div class="form-actions">
            <button type="submit" name="change_password" class="btn btn-primary">
                <i class="fas fa-save"></i> Change Password
            </button>
            <a href="?page=profile" class="btn btn-outline">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

