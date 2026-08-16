<?php
/**
 * Page: login
 * Included by index.php when ?page=login
 */
    $depts = $conn->query("SELECT * FROM departments ORDER BY dept_name");
?>
<div class="login-wrapper">
    <div class="login-container">
        <div class="login-logo">
            <img src="<?php echo $system_logo; ?>" class="logo-img" alt="Logo" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22 viewBox=%220 0 100 100%22%3E%3Crect width=%22100%22 height=%22100%22 fill=%22%236C63FF%22/%3E%3Ctext x=%2250%22 y=%2265%22 text-anchor=%22middle%22 fill=%22white%22 font-size=%2250%22 font-weight=%22bold%22 font-family=%22sans-serif%22%3E🏛%3C/text%3E%3C/svg%3E';">
            <h1><?php echo $system_title; ?></h1>
            <p>Internal Request Management System</p>
            <p style="font-size:0.7rem;color:var(--text-secondary);">with Real-Time Department Tracking</p>
        </div>

        <div id="loginError" class="login-error <?php echo isset($error) ? 'show' : ''; ?>">
            <?php echo $error ?? ''; ?>
        </div>
        <?php if (isset($signup_success)): ?>
        <div class="alert alert-success"><?php echo $signup_success; ?></div>
        <?php endif; ?>
        <?php if (isset($forgot_success)): ?>
        <div class="alert alert-success"><?php echo $forgot_success; ?></div>
        <?php endif; ?>
        <?php if (isset($forgot_error)): ?>
        <div class="alert alert-danger"><?php echo $forgot_error; ?></div>
        <?php endif; ?>
        <?php if (isset($signup_error)): ?>
        <div class="alert alert-danger"><?php echo $signup_error; ?></div>
        <?php endif; ?>

        <div class="login-tabs">
            <button class="login-tab active" onclick="switchTab('login')"><i class="fas fa-sign-in-alt"></i> Sign In</button>
            <button class="login-tab" onclick="switchTab('signup')"><i class="fas fa-user-plus"></i> Sign Up</button>
        </div>

        <form class="login-form active" id="loginForm" method="POST">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Username</label>
                <div class="input-group">
                    <i class="fas fa-user-circle"></i>
                    <input type="text" name="username" placeholder="Enter your username" value="" required autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <div class="input-group">
                    <i class="fas fa-key"></i>
                    <input type="password" name="password" placeholder="Enter your password" value="" required autocomplete="off">
                </div>
            </div>
            <button type="submit" name="login" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <div class="forgot-link">
            <a href="#" onclick="showForgotModal(); return false;"><i class="fas fa-key"></i> Forgot Password?</a>
        </div>

        <form class="login-form" id="signupForm" method="POST" autocomplete="off">
            <div class="form-group">
                <label>Full Name <span class="required">*</span></label>
                <div class="input-group">
                    <i class="fas fa-id-card"></i>
                    <input type="text" name="full_name" placeholder="Enter your full name" required>
                </div>
            </div>
            <div class="form-group">
                <label>Email <span style="font-weight:400;color:var(--text-secondary);">(Optional)</span></label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Enter your email address">
                </div>
            </div>
            <div class="form-group">
                <label>Department <span class="required">*</span></label>
                <div class="input-group">
                    <i class="fas fa-building"></i>
                    <select name="dept_id" required>
                        <option value="">-- Select Department --</option>
                        <?php 
                        $depts->data_seek(0);
                        while($d = $depts->fetch_assoc()): ?>
                        <option value="<?php echo $d['dept_id']; ?>"><?php echo htmlspecialchars($d['dept_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Username <span class="required">*</span></label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Choose a username" required autocomplete="off">
                </div>
                <small style="color:var(--text-secondary);"><i class="fas fa-info-circle"></i> Must be unique</small>
            </div>
            <div class="form-group">
                <label>Password <span class="required">*</span></label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Minimum 6 characters" required autocomplete="new-password">
                </div>
                <small style="color:var(--text-secondary);"><i class="fas fa-info-circle"></i> Minimum 6 characters</small>
            </div>
            <div class="form-group">
                <label>Confirm Password <span class="required">*</span></label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" placeholder="Confirm your password" required autocomplete="off">
                </div>
            </div>
            <button type="submit" name="signup" class="login-btn">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>

        <div class="login-footer">
            <p><i class="fas fa-shield-alt" style="color:var(--accent);"></i> Secure • Encrypted • Protected</p>
            <p style="margin-top:6px;">© <?php echo date('Y'); ?> <?php echo $system_title; ?> v5.0</p>
            <p style="font-size:0.65rem;color:var(--text-secondary);">With Documentation • Multi-Department Tracking • Complete Archive</p>
        </div>
    </div>
</div>

<div id="forgotModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-key" style="color:var(--accent);"></i> Forgot Password</h3>
            <span class="modal-close" onclick="closeForgotModal()">&times;</span>
        </div>
        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Enter Your Username</label>
                <div class="input-group">
                    <i class="fas fa-user" style="left:14px;top:50%;transform:translateY(-50%);position:absolute;color:var(--text-secondary);"></i>
                    <input type="text" name="username" class="form-control" placeholder="Enter your username" required style="padding-left:44px;">
                </div>
                <small style="color:var(--text-secondary);"><i class="fas fa-info-circle"></i> Enter your username to receive a password reset link</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeForgotModal()"><i class="fas fa-times"></i> Cancel</button>
                <button type="submit" name="forgot_password" class="btn btn-primary">
                    <i class="fas fa-envelope"></i> Send Reset Link
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.login-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.login-form').forEach(f => f.classList.remove('active'));
    
    if (tab === 'login') {
        document.querySelectorAll('.login-tab')[0].classList.add('active');
        document.getElementById('loginForm').classList.add('active');
    } else {
        document.querySelectorAll('.login-tab')[1].classList.add('active');
        document.getElementById('signupForm').classList.add('active');
    }
}

function showForgotModal() {
    document.getElementById('forgotModal').classList.add('active');
}

function closeForgotModal() {
    document.getElementById('forgotModal').classList.remove('active');
}

document.getElementById('forgotModal').addEventListener('click', function(e) {
    if (e.target === this) closeForgotModal();
});
</script>

