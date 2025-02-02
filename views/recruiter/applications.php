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

        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 2rem;
        }

        .search-box {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .applications-table {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .badge {
            font-weight: 500;
            padding: 0.5em 1em;
        }

        .loading-spinner {
            width: 1rem;
            height: 1rem;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="d-flex flex-column">
                    <h4 class="mb-4 px-3">Recruitment</h4>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
                        <a class="nav-link" href="new-application.php"><i class="bi bi-plus-circle"></i> New
                            Application</a>
                        <a class="nav-link active" href="#"><i class="bi bi-list-ul"></i> Applications</a>
                        <a class="nav-link" href="#" onclick="logout(); return false;"><i
                                class="bi bi-box-arrow-right"></i>
                            Logout</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
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