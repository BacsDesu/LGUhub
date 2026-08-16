<?php
/**
 * Database connection, auto-migration and schema bootstrap.
 */

// ==================== DATABASE CONNECTION WITH AUTO-CREATE ====================
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'lgu_requests_db';

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

// ==================== CREATE request_types TABLE ====================
$conn->query("CREATE TABLE IF NOT EXISTS request_types (
    type_id INT PRIMARY KEY AUTO_INCREMENT,
    type_name VARCHAR(100) UNIQUE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Insert default request types
$check_types = $conn->query("SELECT COUNT(*) as c FROM request_types");
if ($check_types && $check_types->fetch_assoc()['c'] == 0) {
    $default_types = ['Supply/Equipment', 'Document', 'Repair/Maintenance', 'Vehicle', 'Manpower', 
                      'Financial', 'IT/Computer', 'Permit/License', 'Training/Seminar', 'Other'];
    foreach ($default_types as $type) {
        $conn->query("INSERT INTO request_types (type_name) VALUES ('$type')");
    }
}

// ==================== CREATE ATTACHMENTS TABLE ====================
$conn->query("CREATE TABLE IF NOT EXISTS request_attachments (
    attachment_id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_name VARCHAR(200) NOT NULL,
    file_size INT,
    file_type VARCHAR(100),
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    uploaded_by INT,
    FOREIGN KEY (request_id) REFERENCES requests(request_id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(user_id) ON DELETE SET NULL
)");

// ==================== FIX: ADD theme_preference COLUMN to users ====================
try {
    $check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'theme_preference'");
    if (!$check_column || $check_column->num_rows == 0) {
        $conn->query("ALTER TABLE users ADD COLUMN theme_preference VARCHAR(50) DEFAULT 'dark' AFTER status");
    }
} catch (Exception $e) {}

// ==================== FIX: ENSURE users.status ENUM INCLUDES 'rejected' ====================
// The production dump (database/lgu_requests_db.sql) predates this value being added.
// login.php checks for status == 'rejected', so widen the enum if an older dump was imported.
try {
    $conn->query("ALTER TABLE users MODIFY status ENUM('active','inactive','pending','rejected') DEFAULT 'pending'");
} catch (Exception $e) {}

// ==================== FIX: ADD additional_notes COLUMN ====================
try {
    $check_column = $conn->query("SHOW COLUMNS FROM requests LIKE 'additional_notes'");
    if (!$check_column || $check_column->num_rows == 0) {
        $conn->query("ALTER TABLE requests ADD COLUMN additional_notes TEXT AFTER deadline");
    }
    
    $check_column = $conn->query("SHOW COLUMNS FROM requests LIKE 'to_dept'");
    if ($check_column && $check_column->num_rows > 0) {
        try {
            $conn->query("ALTER TABLE requests DROP FOREIGN KEY requests_ibfk_2");
        } catch (Exception $e) {}
        $conn->query("ALTER TABLE requests DROP COLUMN to_dept");
    }
    
    $check_column = $conn->query("SHOW COLUMNS FROM deleted_requests LIKE 'to_dept'");
    if ($check_column && $check_column->num_rows > 0) {
        $conn->query("ALTER TABLE deleted_requests DROP COLUMN to_dept");
    }
    
    $conn->query("ALTER TABLE requests MODIFY status ENUM('pending','seen','approved','rejected','in_progress','completed') DEFAULT 'pending'");
    $conn->query("ALTER TABLE requests MODIFY description TEXT NULL");
} catch (Exception $e) {}

// ==================== FIX: ADD ON DELETE CASCADE ====================
try {
    $conn->query("ALTER TABLE notifications DROP FOREIGN KEY IF EXISTS notifications_ibfk_2");
    $conn->query("ALTER TABLE notifications ADD CONSTRAINT notifications_ibfk_2 FOREIGN KEY (request_id) REFERENCES requests(request_id) ON DELETE CASCADE");
    
    $conn->query("ALTER TABLE request_details DROP FOREIGN KEY IF EXISTS request_details_ibfk_1");
    $conn->query("ALTER TABLE request_details ADD CONSTRAINT request_details_ibfk_1 FOREIGN KEY (request_id) REFERENCES requests(request_id) ON DELETE CASCADE");
    
    $conn->query("ALTER TABLE request_recipients DROP FOREIGN KEY IF EXISTS request_recipients_ibfk_1");
    $conn->query("ALTER TABLE request_recipients ADD CONSTRAINT request_recipients_ibfk_1 FOREIGN KEY (request_id) REFERENCES requests(request_id) ON DELETE CASCADE");
    
    $conn->query("ALTER TABLE request_tracking DROP FOREIGN KEY IF EXISTS request_tracking_ibfk_1");
    $conn->query("ALTER TABLE request_tracking ADD CONSTRAINT request_tracking_ibfk_1 FOREIGN KEY (request_id) REFERENCES requests(request_id) ON DELETE CASCADE");
    
    $conn->query("ALTER TABLE request_timeline DROP FOREIGN KEY IF EXISTS request_timeline_ibfk_1");
    $conn->query("ALTER TABLE request_timeline ADD CONSTRAINT request_timeline_ibfk_1 FOREIGN KEY (request_id) REFERENCES requests(request_id) ON DELETE CASCADE");
} catch (Exception $e) {}

// ==================== CREATE TABLES ====================
function createTables($conn) {
    $conn->query("CREATE TABLE IF NOT EXISTS system_settings (
        setting_id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS departments (
        dept_id INT PRIMARY KEY AUTO_INCREMENT,
        dept_code VARCHAR(20) UNIQUE NOT NULL,
        dept_name VARCHAR(100) NOT NULL,
        description TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS users (
        user_id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        dept_id INT,
        role ENUM('admin','department_head','staff','viewer') DEFAULT 'staff',
        status ENUM('active','inactive','pending','rejected') DEFAULT 'pending',
        theme_preference VARCHAR(50) DEFAULT 'dark',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        last_login DATETIME,
        FOREIGN KEY (dept_id) REFERENCES departments(dept_id) ON DELETE SET NULL
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS requests (
        request_id INT PRIMARY KEY AUTO_INCREMENT,
        request_number VARCHAR(50) UNIQUE NOT NULL,
        title VARCHAR(200) NOT NULL,
        description TEXT NULL,
        request_type VARCHAR(50) NOT NULL,
        priority ENUM('low','medium','high') DEFAULT 'medium',
        from_dept INT NOT NULL,
        requested_by INT NOT NULL,
        status ENUM('pending','seen','approved','rejected','in_progress','completed') DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
        deadline DATETIME,
        additional_notes TEXT,
        FOREIGN KEY (from_dept) REFERENCES departments(dept_id) ON DELETE CASCADE,
        FOREIGN KEY (requested_by) REFERENCES users(user_id) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS request_recipients (
        recipient_id INT PRIMARY KEY AUTO_INCREMENT,
        request_id INT NOT NULL,
        dept_id INT NOT NULL,
        status ENUM('pending','seen','approved','rejected','completed','in_progress') DEFAULT 'pending',
        seen_at DATETIME,
        responded_at DATETIME,
        notes TEXT,
        FOREIGN KEY (request_id) REFERENCES requests(request_id) ON DELETE CASCADE,
        FOREIGN KEY (dept_id) REFERENCES departments(dept_id) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS request_tracking (
        track_id INT PRIMARY KEY AUTO_INCREMENT,
        request_id INT NOT NULL,
        dept_id INT NOT NULL,
        action VARCHAR(100) NOT NULL,
        action_by INT NOT NULL,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (request_id) REFERENCES requests(request_id) ON DELETE CASCADE,
        FOREIGN KEY (dept_id) REFERENCES departments(dept_id) ON DELETE CASCADE,
        FOREIGN KEY (action_by) REFERENCES users(user_id) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS request_details (
        detail_id INT PRIMARY KEY AUTO_INCREMENT,
        request_id INT NOT NULL,
        action VARCHAR(100) NOT NULL,
        action_by INT NOT NULL,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (request_id) REFERENCES requests(request_id) ON DELETE CASCADE,
        FOREIGN KEY (action_by) REFERENCES users(user_id) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS notifications (
        notif_id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        request_id INT,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        is_system BOOLEAN DEFAULT FALSE,
        sender_id INT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (request_id) REFERENCES requests(request_id) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS deleted_requests (
        delete_id INT PRIMARY KEY AUTO_INCREMENT,
        original_request_id INT,
        request_number VARCHAR(50),
        title VARCHAR(200),
        description TEXT,
        request_type VARCHAR(50),
        priority VARCHAR(20),
        from_dept INT,
        requested_by INT,
        deleted_by INT,
        status_at_deletion VARCHAR(20),
        deleted_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS user_preferences (
        pref_id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        theme_name VARCHAR(50) DEFAULT 'dark',
        notifications_push BOOLEAN DEFAULT TRUE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS request_timeline (
        timeline_id INT PRIMARY KEY AUTO_INCREMENT,
        request_id INT NOT NULL,
        action VARCHAR(100) NOT NULL,
        action_by INT NOT NULL,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (request_id) REFERENCES requests(request_id) ON DELETE CASCADE,
        FOREIGN KEY (action_by) REFERENCES users(user_id) ON DELETE CASCADE
    )");

    // Insert default departments
    $check = $conn->query("SELECT COUNT(*) as c FROM departments");
    if ($check && $check->fetch_assoc()['c'] == 0) {
        $depts = [
            ['MAYOR', 'Office of the Mayor', 'Executive Department'],
            ['VICE', 'Office of the Vice Mayor', 'Legislative Department'],
            ['SB', 'Sangguniang Bayan', 'Legislative Body'],
            ['HRMO', 'Human Resource Management Office', 'Personnel Management'],
            ['BUDGET', 'Budget Office', 'Financial Planning'],
            ['TREASURY', 'Treasury Office', 'Revenue Collection'],
            ['ASSESSOR', 'Assessors Office', 'Property Assessment'],
            ['ACCOUNTING', 'Accounting Office', 'Financial Reporting'],
            ['PLANNING', 'Planning and Development Office', 'Development Planning'],
            ['ENGINEERING', 'Engineering Office', 'Infrastructure'],
            ['GSO', 'General Services Office', 'Procurement and Logistics'],
            ['AGRICULTURE', 'Agriculture Office', 'Agricultural Services'],
            ['HEALTH', 'Health Office', 'Public Health'],
            ['SWDO', 'Social Welfare and Development Office', 'Social Services'],
            ['TOURISM', 'Tourism Office', 'Tourism Promotion'],
            ['DRRM', 'Disaster Risk Reduction and Management', 'Disaster Management'],
            ['PEACE', 'Peace and Order Office', 'Public Safety'],
            ['YOUTH', 'Youth and Sports Development Office', 'Youth Affairs'],
            ['WOMEN', 'Womens Affairs Office', 'Gender and Development'],
            ['BARANGAY', 'Barangay Affairs Office', 'Barangay Coordination']
        ];
        
        foreach ($depts as $d) {
            $conn->query("INSERT INTO departments (dept_code, dept_name, description) VALUES ('$d[0]', '$d[1]', '$d[2]')");
        }
    }

    $settings_check = $conn->query("SELECT COUNT(*) as c FROM system_settings");
    if ($settings_check && $settings_check->fetch_assoc()['c'] == 0) {
        $conn->query("INSERT INTO system_settings (setting_key, setting_value) VALUES 
            ('system_title', 'LGU Support Hub'),
            ('system_logo', 'image/EST.WEBP'),
            ('system_theme', 'dark'),
            ('login_bg', 'image/EST.WEBP')");
    }

    $check = $conn->query("SELECT COUNT(*) as c FROM users WHERE username = 'admin'");
    if ($check && $check->fetch_assoc()['c'] == 0) {
        $dept_id = $conn->query("SELECT dept_id FROM departments WHERE dept_code = 'MAYOR' LIMIT 1")->fetch_assoc()['dept_id'];
        $conn->query("INSERT INTO users (username, password, full_name, email, dept_id, role, status, theme_preference) 
                     VALUES ('admin', 'Admin@2024', 'System Administrator', 'admin@lgu.gov.ph', $dept_id, 'admin', 'active', 'dark')");
        
        $users = [
            ['juan.delacruz', 'User@2024', 'Juan Dela Cruz', 'juan@lgu.gov.ph', 'ENGINEERING', 'department_head'],
            ['maria.santos', 'User@2024', 'Maria Santos', 'maria@lgu.gov.ph', 'HEALTH', 'department_head'],
            ['pedro.reyes', 'User@2024', 'Pedro Reyes', 'pedro@lgu.gov.ph', 'BUDGET', 'staff']
        ];
        
        foreach ($users as $u) {
            $dept = $conn->query("SELECT dept_id FROM departments WHERE dept_code = '$u[4]' LIMIT 1")->fetch_assoc();
            if ($dept) {
                $conn->query("INSERT INTO users (username, password, full_name, email, dept_id, role, status, theme_preference) 
                             VALUES ('$u[0]', '$u[1]', '$u[2]', '$u[3]', {$dept['dept_id']}, '$u[5]', 'active', 'dark')");
            }
        }
    }
}

createTables($conn);
