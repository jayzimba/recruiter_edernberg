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
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Team Members</h6>
                                        <h3 class="mb-0">12</h3>
                                    </div>
                                    <div class="text-primary">
                                        <i class="bi bi-people fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Total Applications</h6>
                                        <h3 class="mb-0">547</h3>
                                    </div>
                                    <div class="text-info">
                                        <i class="bi bi-file-text fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Approval Rate</h6>
                                        <h3 class="mb-0">76%</h3>
                                    </div>
                                    <div class="text-success">
                                        <i class="bi bi-graph-up fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Pending Review</h6>
                                        <h3 class="mb-0">23</h3>
                                    </div>
                                    <div class="text-warning">
                                        <i class="bi bi-clock-history fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Performance -->
                <div class="recent-applications mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="m-0">Team Performance</h5>
                        <a href="team.php" class="btn btn-sm btn-primary">Manage Team</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Recruiter</th>
                                    <th>Applications Processed</th>
                                    <th>Approval Rate</th>
                                    <th>Last Active</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Sarah Wilson</td>
                                    <td>45</td>
                                    <td>82%</td>
                                    <td>2 mins ago</td>
                                    <td><span class="badge bg-success">Online</span></td>
                                </tr>
                                <tr>
                                    <td>Mark Thompson</td>
                                    <td>38</td>
                                    <td>75%</td>
                                    <td>15 mins ago</td>
                                    <td><span class="badge bg-success">Online</span></td>
                                </tr>
                                <tr>
                                    <td>Emily Davis</td>
                                    <td>41</td>
                                    <td>79%</td>
                                    <td>1 hour ago</td>
                                    <td><span class="badge bg-secondary">Offline</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Applications -->
                <div class="recent-applications">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="m-0">Recent Applications</h5>
                        <a href="applications.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Applicant</th>
                                    <th>Program</th>
                                    <th>Recruiter</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John Doe</td>
                                    <td>Computer Science</td>
                                    <td>Sarah Wilson</td>
                                    <td><span class="badge bg-warning">Pending Review</span></td>
                                    <td><a href="#" class="btn btn-sm btn-outline-primary">Review</a></td>
                                </tr>
                                <tr>
                                    <td>Jane Smith</td>
                                    <td>Business Administration</td>
                                    <td>Mark Thompson</td>
                                    <td><span class="badge bg-success">Approved</span></td>
                                    <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
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