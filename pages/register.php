<?php /* Page: register — included by index.php when ?page=register */ ?>
    <!-- Registration Section -->
    <div class="register-container">
        <div class="form-card">
            <h3>Register New User</h3>
            <form method="POST">
                <input type="hidden" name="action" value="register_user">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" required placeholder="Enter first name">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" required placeholder="Enter last name">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="Enter email address">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter password">
                </div>
                <div class="form-group">
                    <label>User Role</label>
                    <select name="role_id" required>
                        <option value="">Select Role</option>
                        <?php foreach($roles as $r): ?>
                        <option value="<?= $r['role_id'] ?>"><?= htmlspecialchars($r['role_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Register User</button>
            </form>
        </div>
    </div>
