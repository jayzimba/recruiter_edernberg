<?php
require_once __DIR__ . '/../../middleware/check_auth.php';
checkAuth(['lead_recruiter']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management - Lead Recruiter</title>
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
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .team-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .search-box {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .recruiter-card {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #eee;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .recruiter-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .recruiter-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .required::after {
            content: "*";
            color: red;
            margin-left: 4px;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .btn-close {
            filter: brightness(0) invert(1);
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <?php include '../../includes/lead_recruiter_sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <?php include '../../includes/lead_recruiter_header.php'; ?>

                <!-- Header -->
                <div class="header mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="m-0">Team Management</h4>
                    <button class="btn btn-primary" onclick="showAddRecruiterModal()">
                        <i class="bi bi-plus-lg"></i> Add Recruiter
                    </button>
                </div>

                <!-- Search Box -->
                <div class="search-box">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" 
                                    placeholder="Search recruiters..." onkeyup="searchRecruiters()">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter" onchange="filterRecruiters()">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Recruiters Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>NRC Number</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="recruitersList">
                                    <tr>
                                        <td colspan="5" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Recruiter Modal -->
    <div class="modal fade" id="recruiterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Recruiter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="recruiterForm">
                        <input type="hidden" id="recruiterId">
                        <div class="mb-3">
                            <label for="firstName" class="form-label required">First Name</label>
                            <input type="text" class="form-control" id="firstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label required">Last Name</label>
                            <input type="text" class="form-control" id="lastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="nrc" class="form-label required">NRC Number</label>
                            <input type="text" class="form-control" id="nrc" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label required">Phone number</label>
                            <input type="text" class="form-control" id="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label required">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label required">Status</label>
                            <select class="form-select" id="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveRecruiter()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadRecruiters();
        });

        function loadRecruiters() {
            fetch('../../api/lead_recruiter/get_recruiters_team.php')
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.status) {
                        updateRecruitersList(data.data);
                    } else {
                        showAlert('danger', 'Failed to load recruiters');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An error occurred while loading recruiters');
                });
        }

        function updateRecruitersList(recruiters) {
            const container = document.getElementById('recruitersList');
            if (!recruiters || !recruiters.length) {
                container.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center">No recruiters found</td>
                    </tr>`;
                return;
            }

            container.innerHTML = recruiters.map(recruiter => `
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="recruiter-avatar">
                                ${recruiter.firstname.charAt(0).toUpperCase()}
                            </div>
                            <div>${recruiter.firstname} ${recruiter.lastname}</div>
                        </div>
                    </td>
                    <td>${recruiter.email}</td>
                    <td>${recruiter.nrc_number}</td>
                    <td>
                        <span class="status-badge ${recruiter.status === 1 ? 'status-active' : 'status-inactive'}">
                            ${recruiter.status === 1 ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="editRecruiter(${recruiter.id})">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteRecruiter(${recruiter.id})">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function searchRecruiters() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            const rows = document.querySelectorAll('#recruitersList tr');
            
            rows.forEach(row => {
                if (row.cells.length === 1) return; // Skip "No recruiters found" row
                
                const text = row.textContent.toLowerCase();
                const status = row.querySelector('.status-badge').textContent.toLowerCase();
                const matchesSearch = text.includes(searchTerm);
                const matchesStatus = !statusFilter || status.includes(statusFilter);
                
                row.style.display = matchesSearch && matchesStatus ? '' : 'none';
            });
        }

        function filterRecruiters() {
            searchRecruiters();
        }

        function showAddRecruiterModal() {
            document.getElementById('modalTitle').textContent = 'Add Recruiter';
            document.getElementById('recruiterForm').reset();
            document.getElementById('recruiterId').value = '';
            new bootstrap.Modal(document.getElementById('recruiterModal')).show();
        }

        function editRecruiter(id) {
            fetch(`../../api/lead_recruiter/get_recruiter.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        const recruiter = data.data;
                        document.getElementById('recruiterId').value = recruiter.id;
                        document.getElementById('firstName').value = recruiter.firstname;
                        document.getElementById('lastName').value = recruiter.lastname;
                        document.getElementById('nrc').value = recruiter.nrc_number;
                        document.getElementById('email').value = recruiter.email;
                        document.getElementById('phone').value = recruiter.phone_number;
                        document.getElementById('status').value = recruiter.status === 1 ? 'active' : 'inactive';
                        document.getElementById('modalTitle').textContent = 'Edit Recruiter';
                        new bootstrap.Modal(document.getElementById('recruiterModal')).show();
                    } else {
                        showAlert('danger', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An error occurred while fetching recruiter details');
                });
        }

        function saveRecruiter() {
            const form = document.getElementById('recruiterForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const recruiterId = document.getElementById('recruiterId').value;
            const recruiterData = {
                id: recruiterId,
                firstname: document.getElementById('firstName').value,
                lastname: document.getElementById('lastName').value,
                nrc_number: document.getElementById('nrc').value,
                phone: document.getElementById('phone').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                status: document.getElementById('status').value === 'active' ? 1 : 0
            };

            const submitButton = document.querySelector('#recruiterModal .btn-primary');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

            fetch('../../api/lead_recruiter/save_recruiter.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(recruiterData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        bootstrap.Modal.getInstance(document.getElementById('recruiterModal')).hide();
                        showAlert('success', data.message);
                        loadRecruiters();
                    } else {
                        showAlert('danger', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An error occurred while saving');
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Save';
                });
        }

        function deleteRecruiter(id) {
            if (confirm('Are you sure you want to delete this recruiter? This action cannot be undone.')) {
                fetch('../../api/lead_recruiter/delete_recruiter.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id: id
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            showAlert('success', data.message);
                            loadRecruiters();
                        } else {
                            showAlert('danger', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('danger', 'An error occurred while deleting');
                    });
            }
        }

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            setTimeout(() => alertDiv.remove(), 3000);
        }

      
    </script>
</body>

</html>