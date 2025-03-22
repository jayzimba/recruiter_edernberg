<?php
require_once __DIR__ . '/../../middleware/check_auth.php';
checkAuth(['lead_recruiter']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leads Management - Lead Recruiter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 2rem;
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

        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .table th {
            background-color: var(--primary-color);
            color: white;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .badge-contacted {
            background-color: #198754;
            color: white;
        }

        .badge-not-contacted {
            background-color: #dc3545;
            color: white;
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

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .alert-dismissing {
            animation: slideOut 0.3s ease-in forwards;
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="m-0">Leads Management</h4>
                    <div>
                        <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addLeadModal">
                            <i class="bi bi-plus-circle"></i> Add Lead
                        </button>
                        <button class="btn btn-success" onclick="exportLeads()">
                            <i class="bi bi-file-earmark-excel"></i> Export Leads
                        </button>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
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
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon me-3">
                                    <i class="bi bi-x-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Not Contacted</h6>
                                    <h3 class="mb-0" id="notContactedLeads">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
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

                <!-- Today's Follow-ups -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-calendar-check"></i> Today's Follow-ups
                                </h5>
                                <span class="badge bg-light text-primary" id="todayFollowupsCount">0</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Time</th>
                                                <th>Lead Name</th>
                                                <th>Contact</th>
                                                <th>Program</th>
                                                <th>Notes</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="todayFollowupsTable">
                                            <tr>
                                                <td colspan="7" class="text-center">Loading follow-ups...</td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                        <div class="col-md-3">
                            <label class="form-label">Country</label>
                            <select class="form-select" id="country" name="country">
                                <option value="">All Countries</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Assigned Recruiter</label>
                            <select class="form-select" id="recruiterFilter" name="recruiter">
                                <option value="">All Recruiters</option>
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
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="tableSearch" placeholder="Search by email or contact...">
                        </div>
                    </div>
                    <!-- Add this before the filters section -->
                <div class="bulk-actions mb-3" style="display: none;">
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select" id="recruiterSelect" style="width: 200px;">
                            <option value="">Select Recruiter</option>
                            <!-- Will be populated dynamically -->
                        </select>
                        <button class="btn btn-primary" onclick="assignSelectedLeads()">
                            <i class="bi bi-person-check"></i> Assign Selected Leads
                        </button>
                        <button class="btn btn-secondary" onclick="clearSelection()">
                            <i class="bi bi-x-circle"></i> Clear Selection
                        </button>
                    </div>
                </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="leadsTable">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" class="form-check-input" id="selectAllLeads">
                                    </th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Country</th>
                                    <th>Program Interest</th>
                                    <th>Assigned To</th>
                                    <th>Date Added</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="leadsData">
                                <tr>
                                    <td colspan="8" class="text-center">No data available</td>
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

                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">Notes</h6>
                            <textarea class="form-control mb-3" id="leadNotes" rows="3" placeholder="Add notes about this lead..."></textarea>
                            <button class="btn btn-primary" onclick="saveNotes()">
                                <i class="bi bi-save"></i> Save Notes
                            </button>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">Schedule Follow-up</h6>
                            <form id="followupForm" class="mb-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Follow-up Date & Time</label>
                                        <input type="datetime-local" class="form-control" id="followupDateTime" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Follow-up Notes</label>
                                        <textarea class="form-control" id="followupNotes" rows="2" placeholder="Add details about the follow-up..." required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="btn btn-primary" onclick="scheduleFollowup()">
                                            <i class="bi bi-calendar-plus"></i> Schedule Follow-up
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Activities History</h6>
                            <div id="activitiesSection" class="activities-list" style="max-height: 200px; overflow-y: auto;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Scheduled Follow-ups</h6>
                            <div id="followupsSection" class="followups-list" style="max-height: 200px; overflow-y: auto;">
                            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

    <script>
        let currentLeadId = null;

        $(document).ready(function() {
            // Get first day of current year and today's date
            const startOfYear = moment().startOf('year');
            const today = moment();

            // Initialize date range picker with default dates
            $('#dateRange').daterangepicker({
                opens: 'left',
                autoUpdateInput: true, // Changed to true since we're setting default dates
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

            // Initialize Select2 for dropdowns
            $('#program, #country, #recruiterFilter').select2({
                width: '100%',
                placeholder: 'Select an option'
            });

            // Load initial data
            loadPrograms();
            loadCountries();
            loadRecruiters();
            loadLeadsData();

            // Event handlers
            $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                loadLeadsData();
            });

            $('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                loadLeadsData();
            });

            $('.filter-control').on('change', function() {
                loadLeadsData();
            });

            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                loadLeadsData();
            });

            // Load today's follow-ups
            loadTodayFollowups();

            // Refresh today's follow-ups every 5 minutes
            setInterval(loadTodayFollowups, 300000);

            // Add real-time search functionality
            $('#tableSearch').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                const rows = $('#leadsData tr').not('#noResultsRow');
                let hasVisibleRows = false;

                rows.each(function() {
                    const email = $(this).find('td:eq(1)').text().toLowerCase();
                    const contact = $(this).find('td:eq(2)').text().toLowerCase();
                    const matches = email.includes(searchTerm) || contact.includes(searchTerm);
                    $(this).toggle(matches);
                    if (matches) hasVisibleRows = true;
                });

                // Show "No results found" if no matches
                if (!hasVisibleRows && searchTerm !== '') {
                    if ($('#noResultsRow').length === 0) {
                        $('#leadsData').append('<tr id="noResultsRow"><td colspan="8" class="text-center">No matching results found</td></tr>');
                    }
                    $('#noResultsRow').show();
                } else {
                    $('#noResultsRow').hide();
                }
            });

            // Remove searchQuery from filters
            $('#searchQuery').closest('.col-md-3').remove();

            loadProgramOptions();
            loadSourceOptions();

            // Also add an event listener for when the modal is shown
            $('#addLeadModal').on('show.bs.modal', function () {
                loadSourceOptions(); // Reload sources when modal is opened
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

        function loadCountries() {
            // You can either load from an API or use a static list
            const countries = [
                "Kenya", "Uganda", "Tanzania", "Rwanda", "Ethiopia", "Somalia",
                "South Sudan", "Nigeria", "Ghana", "South Africa"
            ];
            const select = $('#country');
            select.empty().append('<option value="">All Countries</option>');
            countries.forEach(country => {
                select.append(new Option(country, country));
            });
        }

        function loadLeadsData() {
            $('.loading-overlay').css('display', 'flex');
            const filters = {
                searchQuery: $('#searchQuery').val(),
                dateRange: $('#dateRange').val(),
                program: $('#program').val(),
                contactStatus: $('#contactStatus').val(),
                country: $('#country').val(),
                recruiter: $('#recruiterFilter').val()
            };

            fetch('../../api/lead_recruiter/get_leads.php', {
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
                tbody.html('<tr><td colspan="10" class="text-center">No data available</td></tr>');
                return;
            }

            tbody.html(data.map(lead => `
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input lead-checkbox" 
                               value="${lead.id}" onchange="updateBulkActionsVisibility()">
                    </td>
                    <td>${escapeHtml(lead.name || '')}</td>
                    <td>${escapeHtml(lead.email || '')}</td>
                    <td>${escapeHtml(lead.contact || '')}</td>
                    <td>${escapeHtml(lead.country || '')}</td>
                    <td>${escapeHtml(lead.program_name || '')}</td>
                    <td>${escapeHtml(lead.recruiter_name || 'Unassigned')}</td>
                    <td>${formatDate(lead.created_at)}</td>
                    <td>
                        <span class="badge ${lead.contacted ? 'badge-contacted' : 'badge-not-contacted'}">
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
            $('#notContactedLeads').text(stats.not_contacted || 0);
            $('#convertedLeads').text(stats.converted || 0);
        }

        function viewLeadDetails(leadId) {
            currentLeadId = leadId;
            fetch(`../../api/lead_recruiter/get_lead_details.php?id=${leadId}`)
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
                        $('#modalStatus').html(`<span class="badge ${lead.contacted ? 'badge-contacted' : 'badge-not-contacted'}">
                            ${lead.contacted ? 'Contacted' : 'Not Contacted'}</span>`);
                        $('#modalSource').text(lead.source || 'N/A');

                        // Populate Notes
                        $('#leadNotes').val(lead.notes || '');

                        // Show activities if available
                        if (data.data.activities && data.data.activities.length > 0) {
                            const activitiesList = data.data.activities.map(activity => `
                                <div class="activity-item mb-2">
                                    <small class="text-muted">${formatDate(activity.created_at)}</small>
                                    <div><strong>${activity.user_name}</strong>: ${activity.description}</div>
                                </div>
                            `).join('');
                            $('#activitiesSection').html(activitiesList);
                        } else {
                            $('#activitiesSection').html('<p>No activities recorded</p>');
                        }

                        // Show followups if available
                        if (data.data.followups && data.data.followups.length > 0) {
                            const followupsList = data.data.followups.map(followup => `
                                <div class="followup-item mb-2">
                                    <div class="d-flex justify-content-between">
                                        <strong>${formatDate(followup.followup_date)}</strong>
                                        <span class="badge bg-${getFollowupStatusColor(followup.status)}">${followup.status}</span>
                                    </div>
                                    <div>By: ${followup.user_name}</div>
                                    <div>${followup.notes || ''}</div>
                                </div>
                            `).join('');
                            $('#followupsSection').html(followupsList);
                        } else {
                            $('#followupsSection').html('<p>No follow-ups scheduled</p>');
                        }

                        // Update Convert button state
                        $('#convertButton').prop('disabled', lead.converted === 1);
                        if (lead.converted === 1) {
                            $('#convertButton').text('Already Converted');
                        } else {
                            $('#convertButton').text('Convert to Application');
                        }

                        // Set minimum date time for follow-up to current date/time
                        const now = new Date();
                        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                        document.getElementById('followupDateTime').min = now.toISOString().slice(0, 16);

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

        function getFollowupStatusColor(status) {
            const colors = {
                'pending': 'warning',
                'completed': 'success',
                'cancelled': 'danger'
            };
            return colors[status] || 'secondary';
        }

        function saveNotes() {
            if (!currentLeadId) return;

            const notes = $('#leadNotes').val();
            fetch('../../api/lead_recruiter/update_lead_notes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        leadId: currentLeadId,
                        notes: notes
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.status) {
                        showAlert('success', 'Notes saved successfully');
                    } else {
                        showAlert('danger', 'Failed to save notes');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An error occurred while saving notes');
                });
        }

        function markAsContacted(leadId) {
            fetch('../../api/lead_recruiter/mark_contacted.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        leadId: leadId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        showAlert('success', 'Lead marked as contacted');
                        loadLeadsData();
                    } else {
                        showAlert('danger', 'Failed to update contact status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An error occurred while updating contact status');
                });
        }

        function convertToApplication() {
            if (!currentLeadId) return;

            fetch(`../../api/lead_recruiter/get_lead_details.php?id=${currentLeadId}`)
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
                            // Add program name for reference
                            programName: lead.program_name || '',
                            // Add flags for loading sequence
                            shouldLoadPrograms: true,
                            shouldSelectProgram: true,
                            // Add school name if available
                            schoolName: lead.school_name || ''
                        };

                        console.log('Storing application data:', applicationData); // Debug log

                        // Store the data in sessionStorage
                        sessionStorage.setItem('applicationData', JSON.stringify(applicationData));

                        // Redirect to new application page with both lead ID and program ID
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

        function exportLeads() {
            $('.loading-overlay').css('display', 'flex');
            const filters = {
                dateRange: $('#dateRange').val(),
                program: $('#program').val(),
                contactStatus: $('#contactStatus').val(),
                country: $('#country').val(),
                export: true
            };

            fetch('../../api/lead_recruiter/get_leads.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(filters)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status && Array.isArray(data.data) && data.data.length > 0) {
                        const ws = XLSX.utils.json_to_sheet(data.data);
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, "Leads Report");

                        // Auto-size columns
                        const colWidths = [];
                        data.data.forEach(row => {
                            Object.keys(row).forEach((key, i) => {
                                const value = String(row[key] || '');
                                colWidths[i] = Math.max(colWidths[i] || 0, value.length);
                            });
                        });
                        ws['!cols'] = colWidths.map(w => ({
                            wch: Math.min(w + 2, 50)
                        }));

                        // Generate filename with current date
                        const fileName = `leads_report_${moment().format('YYYY-MM-DD_HH-mm')}.xlsx`;
                        XLSX.writeFile(wb, fileName);
                        showAlert('success', 'Leads exported successfully');
                    } else {
                        showAlert('warning', 'No data available to export');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'Failed to export leads');
                })
                .finally(() => {
                    $('.loading-overlay').css('display', 'none');
                });
        }

        function resetFilters() {
            $('#filterForm')[0].reset();
            $('#program, #country').val('').trigger('change');
            $('#dateRange').val('');
            $('#searchQuery').val('');
            loadLeadsData();
        }

        function formatDate(date) {
            return moment(date).format('MMM D, YYYY');
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

        function scheduleFollowup() {
            if (!currentLeadId) {
                showAlert('danger', 'No lead selected');
                return;
            }

            const followupDateTime = $('#followupDateTime').val();
            const followupNotes = $('#followupNotes').val();

            if (!followupDateTime || !followupNotes) {
                showAlert('warning', 'Please fill in all follow-up details');
                return;
            }

            // Format the date from "YYYY-MM-DDTHH:mm" to "YYYY-MM-DD HH:mm"
            const formattedDate = followupDateTime.replace('T', ' ');

            // Show loading state
            const scheduleButton = document.querySelector('button[onclick="scheduleFollowup()"]');
            const originalText = scheduleButton.innerHTML;
            scheduleButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Scheduling...';
            scheduleButton.disabled = true;

            fetch('../../api/lead_recruiter/add_followup.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        leadId: currentLeadId,
                        followupDate: formattedDate,
                        notes: followupNotes
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        showAlert('success', 'Follow-up scheduled successfully');
                        // Clear form
                        $('#followupDateTime').val('');
                        $('#followupNotes').val('');
                        // Refresh lead details to show new follow-up
                        viewLeadDetails(currentLeadId);
                    } else {
                        throw new Error(data.message || 'Failed to schedule follow-up');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', error.message || 'An error occurred while scheduling follow-up');
                })
                .finally(() => {
                    // Restore button state
                    scheduleButton.innerHTML = originalText;
                    scheduleButton.disabled = false;
                });
        }

        function loadTodayFollowups() {
            fetch('../../api/lead_recruiter/get_today_followups.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        updateTodayFollowups(data.data);
                    } else {
                        showAlert('danger', 'Failed to load today\'s follow-ups');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An error occurred while loading follow-ups');
                });
        }

        function updateTodayFollowups(followups) {
            const tbody = $('#todayFollowupsTable');
            const count = $('#todayFollowupsCount');

            if (!followups || followups.length === 0) {
                tbody.html('<tr><td colspan="7" class="text-center">No follow-ups scheduled for today</td></tr>');
                count.text('0');
                return;
            }

            count.text(followups.length);

            tbody.html(followups.map(followup => `
                <tr>
                    <td>${moment(followup.followup_date).format('hh:mm A')}</td>
                    <td>
                        <a href="javascript:void(0)" onclick="viewLeadDetails(${followup.lead_id})">
                            ${escapeHtml(followup.lead_name)}
                        </a>
                    </td>
                    <td>${escapeHtml(followup.lead_contact || '')}</td>
                    <td>${escapeHtml(followup.program_name || '')}</td>
                    <td>${escapeHtml(followup.notes || '')}</td>
                    <td>
                        <span class="badge bg-${getFollowupStatusColor(followup.status)}">
                            ${followup.status.charAt(0).toUpperCase() + followup.status.slice(1)}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-success" onclick="updateFollowupStatus(${followup.id}, 'completed')" 
                                    ${followup.status === 'completed' ? 'disabled' : ''}>
                                <i class="bi bi-check-circle"></i>
                            </button>
                            <button class="btn btn-danger" onclick="updateFollowupStatus(${followup.id}, 'cancelled')"
                                    ${followup.status === 'cancelled' ? 'disabled' : ''}>
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join(''));
        }

        function updateFollowupStatus(followupId, status) {
            fetch('../../api/lead_recruiter/update_followup_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        followupId: followupId,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.status) {
                        showAlert('success', `Follow-up marked as ${status}`);
                        loadTodayFollowups(); // Refresh the list
                    } else {
                        showAlert('danger', 'Failed to update follow-up status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An error occurred while updating follow-up status');
                });
        }

        function loadProgramOptions() {
            fetch('../../api/lead_recruiter/get_programs.php')
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
            fetch('../../api/lead_recruiter/get_lead_sources.php')
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

        function loadRecruiters() {
            fetch('../../api/lead_recruiter/get_recruiters.php')
                .then(response => response.json())
                .then(data => {
                    console.log("recruiters fetched: "+data.data);

                    if (data.status) {
                        const recruiters = data.data;
                        const recruiterSelect = document.getElementById('recruiterSelect');
                        const recruiterFilter = document.getElementById('recruiterFilter');
                        
                        // Clear existing options
                        recruiterSelect.innerHTML = '<option value="">Select Recruiter</option>';
                        recruiterFilter.innerHTML = '<option value="">All Recruiters</option>';
                        
                        // Add recruiter options
                        recruiters.forEach(recruiter => {
                            const option = new Option(recruiter.name, recruiter.id);
                            recruiterSelect.add(option.cloneNode(true));
                            recruiterFilter.add(option);
                        });

                        // Initialize Select2 for recruiter filter
                        $('#recruiterFilter').select2({
                            width: '100%',
                            placeholder: 'Select Recruiter'
                        });
                    } else {
                        console.error('Failed to load recruiters:', data.message);
                        showAlert('danger', 'Failed to load recruiters');
                    }
                })
                .catch(error => {
                    console.error('Error loading recruiters:', error);
                    showAlert('danger', 'Failed to load recruiters');
                });
        }

        // Handle select all checkbox
        document.getElementById('selectAllLeads').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.lead-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            updateBulkActionsVisibility();
        });

        function updateBulkActionsVisibility() {
            const checkedBoxes = document.querySelectorAll('.lead-checkbox:checked');
            const bulkActions = document.querySelector('.bulk-actions');
            bulkActions.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
        }

        function clearSelection() {
            document.getElementById('selectAllLeads').checked = false;
            const checkboxes = document.querySelectorAll('.lead-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = false);
            updateBulkActionsVisibility();
        }

        function assignSelectedLeads() {
            const selectedLeads = Array.from(document.querySelectorAll('.lead-checkbox:checked'))
                .map(checkbox => checkbox.value);
            
            const recruiterId = document.getElementById('recruiterSelect').value;
            
            if (!recruiterId) {
                showAlert('warning', 'Please select a recruiter');
                return;
            }
            
            if (selectedLeads.length === 0) {
                showAlert('warning', 'Please select leads to assign');
                return;
            }

            // Show loading state
            const assignButton = document.querySelector('button[onclick="assignSelectedLeads()"]');
            const originalText = assignButton.innerHTML;
            assignButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Assigning...';
            assignButton.disabled = true;

            fetch('../../api/lead_recruiter/assign_leads.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    leads: selectedLeads,
                    recruiter_id: recruiterId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    showAlert('success', 'Leads assigned successfully');
                    clearSelection();
                    loadLeadsData();
                } else {
                    throw new Error(data.message || 'Failed to assign leads');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', error.message);
            })
            .finally(() => {
                assignButton.innerHTML = originalText;
                assignButton.disabled = false;
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