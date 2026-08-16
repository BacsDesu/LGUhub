<?php
/**
 * Page: create_request
 * Included by index.php when ?page=create_request
 */
    $departments = $conn->query("SELECT * FROM departments ORDER BY dept_name");
    $request_types_result = $conn->query("SELECT * FROM request_types ORDER BY type_name");
    $request_types = [];
    while($row = $request_types_result->fetch_assoc()) {
        $request_types[] = $row['type_name'];
    }
    
    if (isset($_POST['add_request_type']) && isLoggedIn()) {
        $new_type = $conn->real_escape_string(trim($_POST['new_request_type']));
        if (!empty($new_type)) {
            $check = $conn->query("SELECT * FROM request_types WHERE type_name = '$new_type'");
            if ($check->num_rows == 0) {
                $conn->query("INSERT INTO request_types (type_name) VALUES ('$new_type')");
                $type_added = "New request type '$new_type' added.";
                $request_types_result = $conn->query("SELECT * FROM request_types ORDER BY type_name");
                $request_types = [];
                while($row = $request_types_result->fetch_assoc()) {
                    $request_types[] = $row['type_name'];
                }
            } else {
                $type_error = "That request type already exists.";
            }
        }
    }
    
    if (isset($_POST['remove_request_type']) && isLoggedIn()) {
        $remove_type = $conn->real_escape_string(trim($_POST['remove_type_name']));
        $default_types = ['Supply/Equipment', 'Document', 'Repair/Maintenance', 'Vehicle', 'Manpower', 
                          'Financial', 'IT/Computer', 'Permit/License', 'Training/Seminar', 'Other'];
        if (!in_array($remove_type, $default_types)) {
            $conn->query("DELETE FROM request_types WHERE type_name = '$remove_type'");
            $type_removed = "Request type '$remove_type' removed.";
            $request_types_result = $conn->query("SELECT * FROM request_types ORDER BY type_name");
            $request_types = [];
            while($row = $request_types_result->fetch_assoc()) {
                $request_types[] = $row['type_name'];
            }
        } else {
            $type_error = "Default request types can't be removed.";
        }
    }
?>
<h2><i class="fas fa-plus-circle" style="color:var(--accent);"></i> Create New Request</h2>
<p>Route a request to one or more departments for review and action.</p>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
<?php endif; ?>
<?php if (isset($type_added)): ?>
    <div class="alert alert-success"><?php echo $type_added; ?></div>
<?php endif; ?>
<?php if (isset($type_removed)): ?>
    <div class="alert alert-success"><?php echo $type_removed; ?></div>
<?php endif; ?>
<?php if (isset($type_error)): ?>
    <div class="alert alert-danger"><?php echo $type_error; ?></div>
<?php endif; ?>

<div class="card">
    <form method="POST" enctype="multipart/form-data" id="requestForm">

        <div class="form-section">
        <div class="form-section-title"><span class="step-num">1</span> Request details</div>

        <div class="form-group">
            <label>Request Type <span class="required">*</span></label>
            <div class="request-type-wrapper">
                <select name="request_type" id="requestTypeSelect" class="form-control" required>
                    <option value="">-- Select Type --</option>
                    <?php foreach ($request_types as $type): ?>
                        <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="type-actions">
                    <button type="button" class="btn btn-sm btn-outline" onclick="toggleCustomType()" title="Add New Type">
                        <i class="fas fa-plus"></i> Add
                    </button>
                    <button type="button" class="btn-remove-type" id="removeTypeBtn" onclick="removeSelectedType()" title="Remove Selected Type" style="display:none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="custom-type-input" id="customTypeInput">
                <div style="display:flex;gap:8px;align-items:center;">
                    <input type="text" name="custom_request_type" id="customRequestType" class="form-control" placeholder="Enter new request type...">
                    <button type="button" class="btn btn-success btn-sm" onclick="addCustomType()">
                        <i class="fas fa-plus"></i> Add
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="toggleCustomType()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
                <small style="display:block;margin-top:4px;font-size:0.75rem;color:var(--text-secondary);">
                    <i class="fas fa-info-circle" style="color:var(--accent);"></i> Type a new request type and click "Add" to save it
                </small>
            </div>
            
            <small style="display:block;margin-top:4px;font-size:0.75rem;color:var(--text-secondary);">
                <i class="fas fa-info-circle" style="color:var(--accent);"></i> Select a request type, add a new one, or use "Other" with a custom name
            </small>
        </div>
        
        <div class="form-group">
            <label>Priority Level <span class="required">*</span></label>
            <select name="priority" class="form-control" required>
                <option value="low">🟢 Low</option>
                <option value="medium" selected>🟡 Medium</option>
                <option value="high">🔴 High</option>
            </select>
            <small style="display:block;margin-top:4px;font-size:0.75rem;color:var(--text-secondary);"><i class="fas fa-info-circle" style="color:var(--accent);"></i> Set the urgency level</small>
        </div>

        <div class="form-group">
            <label>Description <span style="font-weight:400;color:var(--text-secondary);font-size:0.8rem;">(Optional)</span></label>
            <textarea name="description" class="form-control" rows="3" placeholder="Provide additional details about your request (optional)"></textarea>
            <small style="display:block;margin-top:4px;font-size:0.75rem;color:var(--text-secondary);"><i class="fas fa-info-circle" style="color:var(--accent);"></i> You can leave this blank</small>
        </div>

        <div class="form-group">
            <label>Deadline <span class="required">*</span></label>
            <div class="form-row" style="display:grid;grid-template-columns:2fr 1fr;gap:12px;">
                <input type="date" name="deadline_date" class="form-control" required>
                <input type="time" name="deadline_time" class="form-control" placeholder="Time (optional)">
            </div>
            <small style="display:block;margin-top:4px;font-size:0.75rem;color:var(--text-secondary);"><i class="fas fa-calendar-alt" style="color:var(--accent);"></i> Date is required. Time is optional.</small>
        </div>
        </div>

        <div class="form-section">
        <div class="form-section-title"><span class="step-num">2</span> Attachments</div>

        <div class="form-group">
            <label>Attachments <span class="required">*</span></label>
            <div class="attachment-label"><i class="fas fa-paperclip"></i> Upload required attachments</div>
            <div style="margin-bottom:8px;">
                <input type="file" name="attachments[]" id="fileInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple style="display:none;">
            </div>
            
            <button type="button" class="add-attachment-btn" onclick="addMoreFiles()">
                <i class="fas fa-plus"></i> Add Attachment
            </button>
            
            <div id="fileList" class="file-list"></div>
            <small style="display:block;margin-top:4px;font-size:0.75rem;color:var(--text-secondary);">
                <i class="fas fa-info-circle" style="color:var(--accent);"></i> Click "Add Attachment" to upload multiple files (PDF, DOC, DOCX, XLS, XLSX, JPG, PNG)
            </small>
        </div>
        </div>

        <div class="form-section">
        <div class="form-section-title"><span class="step-num">3</span> Recipients</div>

        <div class="form-group">
            <label>Send to Departments <span class="required">*</span></label>
            <div class="checkbox-select-all">
                <input type="checkbox" id="selectAll" onclick="toggleAllDepartments(this)">
                <label for="selectAll"><i class="fas fa-check-circle"></i> Select All Departments</label>
                <span style="margin-left:auto;font-size:0.75rem;color:var(--text-secondary);" id="selectedCount">0 selected</span>
            </div>
            <div class="checkbox-group">
                <?php 
                $depts = $conn->query("SELECT * FROM departments ORDER BY dept_name");
                while($d = $depts->fetch_assoc()): 
                    $disabled = ($d['dept_id'] == $_SESSION['dept_id']) ? 'disabled' : '';
                ?>
                <div class="checkbox-item">
                    <input type="checkbox" name="recipient_depts[]" value="<?php echo $d['dept_id']; ?>" 
                           id="dept_<?php echo $d['dept_id']; ?>" 
                           <?php echo $disabled; ?>
                           onchange="updateSelectedCount()">
                    <label for="dept_<?php echo $d['dept_id']; ?>">
                        <i class="fas fa-building" style="color:var(--accent);"></i>
                        <?php echo htmlspecialchars($d['dept_name']); ?>
                        <?php if ($d['dept_id'] == $_SESSION['dept_id']): ?>
                            <span style="font-size:0.6rem;color:var(--text-secondary);">(Your Dept)</span>
                        <?php endif; ?>
                    </label>
                </div>
                <?php endwhile; ?>
            </div>
            <small style="display:block;margin-top:4px;font-size:0.75rem;color:var(--text-secondary);"><i class="fas fa-info-circle" style="color:var(--accent);"></i> Your own department is disabled. Select at least one department.</small>
        </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="create_request" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Submit Request
            </button>
            <a href="?page=dashboard" class="btn btn-outline">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<!-- SUCCESS POPUP -->
<?php if (isset($show_popup) && $show_popup && isset($request_id)): ?>
<div id="successPopup" class="modal-overlay" style="display:flex;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);z-index:9999;align-items:center;justify-content:center;">
    <div class="modal" style="max-width:450px;text-align:center;background:var(--card-bg);border-radius:16px;padding:40px;animation:modalSlide 0.3s ease;">
        <div style="font-size:3.2rem;color:#2E9E5B;margin-bottom:14px;">
            <i class="fas fa-check-circle"></i>
        </div>
        <h3 style="color:var(--text-primary);margin-bottom:8px;">Request sent</h3>
        <p style="color:var(--text-secondary);margin-bottom:16px;font-size:0.95rem;">
            <?php echo $popup_message; ?>
        </p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="?page=request_details&id=<?php echo $request_id; ?>" class="btn btn-primary">
                <i class="fas fa-eye"></i> View Request
            </a>
            <a href="?page=view_requests" class="btn btn-secondary">
                <i class="fas fa-list"></i> All Requests
            </a>
            <a href="?page=create_request" class="btn btn-outline">
                <i class="fas fa-plus"></i> Create Another
            </a>
        </div>
        <button onclick="document.getElementById('successPopup').style.display='none'" 
                style="margin-top:16px;background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:0.8rem;">
            <i class="fas fa-times"></i> Close
        </button>
    </div>
</div>

<script>
// Auto-close after 10 seconds
setTimeout(function() {
    var popup = document.getElementById('successPopup');
    if (popup) {
        popup.style.display = 'none';
    }
}, 10000);

// Close when clicking outside
document.getElementById('successPopup')?.addEventListener('click', function(e) {
    if (e.target === this) {
        this.style.display = 'none';
    }
});
</script>
<?php endif; ?>

<script>
function toggleCustomType() {
    const input = document.getElementById('customTypeInput');
    input.classList.toggle('show');
    if (input.classList.contains('show')) {
        document.getElementById('customRequestType').focus();
    }
}

function addCustomType() {
    const value = document.getElementById('customRequestType').value.trim();
    if (value) {
        const form = document.createElement('form');
        form.method = 'POST';
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'new_request_type';
        input.value = value;
        form.appendChild(input);
        const btn = document.createElement('input');
        btn.type = 'hidden';
        btn.name = 'add_request_type';
        btn.value = '1';
        form.appendChild(btn);
        document.body.appendChild(form);
        form.submit();
    } else {
        alert('Please enter a request type name.');
    }
}

function removeSelectedType() {
    const select = document.getElementById('requestTypeSelect');
    const selected = select.value;
    if (selected && selected !== 'Other') {
        if (confirm(`Remove request type "${selected}"? This cannot be undone.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'remove_type_name';
            input.value = selected;
            form.appendChild(input);
            const btn = document.createElement('input');
            btn.type = 'hidden';
            btn.name = 'remove_request_type';
            btn.value = '1';
            form.appendChild(btn);
            document.body.appendChild(form);
            form.submit();
        }
    } else if (selected === 'Other') {
        alert('Cannot remove the "Other" default type.');
    } else {
        alert('Please select a type to remove.');
    }
}

document.getElementById('requestTypeSelect').addEventListener('change', function() {
    const removeBtn = document.getElementById('removeTypeBtn');
    if (this.value && this.value !== 'Other') {
        removeBtn.style.display = 'inline-flex';
    } else {
        removeBtn.style.display = 'none';
    }
    
    const customInput = document.getElementById('customTypeInput');
    if (this.value === 'Other') {
        customInput.classList.add('show');
        document.getElementById('customRequestType').focus();
    } else {
        customInput.classList.remove('show');
    }
});

const fileInput = document.getElementById('fileInput');
const fileList = document.getElementById('fileList');

function addMoreFiles() {
    const tempInput = document.createElement('input');
    tempInput.type = 'file';
    tempInput.multiple = true;
    tempInput.accept = '.pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png';
    tempInput.onchange = function(e) {
        if (this.files.length > 0) {
            const dt = new DataTransfer();
            for (let i = 0; i < fileInput.files.length; i++) {
                dt.items.add(fileInput.files[i]);
            }
            for (let i = 0; i < this.files.length; i++) {
                dt.items.add(this.files[i]);
            }
            fileInput.files = dt.files;
            updateFileList(fileInput.files);
            fileInput.required = false;
        }
    };
    tempInput.click();
}

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        updateFileList(e.target.files);
        fileInput.required = false;
    }
});

function updateFileList(files) {
    fileList.innerHTML = '';
    if (files.length === 0) {
        fileInput.required = true;
        return;
    }
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const size = (file.size / 1024).toFixed(1);
        const div = document.createElement('div');
        div.className = 'file-item';
        const icon = file.type.startsWith('image/') ? 'fa-image' : 
                     file.type.includes('pdf') ? 'fa-file-pdf' :
                     file.type.includes('word') || file.type.includes('doc') ? 'fa-file-word' :
                     file.type.includes('excel') || file.type.includes('sheet') ? 'fa-file-excel' : 'fa-file';
        div.innerHTML = `
            <span class="file-icon"><i class="fas ${icon}"></i></span>
            <span class="file-name">${file.name}</span>
            <span class="file-size">(${size} KB)</span>
            <button type="button" class="remove-file" onclick="removeFile(${i})"><i class="fas fa-times-circle"></i></button>
        `;
        fileList.appendChild(div);
    }
}

function removeFile(index) {
    const dt = new DataTransfer();
    const files = fileInput.files;
    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }
    fileInput.files = dt.files;
    if (fileInput.files.length > 0) {
        updateFileList(fileInput.files);
        fileInput.required = false;
    } else {
        fileList.innerHTML = '';
        fileInput.required = true;
    }
}

function toggleAllDepartments(selectAll) {
    const checkboxes = document.querySelectorAll('input[name="recipient_depts[]"]');
    checkboxes.forEach(cb => {
        if (!cb.disabled) {
            cb.checked = selectAll.checked;
        }
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('input[name="recipient_depts[]"]:checked');
    document.getElementById('selectedCount').textContent = checkboxes.length + ' selected';
}

document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
});
</script>

