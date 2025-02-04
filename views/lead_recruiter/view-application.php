<?php
require_once __DIR__ . '/../../middleware/check_auth.php';
require_once __DIR__ . '/../../config/database.php';
checkAuth(['lead_recruiter']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application</title>
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

        .detail-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .detail-row {
            margin-bottom: 1rem;
        }

        .detail-label {
            font-weight: 600;
            color: #6c757d;
        }

        .attachment-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .status-badge {
            font-size: 0.9rem;
            padding: 0.5em 1em;
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
                        <a class="nav-link" href="dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
                        <a class="nav-link" href="team.php"><i class="bi bi-people"></i> Team Management</a>
                        <a class="nav-link " href="applications.php"><i class="bi bi-list-ul"></i> All
                            Applications</a>
                        <a class="nav-link" href="reports.php"><i class="bi bi-graph-up"></i> Reports</a>
                        <a class="nav-link active" href="settings.php"><i class="bi bi-gear"></i> Settings</a>
                        <a class="nav-link" href="#" onclick="logout(); return false;"><i
                                class="bi bi-box-arrow-right"></i>
                            Logout</a>
                    </nav>
                </div>
            </div>
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Application Details</h4>
                    <div>
                        <div id="actionButtons">
                            <!-- Buttons will be loaded dynamically -->
                        </div>
                    </div>
                </div>

                <div id="applicationDetails">
                    <!-- Content will be loaded here -->
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading application details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get application ID from URL
            const urlParams = new URLSearchParams(window.location.search);
            const applicationId = urlParams.get('id');

            if (!applicationId) {
                window.location.href = 'applications.php';
                return;
            }

            loadApplicationDetails(applicationId);
        });

        function loadApplicationDetails(id) {
            fetch(`../../api/applications/get_all_application.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status && data.data) {
                        const app = data.data;
                        const details = document.getElementById('applicationDetails');
                        const actionButtons = document.getElementById('actionButtons');

                        // Only show Review Application button if status is not "Accepted" (status_id != 3)
                        actionButtons.innerHTML = app.status_id != 3 ? `
                            <button class="btn btn-primary me-2" onclick="showActionModal()">
                                <i class="bi bi-check-circle"></i> Review Application
                            </button>
                        ` : '';

                        details.innerHTML = `
                        <!-- Status Section -->
                        <div class="detail-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="section-title mb-0">Application Status</h5>
                            <div class="d-flex justify-content-end align-items-center gap-3">
                               
                                 ${app.pop && app.student_status =='Not Paid' ? `
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-sm btn-success" onclick="approvePOP(${app.id})">
                                        <i class="bi bi-check-circle"></i> Approve Payment
                                    </button>
                                </div>
                            ` : ''} <span class="badge status-badge ${getStatusBadgeClass(app.status_name)}">
                                    ${app.status_name}
                                </span>
                            </div>
                            </div>
                           
                        </div>

                        <!-- Recruiter Information -->
                        <div class="detail-card">
                            <h5 class="section-title">Recruiter Information</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="detail-row">
                                        <div class="detail-label">Recruiter Name</div>
                                        <div>${app.recruiter_firstname} ${app.recruiter_lastname}</div>
                                    </div>
                                    </div>
                                    <div class="col-md-4">
                                    <div class="detail-row">
                                        <div class="detail-label">Recruiter Email</div>
                                        <div>${app.recruiter_email}</div>
                                    </div>
                                    </div>
                                    ${app.pop ? `
                                <div class="mt-3 col-md-4">
                                    <small class="text-muted">Proof of Payment:</small>
                                    <a href="../../uploads/proof_of_payment/${app.pop}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="bi bi-eye"></i> View POP
                                    </a>
                                </div>
                            ` : ''}
                                    
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="detail-card">
                            <h5 class="section-title">Personal Information</h5>
                           <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-row">
                                        <div class="detail-label">Full Name</div>
                                        <div>${app.firstname} ${app.lastname}</div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Email</div>
                                        <div>${app.email}</div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Contact</div>
                                        <div>${app.contact}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-row">
                                        <div class="detail-label">Nationality</div>
                                        <div>${app.nationality}</div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">ID Number</div>
                                        <div>${app.G_ID}</div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Payment Status</div>
                                        <div>
                                            <span class="badge bg-${app.student_status === 'Not Paid' ? 'info' : 'success'}">
                                                ${app.student_status}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Program Information -->
                        <div class="detail-card">
                            <h5 class="section-title">Program Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-row">
                                        <div class="detail-label">Program</div>
                                        <div>${app.program_name}</div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Study Mode</div>
                                        <div>${app.mode_name}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-row">
                                        <div class="detail-label">Intake</div>
                                        <div>${app.intake_description}</div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Admission Type</div>
                                        <div>${app.admission_description}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div class="detail-card">
                            <h5 class="section-title">Documents</h5>
                            <div class="row" id="attachmentsContainer">
                                ${renderAttachments(app.attachments)}
                            </div>
                        </div>
                    `;
                    } else {
                        document.getElementById('applicationDetails').innerHTML = `
                        <div class="alert alert-danger">
                            Failed to load application details. ${data.message || ''}
                        </div>
                    `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('applicationDetails').innerHTML = `
                    <div class="alert alert-danger">
                        An error occurred while loading the application details.
                    </div>
                `;
                });
        }

        function getStatusBadgeClass(status) {
            switch (status) {
                case 'Pending':
                    return 'bg-warning';
                case 'Under Review':
                    return 'bg-warning';
                case 'Rejected':
                    return 'bg-danger';
                default:
                    return 'bg-success';
            }
        }

        function renderAttachments(attachments) {
            if (!attachments || attachments.length === 0) {
                return '<div class="col-12">No documents attached</div>';
            }

            return attachments.map(attachment => `
            <div class="col-md-6">
                <div class="attachment-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="detail-label">${attachment.description}</div>
                            <small class="text-muted">Uploaded: ${new Date(attachment.uploaded_at).toLocaleDateString()}</small>
                        </div>
                        <a href="../../uploads/student_documents/${attachment.uri}" 
                           class="btn btn-sm btn-primary" 
                           target="_blank">
                            <i class="bi bi-eye"></i> View
                        </a>
                    </div>
                </div>
            </div>
        `).join('');
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

        function showActionModal() {
            const modal = new bootstrap.Modal(document.getElementById('applicationActionModal'));
            modal.show();
        }

        function setButtonLoading(button, isLoading) {
            if (isLoading) {
                button.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting Decision...';
                button.disabled = true;
            } else {
                button.innerHTML = 'Submit Decision';
                button.disabled = false;
            }
        }

        function updateApplicationStatus() {
            const urlParams = new URLSearchParams(window.location.search);
            const applicationId = urlParams.get('id');
            const status = document.getElementById('applicationStatus').value;
            const feedback = document.getElementById('actionFeedback');

            if (!status) {
                feedback.innerHTML = `
                <div class="alert alert-danger">
                    Please select a decision
                </div>
            `;
                return;
            }

            const button = event.target;
            setButtonLoading(button, true);

            fetch('../../api/applications/update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        application_id: applicationId,
                        status: status,
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        feedback.innerHTML = `
                    <div class="alert alert-success">
                        ${data.message}
                    </div>
                `;
                        setTimeout(() => {
                            bootstrap.Modal.getInstance(document.getElementById('applicationActionModal'))
                                .hide();
                            loadApplicationDetails(applicationId);
                        }, 1500);
                    } else {
                        feedback.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.message}
                    </div>
                `;
                    }
                    setButtonLoading(button, false);
                })
                .catch(error => {
                    console.error('Error:', error);
                    feedback.innerHTML = `
                <div class="alert alert-danger">
                    An error occurred. Please try again.
                </div>
            `;
                    setButtonLoading(button, false);
                });
        }

        // Add this function to handle POP approval
        function approvePOP(applicationId) {
            fetch('../../api/applications/approve_payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        application_id: applicationId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        // Reload application details to show updated status
                        loadApplicationDetails(applicationId);
                        // Show success message
                        alert('Payment approved successfully');
                    } else {
                        alert(data.message || 'Failed to approve payment');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while approving payment');
                });
        }
    </script>

    <!-- Application Action Modal -->
    <div class="modal fade" id="applicationActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Review Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="applicationActionForm">
                        <div class="mb-3">
                            <label class="form-label required">Decision</label>
                            <select class="form-select" id="applicationStatus" required>
                                <option value="">Select decision...</option>
                                <option value="3">Accept Application</option>
                                <option value="4">Reject Application</option>
                            </select>
                        </div>

                    </form>
                    <div id="actionFeedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateApplicationStatus()">
                        Submit Decision
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
data.message
} < /div>
    `;
    setTimeout(() => {
    bootstrap.Modal.getInstance(document.getElementById('applicationActionModal'))
    .hide();
    loadApplicationDetails(applicationId);
    }, 1500);
    } else {
    feedback.innerHTML = ` < div class="alert alert-danger">
        $ {
        data.message
        } < /div>
            `;
            }
            setButtonLoading(button, false);
            })
            .catch(error => {
            console.error('Error:', error);
            feedback.innerHTML = ` < div class="alert alert-danger">
                An error occurred.Please
                try again. < /div>
                    `;
                    setButtonLoading(button, false);
                    });
                    }

                    // Add this function to handle POP approval
                    function approvePOP(applicationId) {
                    fetch('../../api/applications/approve_payment.php', {
                    method: 'POST',
                    headers: {
                    'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                    application_id: applicationId
                    })
                    })
                    .then(response => response.json())
                    .then(data => {
                    if (data.status) {
                    // Reload application details to show updated status
                    loadApplicationDetails(applicationId);
                    // Show success message
                    alert('Payment approved successfully');
                    } else {
                    alert(data.message || 'Failed to approve payment');
                    }
                    })
                    .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while approving payment');
                    });
                    }
                    </script>

                    <!-- Application Action Modal -->
                    <div class="modal fade" id="applicationActionModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Review Application</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="applicationActionForm">
                                        <div class="mb-3">
                                            <label class="form-label required">Decision</label>
                                            <select class="form-select" id="applicationStatus" required>
                                                <option value="">Select decision...</option>
                                                <option value="3">Accept Application</option>
                                                <option value="4">Reject Application</option>
                                            </select>
                                        </div>

                                    </form>
                                    <div id="actionFeedback"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" onclick="updateApplicationStatus()">
                                        Submit Decision
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </body>

                    </html>