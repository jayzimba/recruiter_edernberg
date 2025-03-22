<?php
require_once __DIR__ . '/../../middleware/check_auth.php';
checkAuth(['recruiter']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Leads - Recruiter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
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

        .filter-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .leads-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            border: none;
            animation: slideIn 0.3s ease-out;
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
                        <a class="nav-link" href="applications.php"><i class="bi bi-list-ul"></i>
                            Applications</a>
                        <a class="nav-link active" href="leads.php">
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

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="m-0">My Leads</h4>
                    <div>
                        <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addLeadModal">
                            <i class="bi bi-plus-circle"></i> Add Lead
                        </button>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon me-3">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Total Leads</h6>
                                    <h3 class="mb-0" id="totalLeads">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon me-3">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Contacted</h6>
                                    <h3 class="mb-0" id="contactedLeads">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon me-3">
                                    <i class="bi bi-arrow-right-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Converted</h6>
                                    <h3 class="mb-0" id="convertedLeads">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-card">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Date Range</label>
                            <input type="text" class="form-control" id="dateRange" name="dateRange">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Program</label>
                            <select class="form-select" id="program" name="program">
                                <option value="">All Programs</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Contact Status</label>
                            <select class="form-select" id="contactStatus" name="contactStatus">
                                <option value="">All Status</option>
                                <option value="1">Contacted</option>
                                <option value="0">Not Contacted</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-filter"></i> Apply Filters
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Leads Table -->
                <div class="leads-card position-relative">
                    <div class="loading-overlay">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="leadsTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Program Interest</th>
                                    <th>Date Added</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="leadsData">
                                <tr>
                                    <td colspan="7" class="text-center">Loading leads...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lead Details Modal -->
    <div class="modal fade" id="leadDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lead Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Personal Information</h6>
                            <p><strong>Name:</strong> <span id="modalName"></span></p>
                            <p><strong>Email:</strong> <span id="modalEmail"></span></p>
                            <p><strong>Contact:</strong> <span id="modalContact"></span></p>
                            <p><strong>Country:</strong> <span id="modalCountry"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Lead Information</h6>
                            <p><strong>Program Interest:</strong> <span id="modalProgram"></span></p>
                            <p><strong>Date Added:</strong> <span id="modalDate"></span></p>
                            <p><strong>Contact Status:</strong> <span id="modalStatus"></span></p>
                            <p><strong>Source:</strong> <span id="modalSource"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="convertButton" onclick="convertToApplication()">
                        Convert to Application
                    </button>
                </div>
            </div>
        </div>
    </div>

        <!-- Add Lead Modal -->
        <div class="modal fade" id="addLeadModal" tabindex="-1" aria-labelledby="addLeadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLeadModalLabel">Add New Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addLeadForm">
                        <div class="mb-3">
                            <label for="leadName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="leadName" required>
                        </div>
                        <div class="mb-3">
                            <label for="leadEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="leadEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="leadContact" class="form-label">Contact</label>
                            <input type="tel" class="form-control" id="leadContact" required>
                        </div>
                        <div class="mb-3">
                            <label for="leadSource" class="form-label">Source</label>
                            <select class="form-select" id="leadSource" required>
                                <option value="">Select Source</option>
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="leadProgram" class="form-label">Program</label>
                            <select class="form-select" id="leadProgram" required>
                                <option value="">Select Program</option>
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Lead</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add necessary scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        let currentLeadId = null;

        $(document).ready(function() {

            loadProgramOptions();
            loadSourceOptions();

            // Also add an event listener for when the modal is shown
            $('#addLeadModal').on('show.bs.modal', function () {
                loadSourceOptions(); // Reload sources when modal is opened
            });
            // Initialize date range picker with default dates
            const startOfYear = moment().startOf('year');
            const today = moment();

            $('#dateRange').daterangepicker({
                opens: 'left',
                autoUpdateInput: true,
                startDate: startOfYear,
                endDate: today,
                locale: {
                    format: 'MM/DD/YYYY',
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment()]
                }
            });

            // Set initial value in the input
            $('#dateRange').val(startOfYear.format('MM/DD/YYYY') + ' - ' + today.format('MM/DD/YYYY'));

            // Initialize Select2 for all select elements in the filter form
            $('#filterForm select').select2({
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder') || 'Select an option';
                }
            });

            // Load initial data
            loadPrograms();
            loadLeadsData();
            loadTodayFollowups();

            // Event handlers
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                loadLeadsData();
            });

           
        });

        function loadPrograms() {
            fetch('../../api/lead_recruiter/get_programs.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        const select = $('#program');
                        select.empty().append('<option value="">All Programs</option>');
                        data.data.forEach(program => {
                            select.append(new Option(program.name, program.id));
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading programs:', error);
                    showAlert('danger', 'Failed to load programs');
                });
        }

        function loadLeadsData() {
            $('.loading-overlay').css('display', 'flex');
            const filters = {
                dateRange: $('#dateRange').val(),
                program: $('#program').val(),
                contactStatus: $('#contactStatus').val()
            };

            fetch('../../api/recruiter/get_my_leads.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(filters)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    updateLeadsTable(data.data);
                    updateLeadsStatistics(data.statistics);
                } else {
                    showAlert('danger', data.message || 'Failed to load leads data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while loading the leads data');
            })
            .finally(() => {
                $('.loading-overlay').css('display', 'none');
            });
        }

        function updateLeadsTable(data) {
            const tbody = $('#leadsData');
            if (!data || data.length === 0) {
                tbody.html('<tr><td colspan="7" class="text-center">No data available</td></tr>');
                return;
            }

            tbody.html(data.map(lead => `
                <tr>
                    <td>${escapeHtml(lead.name || '')}</td>
                    <td>${escapeHtml(lead.email || '')}</td>
                    <td>${escapeHtml(lead.contact || '')}</td>
                    <td>${escapeHtml(lead.program_name || '')}</td>
                    <td>${formatDate(lead.created_at)}</td>
                    <td>
                        <span class="badge ${lead.contacted ? 'bg-success' : 'bg-danger'}">
                            ${lead.contacted ? 'Contacted' : 'Not Contacted'}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewLeadDetails(${lead.id})">
                            <i class="bi bi-eye"></i> View
                        </button>
                        <button class="btn btn-sm btn-success" onclick="markAsContacted(${lead.id})" 
                                ${lead.contacted ? 'disabled' : ''}>
                            <i class="bi bi-check2"></i> ${lead.contacted ? 'Already Contacted' : 'Mark Contacted'}
                        </button>
                    </td>
                </tr>
            `).join(''));
        }

        function updateLeadsStatistics(stats) {
            $('#totalLeads').text(stats.total || 0);
            $('#contactedLeads').text(stats.contacted || 0);
            $('#convertedLeads').text(stats.converted || 0);
        }


        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show custom-alert`;
            alertDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi ${getAlertIcon(type)} me-2"></i>
                    <div class="flex-grow-1">${message}</div>
                    <button type="button" class="btn-close ms-3" onclick="dismissAlert(this)"></button>
                </div>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                if (alertDiv.parentElement) {
                    alertDiv.classList.add('alert-dismissing');
                    setTimeout(() => alertDiv.remove(), 300);
                }
            }, 3000);
        }

        function dismissAlert(button) {
            const alertDiv = button.closest('.custom-alert');
            alertDiv.classList.add('alert-dismissing');
            setTimeout(() => alertDiv.remove(), 300);
        }

        function getAlertIcon(type) {
            const icons = {
                'success': 'bi-check-circle-fill',
                'danger': 'bi-exclamation-circle-fill',
                'warning': 'bi-exclamation-triangle-fill',
                'info': 'bi-info-circle-fill'
            };
            return icons[type] || 'bi-info-circle-fill';
        }

        function escapeHtml(unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return String(unsafe)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function formatDate(date) {
            return moment(date).format('MMM D, YYYY');
        }

        function resetFilters() {
            $('#filterForm')[0].reset();
            $('#program').val('').trigger('change');
            $('#dateRange').val('');
            loadLeadsData();
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

        function viewLeadDetails(leadId) {
            currentLeadId = leadId;
            fetch(`../../api/recruiter/get_lead_details.php?id=${leadId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        const lead = data.data.lead;
                        
                        // Populate Personal Information
                        $('#modalName').text(lead.name || 'N/A');
                        $('#modalEmail').text(lead.email || 'N/A');
                        $('#modalContact').text(lead.contact || 'N/A');
                        $('#modalCountry').text(lead.country || 'N/A');
                        
                        // Populate Lead Information
                        $('#modalProgram').text(lead.program_name || 'N/A');
                        $('#modalDate').text(formatDate(lead.created_at) || 'N/A');
                        $('#modalStatus').html(`<span class="badge ${lead.contacted ? 'bg-success' : 'bg-danger'}">
                            ${lead.contacted ? 'Contacted' : 'Not Contacted'}</span>`);
                        $('#modalSource').text(lead.source || 'N/A');
                        
                        // Update Convert button state
                        $('#convertButton').prop('disabled', lead.converted === 1);
                        if (lead.converted === 1) {
                            $('#convertButton').text('Already Converted');
                        } else {
                            $('#convertButton').text('Convert to Application');
                        }
                        
                        const modal = new bootstrap.Modal(document.getElementById('leadDetailsModal'));
                        modal.show();
                    } else {
                        showAlert('danger', 'Failed to load lead details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An error occurred while loading lead details');
                });
        }

        function convertToApplication() {
            if (!currentLeadId) return;

            fetch(`../../api/recruiter/get_lead_details.php?id=${currentLeadId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        const lead = data.data.lead;
                        
                        // Split the full name into first and last name
                        const nameParts = (lead.name || '').split(' ');
                        const firstName = nameParts[0] || '';
                        const lastName = nameParts.slice(1).join(' ') || '';
                        
                        // Prepare the lead data for the application form
                        const applicationData = {
                            firstName: firstName,
                            lastName: lastName,
                            email: lead.email || '',
                            contact: lead.contact || '',
                            nationality: lead.country || '',
                            schoolId: lead.school_id || '',
                            programId: lead.program_id || '',
                            leadId: lead.id,
                            programName: lead.program_name || '',
                            shouldLoadPrograms: true,
                            shouldSelectProgram: true,
                            schoolName: lead.school_name || ''
                        };

                        console.log('Storing application data:', applicationData);
                        
                        // Store the data in sessionStorage
                        sessionStorage.setItem('applicationData', JSON.stringify(applicationData));
                        
                        // Redirect to new application page
                        window.location.href = `new-application.php?from_lead=${currentLeadId}&program_id=${lead.program_id}`;
                    } else {
                        showAlert('danger', 'Failed to get lead details for conversion');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An error occurred while converting lead');
                });
        }

        function loadProgramOptions() {
            fetch('../../api/recruiter/get_programs.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        const select = document.getElementById('leadProgram');
                        select.innerHTML = '<option value="">Select Program</option>';
                        data.data.forEach(program => {
                            select.add(new Option(program.name, program.id));
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading programs:', error);
                    showAlert('danger', 'Failed to load programs');
                });
        }

        function loadSourceOptions() {
            fetch('../../api/recruiter/get_lead_sources.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        const select = document.getElementById('leadSource');
                        select.innerHTML = '<option value="">Select Source</option>';
                        data.data.forEach(source => {
                            const option = new Option(source.name, source.id);
                            select.add(option);
                        });
                    } else {
                        console.error('Failed to load sources:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error loading sources:', error);
                    showAlert('danger', 'Failed to load sources');
                });
        }

        document.getElementById('addLeadForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const contact = document.getElementById('leadContact').value;
            const countryCodePattern = /^\+\d+/;

            if (!countryCodePattern.test(contact)) {
                showAlert('warning', 'Please include a country code in the contact number (e.g., +254)');
                return;
            }

            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
            submitButton.disabled = true;

            const leadData = {
                name: document.getElementById('leadName').value.trim(),
                email: document.getElementById('leadEmail').value.trim(),
                contact: contact.trim(),
                source_id: document.getElementById('leadSource').value,
                program_id: document.getElementById('leadProgram').value,
                country: 'Kenya' // Default country or you can add a country select
            };

            // Validate all required fields
            if (!leadData.name || !leadData.email || !leadData.contact || !leadData.source_id || !leadData.program_id) {
                showAlert('warning', 'Please fill in all required fields');
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
                return;
            }

            fetch('../../api/lead_recruiter/add_lead.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(leadData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    showAlert('success', 'Lead added successfully');
                    $('#addLeadModal').modal('hide');
                    document.getElementById('addLeadForm').reset();
                    loadLeadsData(); // Refresh the leads table
                } else {
                    throw new Error(data.message || 'Failed to add lead');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', error.message || 'An error occurred while adding the lead');
            })
            .finally(() => {
                // Restore button state
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        });
    </script>
</body>
</html> 