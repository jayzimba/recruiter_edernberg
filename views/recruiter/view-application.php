<?php
require_once __DIR__ . '/../../middleware/check_auth.php';
require_once __DIR__ . '/../../config/database.php';
checkAuth(['recruiter']);

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
                    <h4 class="m-0 d-md-block d-none">Onboarding</h4>


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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Application Details</h4>
                    <div>
                        <a href="applications.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Back to Applications
                        </a>
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
        if (!applicationId) {
            window.location.href = 'applications.php';
            return;
        }

        loadApplicationDetails(applicationId);
    });

    let currentApplicationData = null;

    function loadApplicationDetails(id) {
        fetch(`../../api/applications/get_application.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.status && data.data) {
                    currentApplicationData = data.data;
                    const app = data.data;
                    const details = document.getElementById('applicationDetails');

                    details.innerHTML = `
                        <!-- Status Section -->
                        <div class="detail-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="section-title mb-0">Application Status</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge status-badge ${getStatusBadgeClass(app.status_name)}">
                                        ${app.status_name}
                                    </span>
                                    ${app.status_id == 1 || app.status_id == 3 ? `
                                        <button class="btn btn-primary btn-sm" onclick="showPopUpload()">
                                            <i class="bi bi-upload"></i> Upload POP
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                            ${app.pop ? `
                                <div class="mt-3">
                                    <small class="text-muted">Proof of Payment:</small>
                                    <a href="../../uploads/proof_of_payment/${app.pop}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="bi bi-eye"></i> View POP
                                    </a>
                                </div>
                            ` : ''}
                        </div>

                        <!-- Personal Information -->
                        <div class="detail-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="section-title mb-0">Personal Information</h5>
                                <button class="btn btn-outline-primary btn-sm" onclick="showEditModal()">
                                    <i class="bi bi-pencil"></i> Edit Information
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-row">
                                        <div class="detail-label">Full Name</div>
                                        <div>${app.firstname} ${app.middlename || ''} ${app.lastname}</div>
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
                return 'bg-info';
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

    function showPopUpload() {
        const modal = new bootstrap.Modal(document.getElementById('proofOfPaymentModal'));
        modal.show();
    }

    function uploadPOP() {
        const form = document.getElementById('popUploadForm');
        const formData = new FormData(form);
        const uploadBtn = document.getElementById('uploadPopBtn');
        const feedback = document.getElementById('uploadFeedback');
        const urlParams = new URLSearchParams(window.location.search);
        const applicationId = urlParams.get('id');

        // Add application ID to form data
        formData.append('application_id', applicationId);

        // Show loading state
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Uploading...`;
        feedback.innerHTML = '';

        fetch('../../api/applications/upload_pop.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    feedback.innerHTML = `
                    <div class="alert alert-success">
                        ${data.message}
                    </div>
                `;
                    // Reload application details after successful upload
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('proofOfPaymentModal')).hide();
                        loadApplicationDetails(applicationId);
                    }, 1500);
                } else {
                    feedback.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.message}
                    </div>
                `;
                    uploadBtn.disabled = false;
                    uploadBtn.innerHTML = 'Upload';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                feedback.innerHTML = `
                <div class="alert alert-danger">
                    An error occurred while uploading. Please try again.
                </div>
            `;
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = 'Upload';
            });
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

    function updateApplicationStatus(applicationId, status) {
        const button = event.target;
        setButtonLoading(button, true);

        fetch('../../api/applications/update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    application_id: applicationId,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    showAlert('success', data.message);
                    // Reload the current page
                    window.location.reload();
                } else {
                    showAlert('danger', data.message);
                    setButtonLoading(button, false);
                }
            })
            .catch(error => {
                showAlert('danger', 'An error occurred. Please try again.');
                setButtonLoading(button, false);
            });
    }

    function showEditModal() {
        const app = currentApplicationData;
        if (!app) return;

        document.getElementById('editFirstname').value = app.firstname || '';
        document.getElementById('editMiddlename').value = app.middlename || '';
        document.getElementById('editLastname').value = app.lastname || '';
        document.getElementById('editEmail').value = app.email || '';
        document.getElementById('editContact').value = app.contact || '';
        document.getElementById('editGID').value = app.G_ID || '';
        document.getElementById('editNationality').value = app.nationality || '';

        const modal = new bootstrap.Modal(document.getElementById('editInformationModal'));
        modal.show();
    }

    function saveChanges() {
        const form = document.getElementById('editInformationForm');
        const formData = new FormData(form);
        const button = document.getElementById('saveChangesBtn');
        const feedback = document.getElementById('editFeedback');
        const urlParams = new URLSearchParams(window.location.search);
        const applicationId = urlParams.get('id');

        formData.append('application_id', applicationId);

        button.disabled = true;
        button.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Saving...`;
        feedback.innerHTML = '';

        const jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        fetch('../../api/applications/update_information.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(jsonData)
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
                    bootstrap.Modal.getInstance(document.getElementById('editInformationModal')).hide();
                    loadApplicationDetails(applicationId);
                    window.location.reload();
                }, 1500);
            } else {
                feedback.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.message}
                    </div>
                `;
                button.disabled = false;
                button.innerHTML = 'Save Changes';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            feedback.innerHTML = `
                <div class="alert alert-danger">
                    An error occurred while saving changes. Please try again.
                </div>
            `;
            button.disabled = false;
            button.innerHTML = 'Save Changes';
        });
    }
    </script>

    <!-- Add this modal HTML just before the closing body tag -->
    <div class="modal fade" id="proofOfPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Proof of Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="popUploadForm">
                        <div class="mb-3">
                            <label class="form-label required">Select File</label>
                            <input type="file" class="form-control" name="pop_file" accept=".pdf,.jpg,.jpeg,.png"
                                required>
                            <small class="text-muted">Accepted formats: PDF, JPG, PNG</small>
                        </div>
                    </form>
                    <div id="uploadFeedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="uploadPopBtn" onclick="uploadPOP()">
                        Upload
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this modal before the closing body tag -->
    <div class="modal fade" id="editInformationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Personal Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editInformationForm">
                        <div class="mb-3">
                            <label class="form-label required">First Name</label>
                            <input type="text" class="form-control" id="editFirstname" name="firstname" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="editMiddlename" name="middlename">
                            <small class="text-muted">Optional</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Last Name</label>
                            <input type="text" class="form-control" id="editLastname" name="lastname" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Contact</label>
                            <input type="text" class="form-control" id="editContact" name="contact" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">National ID/Passport</label>
                            <input type="text" class="form-control" id="editGID" name="G_ID" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Nationality</label>
                            <input type="text" class="form-control" id="editNationality" name="nationality" required>
                        </div>
                        <div id="editFeedback"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveChangesBtn" onclick="saveChanges()">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>