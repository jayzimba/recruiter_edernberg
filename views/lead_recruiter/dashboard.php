<?php
require_once __DIR__ . '/../../middleware/check_auth.php';
checkAuth(['lead_recruiter']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Recruiter Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <style>
        /* Same styles as recruiter dashboard */
        .sidebar {
            min-height: 100vh;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            padding-top: 1rem;
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

        .team-member {
            padding: 1rem;
            border-radius: 10px;
            background: white;
            margin-bottom: 1rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .recent-applications {
            max-height: 400px;
            overflow-y: auto;
        }

        .application-row:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.05) !important;
            transition: background-color 0.2s ease;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="d-flex flex-column">
                    <h4 class="mb-4 px-3">Lead Recruiter</h4>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="#"><i class="bi bi-house-door"></i> Dashboard</a>
                        <a class="nav-link" href="team.php"><i class="bi bi-people"></i> Team Management</a>
                        <a class="nav-link" href="applications.php"><i class="bi bi-list-ul"></i> All Applications</a>
                        <a class="nav-link" href="reports.php"><i class="bi bi-graph-up"></i> Reports</a>
                        <a class="nav-link" href="settings.php"><i class="bi bi-gear"></i> Settings</a>
                        <a class="nav-link" href="change-password.php">
                            <i class="bi bi-key"></i> Change Password
                        </a>
                        <a class="nav-link" href="#" onclick="logout(); return false;"><i
                                class="bi bi-box-arrow-right"></i>
                            Logout</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header -->
                <div class="header mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="m-0">Lead Dashboard Overview</h4>
                    <div class="user-profile">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['user_email'], 0, 1)); ?>
                        </div>
                        <div>
                            <small class="text-muted">Welcome,</small>
                            <div class="fw-bold"><?php echo $_SESSION['user_email']; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-card">
                            <h5>Total Applications</h5>
                            <div class="stats-number" id="totalApplications">-</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-card">
                            <h5>Pending Review</h5>
                            <div class="stats-number" id="pendingApplications">-</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-card">
                            <h5>Approved</h5>
                            <div class="stats-number" id="approvedApplications">-</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-card">
                            <h5>Rejected</h5>
                            <div class="stats-number" id="rejectedApplications">-</div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Tables -->
                <div class="row g-4">
                    <!-- Recent Applications -->
                    <div class="col-md-8">
                        <div class="stats-card">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-3">Top (5) Applications</h5> <a href="applications.php"
                                    class="btn btn-sm btn-link">View All</a>
                            </div>


                            <div class="recent-applications">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Program</th>
                                                <th>Status</th>
                                                <th>Recruiter</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="recentApplicationsTable">
                                            <tr>
                                                <td colspan="5" class="text-center">Loading...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Recruiters -->
                    <div class="col-md-4">
                        <div class="stats-card">
                            <h5 class="mb-3">Top Recruiters</h5>
                            <div id="topRecruiters">
                                Loading...
                            </div>
                        </div>
                    </div>

                    <!-- Applications by Program -->
                    <div class="col-md-12">
                        <div class="stats-card">
                            <h5 class="mb-3">Applications by Program</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Program</th>
                                            <th>Applications</th>
                                        </tr>
                                    </thead>
                                    <tbody id="programStats">
                                        <tr>
                                            <td colspan="2" class="text-center">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
        });

        function loadDashboardData() {
            fetch('../../api/dashboard/lead_stats.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        updateDashboard(data.data);
                    } else {
                        console.error('Failed to load dashboard data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function updateDashboard(data) {
            // Update total applications
            document.getElementById('totalApplications').textContent = data.total_applications;

            // Update status counts
            const statusMap = data.applications_by_status.reduce((acc, curr) => {
                acc[curr.status.toLowerCase()] = curr.count;
                return acc;
            }, {});

            document.getElementById('pendingApplications').textContent = statusMap['pending'] || 0;
            document.getElementById('approvedApplications').textContent = statusMap['approved'] || 0;
            document.getElementById('rejectedApplications').textContent = statusMap['rejected'] || 0;

            // Update recent applications table with clickable rows
            const recentHtml = data.recent_applications.map((app, index) => `
                <tr class="application-row" onclick="viewApplication(${app.id})" style="cursor: pointer">
                    <td>
                        <span class="badge bg-secondary me-2">${index + 1}</span>
                        ${app.firstname} ${app.lastname}
                    </td>
                    <td>${app.program_name}</td>
                    <td><span class="badge bg-${getStatusBadgeClass(app.status)}">${app.status}</span></td>
                    <td>${app.recruiter_firstname} ${app.recruiter_lastname}</td>
                    <td>${formatDate(app.created_at)}</td>
                </tr>
            `).join('');
            document.getElementById('recentApplicationsTable').innerHTML = recentHtml;

            // Update top recruiters
            const recruitersHtml = data.top_recruiters.map((recruiter, index) => `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span class="badge bg-primary me-2">${index + 1}</span>
                        ${recruiter.firstname} ${recruiter.lastname}
                    </div>
                    <span class="badge bg-secondary">${recruiter.applications_count} apps</span>
                </div>
            `).join('');
            document.getElementById('topRecruiters').innerHTML = recruitersHtml;

            // Update program stats
            const programsHtml = data.applications_by_program.map(prog => `
                <tr>
                    <td>${prog.program_name}</td>
                    <td>${prog.count}</td>
                </tr>
            `).join('');
            document.getElementById('programStats').innerHTML = programsHtml;
        }

        function getStatusBadgeClass(status) {
            const statusClasses = {
                'Pending': 'warning',
                'Under Review': 'warning',
                'Approved': 'success',
                'Rejected': 'danger'
            };
            return statusClasses[status] || 'secondary';
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

        function viewApplication(id) {
            window.location.href = `view-application.php?id=${id}`;
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    </script>
</body>

</html>