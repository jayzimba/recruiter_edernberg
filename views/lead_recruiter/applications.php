<?php
require_once __DIR__ . '/../../middleware/check_auth.php';
checkAuth(['lead_recruiter']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications - Lead Recruiter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <style>
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
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Include Sidebar -->
            <?php include '../../includes/lead_recruiter_sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Include Header -->
                <?php include '../../includes/lead_recruiter_header.php'; ?>

                <div class="search-box mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <h4 class="mb-0">All Applications</h4>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="searchInput"
                                    placeholder="Search by ID, email or contact...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" style="width: auto;" id="statusFilter"
                                onchange="filterByStatus(this.value)">
                                <option value="">All Statuses</option>
                                <option value="1">Pending</option>
                                <option value="2">Under Review</option>
                                <option value="3">Accepted</option>
                                <option value="4">Rejected</option>
                                <option value="5">Paid Up</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Program</th>
                                        <th>Recruiter</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="applicationsTable">
                                    <tr>
                                        <td colspan="6" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadApplications();

            // Set initial filter value from URL
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const search = urlParams.get('search');
            if (status) {
                document.getElementById('statusFilter').value = status;
            }
            if (search) {
                document.getElementById('searchInput').value = search;
            }

            // Add search functionality with debounce
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const currentUrl = new URL(window.location.href);
                    if (this.value) {
                        currentUrl.searchParams.set('search', this.value);
                    } else {
                        currentUrl.searchParams.delete('search');
                    }
                    window.location.href = currentUrl.toString();
                }, 500);
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

        function loadApplications() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const search = urlParams.get('search');
            let url = '../../api/applications/get_lead.php';

            const params = new URLSearchParams();
            if (status) {
                params.set('status', status);
            }
            if (search) {
                params.set('search', search);
            }

            if (params.toString()) {
                url += `?${params.toString()}`;
            }

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        updateApplicationsTable(data.data);
                    } else {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Failed to load applications');
                });
        }

        function updateApplicationsTable(applications) {
            const tbody = document.getElementById('applicationsTable');

            if (!applications.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center">No applications found</td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = applications.map(app => `
                <tr>
                    <td>${app.firstname} ${app.lastname}</td>
                    <td>${app.program_name}</td>
                    <td>${app.recruiter_firstname} ${app.recruiter_lastname}</td>
                    <td>
                        <span class="badge bg-${getStatusBadgeClass(app.status_name)}">
                            ${app.status_name}
                        </span>
                    </td>
                    <td>${formatDate(app.created_at)}</td>
                    <td>
                        <a href="view-application.php?id=${app.id}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> View
                        </a>
                    </td>
                </tr>
            `).join('');
        }

        function filterByStatus(status) {
            const currentUrl = new URL(window.location.href);
            if (status) {
                currentUrl.searchParams.set('status', status);
            } else {
                currentUrl.searchParams.delete('status');
            }
            window.location.href = currentUrl.toString();
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

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function showError(message) {
            document.getElementById('applicationsTable').innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        ${message}
                    </td>
                </tr>
            `;
        }
    </script>
</body>

</html>