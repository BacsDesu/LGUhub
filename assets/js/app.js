/**
 * MCR v3 - global front-end behavior shared by every page:
 * sidebar toggle, outside-click to close sidebar, auto-hiding alerts,
 * and keyboard shortcuts.
 */

function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}

function setSidebarCollapsed(collapsed) {
    document.body.classList.toggle('sidebar-collapsed', collapsed);
    localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
    const btn = document.getElementById('sidebarCollapseBtn');
    if (btn) {
        btn.querySelector('i').className = collapsed ? 'fas fa-angles-right' : 'fas fa-angles-left';
        btn.title = collapsed ? 'Expand sidebar' : 'Collapse sidebar';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const collapseBtn = document.getElementById('sidebarCollapseBtn');
    if (collapseBtn) {
        setSidebarCollapsed(document.body.classList.contains('sidebar-collapsed'));
        collapseBtn.addEventListener('click', function () {
            setSidebarCollapsed(!document.body.classList.contains('sidebar-collapsed'));
        });
    }
});

document.addEventListener('click', function (event) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.querySelector('.menu-toggle');
    if (window.innerWidth <= 768 && sidebar && toggle) {
        if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
            sidebar.classList.remove('open');
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.style.display = 'none', 500);
        }, 5000);
    });
});

document.addEventListener('keydown', function (e) {
    if (e.ctrlKey && e.key === 'b') {
        e.preventDefault();
        const collapseBtn = document.getElementById('sidebarCollapseBtn');
        if (collapseBtn) setSidebarCollapsed(!document.body.classList.contains('sidebar-collapsed'));
    }
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        window.location.href = '?page=create_request';
    }
    if (e.ctrlKey && e.key === 'd') {
        e.preventDefault();
        window.location.href = '?page=dashboard';
    }
    if (e.ctrlKey && e.key === 'a') {
        e.preventDefault();
        window.location.href = '?page=documentation';
    }
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(modal => {
            modal.classList.remove('active');
        });
    }
});
