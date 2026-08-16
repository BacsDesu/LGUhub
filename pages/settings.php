<?php
/**
 * Page: settings
 * Included by index.php when ?page=settings
 */
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') { header('Location: ?page=dashboard'); exit(); }
    if (isset($settings_success)): ?>
        <div class="alert alert-success"><?php echo $settings_success; ?></div>
    <?php endif; ?>
    <?php if (isset($settings_error)): ?>
        <div class="alert alert-danger"><?php echo $settings_error; ?></div>
    <?php endif; 
    
    $system_title = getSetting($conn, 'system_title') ?: 'LGU Support Hub';
    $system_logo = getSetting($conn, 'system_logo') ?: 'image/EST.WEBP';
    $login_bg = getSetting($conn, 'login_bg') ?: 'image/EST.WEBP';
    $system_theme = getSetting($conn, 'system_theme') ?: 'dark';
?>
<h2><i class="fas fa-cog" style="color:var(--accent);"></i> System Settings</h2>
<p>Customize your system settings including logo, title, theme, and login background.</p>

<div class="card">
    <h3><i class="fas fa-image"></i> Logo & Branding</h3>
    
    <div class="settings-preview">
        <img src="<?php echo $system_logo; ?>" class="preview-logo" alt="Current Logo" id="currentLogoPreview">
        <div>
            <div class="preview-title" id="titlePreview"><?php echo $system_title; ?></div>
            <div style="font-size:0.8rem;color:var(--text-secondary);">Current Logo & Title</div>
        </div>
    </div>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>System Title</label>
            <input type="text" name="system_title" class="form-control" value="<?php echo htmlspecialchars($system_title); ?>" oninput="document.getElementById('titlePreview').textContent = this.value">
        </div>
        
        <div class="form-group">
            <label>System Logo</label>
            <div style="margin-bottom:8px;">
                <input type="file" name="system_logo" id="logoUpload" accept=".png,.jpg,.jpeg,.webp" style="display:none;" onchange="previewLogo(this)">
            </div>
            <button type="button" class="upload-btn" onclick="document.getElementById('logoUpload').click();">
                <i class="fas fa-upload"></i> Apply Logo
            </button>
            <small style="display:block;margin-top:4px;color:var(--text-secondary);">Recommended size: 200x200px</small>
        </div>
        
        <h3 class="mt-3"><i class="fas fa-image"></i> Login Background</h3>
        
        <div class="settings-preview">
            <img src="<?php echo $login_bg; ?>" class="preview-logo" alt="Login Background" id="loginBgPreview">
            <div>
                <div style="font-size:1rem;font-weight:700;color:var(--text-primary);">Login Background</div>
                <div style="font-size:0.8rem;color:var(--text-secondary);">Current background image</div>
            </div>
        </div>
        
        <div class="form-group">
            <label>Login Background Image</label>
            <div style="margin-bottom:8px;">
                <input type="file" name="login_bg" id="loginBgUpload" accept=".png,.jpg,.jpeg,.webp" style="display:none;" onchange="previewLoginBg(this)">
            </div>
            <button type="button" class="upload-btn" onclick="document.getElementById('loginBgUpload').click();">
                <i class="fas fa-upload"></i> Apply Background
            </button>
            <small style="display:block;margin-top:4px;color:var(--text-secondary);">Recommended size: 1920x1080px</small>
        </div>
        
        <h3 class="mt-3"><i class="fas fa-palette"></i> System Theme</h3>
        
        <div class="form-group theme-selector-dropdown">
            <label>Default System Theme (applies to all users)</label>
            <select name="system_theme" class="form-control" id="systemThemeSelect">
                <option value="dark" <?php echo $system_theme == 'dark' ? 'selected' : ''; ?>>🌙 Dark</option>
                <option value="blue" <?php echo $system_theme == 'blue' ? 'selected' : ''; ?>>🔵 Blue</option>
                <option value="green" <?php echo $system_theme == 'green' ? 'selected' : ''; ?>>🟢 Green</option>
                <option value="purple" <?php echo $system_theme == 'purple' ? 'selected' : ''; ?>>🟣 Purple</option>
                <option value="pink" <?php echo $system_theme == 'pink' ? 'selected' : ''; ?>>🌸 Pink</option>
                <option value="orange" <?php echo $system_theme == 'orange' ? 'selected' : ''; ?>>🟠 Orange</option>
                <option value="teal" <?php echo $system_theme == 'teal' ? 'selected' : ''; ?>>🩵 Teal</option>
            </select>
            <small style="color:var(--text-secondary);">This will be the default theme for all users. Users can override this in their profile.</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" name="update_settings" class="btn btn-primary">
                <i class="fas fa-save"></i> Save All Settings
            </button>
        </div>
    </form>
</div>

<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('currentLogoPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function previewLoginBg(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('loginBgPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

