<?php
require_once __DIR__ . '/../middleware/check_auth.php';
checkAuth(['lead_recruiter']);

// Get the current page name from the URL
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
.sidebar {
    min-height: 100vh;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    padding-top: 1rem;
    position: fixed;
    width: 250px;
    z-index: 1030;
    transition: all 0.3s ease;
}

.nav-link {
    padding: 0.8rem 1rem;
    color: #6c757d;
    border-radius: 5px;
    margin: 0.2rem 0;
    display: flex;
    align-items: center;
    text-decoration: none;
}

.nav-link:hover,
.nav-link.active {
    background-color: var(--primary-color);
    color: white;
}

.nav-link i {
    margin-right: 10px;
}

.sidebar-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1029;
}

.sidebar-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    color: #6c757d;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0.5rem;
    display: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.sidebar-close:hover {
    background-color: rgba(0, 0, 0, 0.05);
    color: var(--primary-color);
}

/* Mobile Responsive Styles */
@media (max-width: 768px) {
    .sidebar-close {
        display: flex;
    }
    
    .sidebar {
        transform: translateX(-100%);
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .sidebar-overlay.active {
        display: block;
    }

    .main-content {
        margin-left: 0 !important;
        width: 100% !important;
    }
}

/* Main content adjustment */
.main-content {
    margin-left: 250px;
    transition: all 0.3s ease;
}
</style>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<div class="col-md-3 col-lg-2 sidebar" id="sidebar">
    <button class="sidebar-close d-md-none" id="sidebarClose">
        <i class="bi bi-x-lg"></i>
    </button>
    <div class="d-flex flex-column">
        <h4 class="mb-4 px-3">Lead Recruiter</h4>
        <nav class="nav flex-column">
            <a class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>" 
               href="dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
            <a class="nav-link <?php echo $current_page === 'team.php' ? 'active' : ''; ?>" 
               href="team.php"><i class="bi bi-people"></i> Team Management</a>
            <a class="nav-link <?php echo $current_page === 'new-application.php' ? 'active' : ''; ?>" 
               href="new-application.php"><i class="bi bi-plus-circle"></i> New Application</a>
            <a class="nav-link <?php echo $current_page === 'applications.php' ? 'active' : ''; ?>" 
               href="applications.php"><i class="bi bi-list-ul"></i> All Applications</a>
            <a class="nav-link <?php echo $current_page === 'reports.php' ? 'active' : ''; ?>" 
               href="reports.php"><i class="bi bi-graph-up"></i> Reports</a>
            <a class="nav-link <?php echo $current_page === 'settings.php' ? 'active' : ''; ?>" 
               href="settings.php"><i class="bi bi-gear"></i> Settings</a>
            <a class="nav-link <?php echo $current_page === 'change-password.php' ? 'active' : ''; ?>" 
               href="change-password.php"><i class="bi bi-key"></i> Change Password</a>
            <a class="nav-link" href="#" onclick="logout()">
               <i class="bi bi-box-arrow-right"></i> Logout</a>
        </nav>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const menuToggle = document.getElementById('menuToggle');
    const sidebarClose = document.getElementById('sidebarClose');
    const overlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }

    // Menu toggle button click
    if (menuToggle) {
        menuToggle.addEventListener('click', toggleSidebar);
    }
    
    // Close button click
    if (sidebarClose) {
        sidebarClose.addEventListener('click', toggleSidebar);
    }
    
    // Overlay click
    if (overlay) {
        overlay.addEventListener('click', toggleSidebar);
    }

    // Close sidebar on window resize if in mobile view
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }
    });

    // Close sidebar when clicking a link on mobile
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                toggleSidebar();
            }
        });
    });
});

function logout() {
    fetch('../../api/logout.php')
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                localStorage.removeItem('jwt_token');
                window.location.href = '../login.php';
            }
        })
        .catch(error => {
            console.error('Logout failed:', error);
        });
}
</script> 