<?php
require_once __DIR__ . '/../../middleware/check_auth.php';
checkAuth(['recruiter', 'lead_recruiter']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            padding-top: 1rem;
            transition: transform 0.3s ease;
        }

        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 2rem;
        }

        .stat-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            margin-bottom: 1rem;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .nav-link {
            padding: 0.8rem 1rem;
            color: #6c757d;
            border-radius: 5px;
            margin: 0.2rem 0;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .nav-link i {
            margin-right: 10px;
        }

        .header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .recent-applications {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        /* Sidebar toggle button */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Close button for sidebar on mobile */
        .sidebar-close {
            display: none;
            /* Hidden by default */
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #6c757d;
            cursor: pointer;
            z-index: 1001;
            /* Ensure it's above the sidebar */
        }

        @media (max-width: 767.98px) {
            .sidebar-close {
                display: block;
                /* Show on mobile */
            }
        }

        @media (max-width: 767.98px) {
            .sidebar-toggle {
                display: block;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1000;
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar" id="sidebar">
                <div class="d-flex flex-column">
                    <!-- Close Button for Mobile -->
                    <button class="sidebar-close d-md-none" id="sidebarClose">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <h4 class="mb-4 px-3">Recruitment</h4>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
                        <a class="nav-link" href="new-application.php"><i class="bi bi-plus-circle"></i> New
                            Application</a>
                        <a class="nav-link active" href="applications.php"><i class="bi bi-list-ul"></i>
                            Applications</a>
                        <a class="nav-link" href="leads.php">
                            <i class="bi bi-people"></i> My Leads
                        </a>
                        <a class="nav-link" href="change-password.php">
                            <i class="bi bi-key"></i> Change Password
                        </a>
                        <a class="nav-link" href="#" onclick="logout(); return false;"><i
                                class="bi bi-box-arrow-right"></i> Logout</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header -->
                <div class="header mb-4 d-flex justify-content-between align-items-center">
                    <button class="sidebar-toggle d-md-none" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h4 class="m-0 d-md-block d-none">Dashboard Overview</h4>


                    <div class="user-profile">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['user_email'], 0, 1)); ?>
                        </div>
                        <div>
                            <!-- <small class="text-muted">Welcome,</small> -->
                            <div class="fw-bold"><?php echo $_SESSION['user_email']; ?></div>
                        </div>
                    </div>
                </div>
                <div class="search-box">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="searchInput"
                                    placeholder="Search by email or ID number...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="applications-table">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>ID Number</th>
                                    <th>Program</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="applicationsTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="loading-spinner"></div>
                                        Loading applications...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadApplications();
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');

            // Toggle sidebar on button click
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });

            // Close sidebar when clicking the close button
            sidebarClose.addEventListener('click', () => {
                sidebar.classList.remove('show');
            });

            // Close sidebar when clicking outside
            document.addEventListener('click', (event) => {
                if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            });

            // Add search functionality
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadApplications(e.target.value);
                }, 500);
            });
        });

        function loadApplications(search = '') {
            const tableBody = document.getElementById('applicationsTableBody');

            fetch(`../../api/applications/get.php${search ? `?search=${encodeURIComponent(search)}` : ''}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status && data.data) {
                        if (data.data.length === 0) {
                            tableBody.innerHTML = `
                                <tr>
                                    <td colspan="7" class="text-center">No applications found</td>
                                </tr>
                            `;
                            return;
                        }

                        tableBody.innerHTML = data.data.map(app => `
                            <tr>
                                <td>${app.firstname} ${app.lastname}</td>
                                <td>${app.email}</td>
                                <td>${app.contact}</td>
                                <td>${app.G_ID}</td>
                                <td>${app.program_name}</td>
                                <td>${new Date(app.created_at).toLocaleDateString()}</td>
                             <td>
    ${app.status_name === "Pending" 
        ? '<span class="badge bg-warning">Pending</span>' 
        : app.status_name === "Under Review" 
        ? '<span class="badge bg-warning">Under Review</span>' 
        : app.status_name === "Rejected" 
        ? '<span class="badge bg-danger">Rejected</span>' 
        : app.status_name === "Paid Up"
        ? '<span class="badge bg-secondary">Paid Up</span>'
        : '<span class="badge bg-success">Approved</span>'
    }
    </td>
                             
                                 <td>
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="viewApplication(${app.id})">
                                        View
                                    </button>
                                </td>
                           </tr>
                          `).join('');
                    } else {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="7" class="text-center text-danger">
                                    Failed to load applications
                                </td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center text-danger">
                                An error occurred while loading applications
                            </td>
                        </tr>
                    `;
                });
        }

        function viewApplication(id) {
            window.location.href = `view-application.php?id=${id}`;
        }

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
</body>

</html>