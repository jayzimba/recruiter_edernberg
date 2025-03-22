<?php
require_once __DIR__ . '/../../middleware/check_auth.php';
checkAuth(['lead_recruiter']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Lead Recruiter</title>
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

        .report-card {
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

        .chart-container {
            position: relative;
            margin-top: 2rem;
            height: 300px;
        }

        /* Add custom alert styling */
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
                    <h4 class="m-0">Recruitment Reports</h4>
                    <button class="btn btn-success" onclick="exportToExcel()">
                        <i class="bi bi-file-earmark-excel"></i> Export to Excel
                    </button>
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
                                    <h6 class="mb-0">Total Applications</h6>
                                    <h3 class="mb-0" id="totalApplications">0</h3>
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
                                    <h6 class="mb-0">Approved</h6>
                                    <h3 class="mb-0" id="approvedApplications">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon me-3">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Pending</h6>
                                    <h3 class="mb-0" id="pendingApplications">0</h3>
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
                                    <h6 class="mb-0">Rejected</h6>
                                    <h3 class="mb-0" id="rejectedApplications">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-card">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Date Range</label>
                            <input type="text" class="form-control" id="dateRange" name="dateRange">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Recruiter</label>
                            <select class="form-select" id="recruiter" name="recruiter">
                                <option value="">All Recruiters</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Program</label>
                            <select class="form-select" id="program" name="program">
                                <option value="">All Programs</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="1">Pending</option>
                                <option value="2">Under Review</option>
                                <option value="3">Approved</option>
                                <option value="4">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Paid Up</label>
                            <select class="form-select" id="paidUp" name="paidUp">
                                <option value="">All Status</option>
                                <option value="1">Paid Up</option>
                                <option value="2">Not Paid</option>
                               
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

                <!-- Report Table -->
                <div class="report-card position-relative">
                    <div class="loading-overlay">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="reportsTable">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Program</th>
                                    <th>Recruiter</th>
                                    <th>Application Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="reportsData">
                                <tr>
                                    <td colspan="6" class="text-center">No data available</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="report-card">
                            <h5>Applications by Status</h5>
                            <div class="chart-container">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="report-card">
                            <h5>Applications by Program</h5>
                            <div class="chart-container">
                                <canvas id="programChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

    <script>
        let statusChart, programChart;

        $(document).ready(function() {
            // Initialize date range picker
            $('#dateRange').daterangepicker({
                startDate: moment().subtract(30, 'days'),
                endDate: moment(),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            // Initialize Select2 for dropdowns
            $('#recruiter, #program').select2({
                width: '100%',
                placeholder: 'Select an option'
            });

            // Update status filter options to match database values
            $('#status').html(`
                <option value="">All Status</option>
                <option value="1">Pending</option>
                <option value="2">Under Review</option>
                <option value="3">Approved</option>
                <option value="4">Rejected</option>
            `);

            // Load initial data
            loadRecruiters();
            loadPrograms();
            initializeCharts();
            
            // Load all applications initially without filters
            loadReportData({
                dateRange: '',
                recruiter: '',
                program: '',
                status: '',
                paidUp: ''
            });

            // Handle filter changes
            $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
                applyFilters();
            });

            $('#recruiter, #program, #status, #paidUp').on('change', function() {
                applyFilters();
            });

            // Form submission (now just prevents default as we're using real-time filtering)
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                applyFilters();
            });
        });

        function applyFilters() {
            const filters = {
                dateRange: $('#dateRange').val(),
                recruiter: $('#recruiter').val(),
                program: $('#program').val(),
                status: $('#status').val(),
                paidUp: $('#paidUp').val()
            };
            loadReportData(filters);
        }

        function loadRecruiters() {
            fetch('../../api/lead_recruiter/get_recruiters.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        const select = $('#recruiter');
                        select.empty().append('<option value="">All Recruiters</option>');
                        data.data.forEach(recruiter => {
                            select.append(new Option(
                                `${recruiter.firstname} ${recruiter.lastname}`,
                                recruiter.id
                            ));
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading recruiters:', error);
                    showAlert('danger', 'Failed to load recruiters');
                });
        }

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

        function loadReportData(filters = {}) {
            $('.loading-overlay').css('display', 'flex');

            fetch('../../api/lead_recruiter/get_reports.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(filters)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Raw API response:', data);
                console.log('Sample row data:', data.data[0]);
                if (data.status) {
                    updateTable(data.data);
                    updateStatistics(data.statistics);
                    updateCharts(data.statistics);
                } else {
                    showAlert('danger', data.message || 'Failed to load report data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while loading the report data');
            })
            .finally(() => {
                $('.loading-overlay').css('display', 'none');
            });
        }

        function updateTable(data) {
            const tbody = $('#reportsData');
            if (!data || data.length === 0) {
                tbody.html('<tr><td colspan="6" class="text-center">No data available</td></tr>');
                return;
            }

            tbody.html(data.map(row => `
                <tr>
                    <td>${escapeHtml(row.student_name || '')}</td>
                    <td>${escapeHtml(row.program_name || '')}</td>
                    <td>${escapeHtml(row.recruiter_name || '')}</td>
                    <td>${formatDate(row.application_date)}</td>
                    <td>
                        <span class="badge bg-${getStatusColor(row.status)}">${getStatusLabel(row.status)}</span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewDetails(${row.id})">
                            <i class="bi bi-eye"></i> View
                        </button>
                    </td>
                </tr>
            `).join(''));
        }

        function updateStatistics(stats) {
            console.log('Updating statistics:', stats); // Debug log
            $('#totalApplications').text(stats.total || 0);
            $('#approvedApplications').text(stats['3'] || 0); // Approved
            $('#pendingApplications').text(stats['1'] || 0); // Pending
            $('#rejectedApplications').text(stats['4'] || 0); // Rejected
        }

        function initializeCharts() {
            // Status Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            statusChart = new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: ['Approved', 'Pending', 'Under Review', 'Rejected'],
                    datasets: [{
                        data: [0, 0, 0, 0],
                        backgroundColor: ['#198754', '#ffc107', '#0dcaf0', '#dc3545']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Program Chart
            const programCtx = document.getElementById('programChart').getContext('2d');
            programChart = new Chart(programCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Applications',
                        data: [],
                        backgroundColor: '#0d6efd'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        function updateCharts(stats) {
            console.log('Updating charts:', stats); // Debug log
            // Update Status Chart
            statusChart.data.labels = ['Approved', 'Pending', 'Under Review', 'Rejected'];
            statusChart.data.datasets[0].data = [
                stats['3'] || 0, // Approved
                stats['1'] || 0, // Pending
                stats['2'] || 0, // Under Review
                stats['4'] || 0  // Rejected
            ];
            statusChart.data.datasets[0].backgroundColor = ['#198754', '#ffc107', '#0dcaf0', '#dc3545'];
            statusChart.update();

            // Update Program Chart
            if (stats.programs && stats.programs.length > 0) {
                programChart.data.labels = stats.programs.map(p => p.name);
                programChart.data.datasets[0].data = stats.programs.map(p => p.count);
                programChart.update();
            }
        }

        function exportToExcel() {
            // Get the current table data instead of making a new API call
            const tableData = [];
            const rows = document.querySelectorAll('#reportsTable tbody tr');
            
            // If no data, show alert and return
            if (rows.length === 0 || (rows.length === 1 && rows[0].cells.length === 1)) {
                showAlert('warning', 'No data available to export');
                return;
            }

            // Show loading overlay
            $('.loading-overlay').css('display', 'flex');

            // Get current filters
            const filters = {
                dateRange: $('#dateRange').val(),
                recruiter: $('#recruiter').val(),
                program: $('#program').val(),
                status: $('#status').val(),
                export: true // Add flag for detailed export data
            };

            // Fetch detailed data for export
            fetch('../../api/lead_recruiter/get_reports.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(filters)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Export data:', data); // Debug log

                if (data.status && Array.isArray(data.data) && data.data.length > 0) {
                    // Transform data for Excel
                    const excelData = data.data.map(row => {
                        return {
                            'Student ID': row.student_id_number || '',
                            'Full Name': row.student_name || '',  
                            'Email': row.email || '',
                            'Contact': row.contact || '',
                            'Nationality': row.nationality || '',
                            'Government ID': row.G_ID || '',
                            'Program': row.program_name || '',
                            'Recruiter': row.recruiter_name || '',
                            'Application Date': row.created_at ? formatExcelDate(row.created_at) : '',
                            'Status': getStatusLabel(row.status) || '',
                            'Commencement Date': row.commencement_date ? formatExcelDate(row.commencement_date) : '',
                            'Last Updated': row.updated_at ? formatExcelDate(row.updated_at) : '',
                            'Study Mode': row.study_mode || '',
                            'Level': row.program_level || '',
                            'Paid Up': row.paid_up_description || getPaidUpStatus(row.paid_up) || 'Not Paid'
                        };
                    });

                    // Create worksheet from the transformed data
                    const ws = XLSX.utils.json_to_sheet(excelData);

                    // Set column widths
                    ws['!cols'] = [
                        { wch: 15 },  // Student ID
                        { wch: 30 },  // Full Name
                        { wch: 30 },  // Email
                        { wch: 15 },  // Contact
                        { wch: 20 },  // Nationality
                        { wch: 20 },  // Government ID
                        { wch: 30 },  // Program
                        { wch: 25 },  // Recruiter
                        { wch: 20 },  // Application Date
                        { wch: 15 },  // Status
                        { wch: 20 },  // Commencement Date
                        { wch: 20 },  // Last Updated
                        { wch: 15 },  // Study Mode
                        { wch: 15 },  // Level
                        { wch: 15 }   // Paid Up
                    ];

                    // Create workbook and append the worksheet
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Applications Report");

                    // Generate filename with current date and time
                    const fileName = `recruitment_report_${moment().format('YYYY-MM-DD_HH-mm')}.xlsx`;

                    // Save file
                    XLSX.writeFile(wb, fileName);
                    showAlert('success', 'Report exported successfully');
                } else {
                    showAlert('warning', 'No data available to export');
                }
            })
            .catch(error => {
                console.error('Export error:', error);
                showAlert('danger', 'Failed to export data');
            })
            .finally(() => {
                $('.loading-overlay').css('display', 'none');
            });
        }

        function resetFilters() {
            $('#filterForm')[0].reset();
            $('#recruiter, #program').val('').trigger('change');
            $('#dateRange').data('daterangepicker').setStartDate(moment().subtract(30, 'days'));
            $('#dateRange').data('daterangepicker').setEndDate(moment());
            
            // Load all data without filters
            loadReportData({
                dateRange: '',
                recruiter: '',
                program: '',
                status: ''
            });
        }

        function viewDetails(id) {
            window.location.href = `view-application.php?id=${id}`;
        }

        function formatDate(date) {
            return moment(date).format('MMM D, YYYY');
        }

        function getStatusLabel(status) {
            const labels = {
                '1': 'Pending',
                '2': 'Under Review',
                '3': 'Approved',
                '4': 'Rejected',
                '5': 'Paid Up'
            };
            return labels[status] || 'Unknown';
        }

        function getStatusColor(status) {
            const colors = {
                '1': 'warning',     // Pending
                '2': 'info',        // Under Review
                '3': 'success',     // Approved
                '4': 'danger',      // Rejected
                '5': 'secondary'      // Paid Up
            };
            return colors[status] || 'secondary';
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
            
            // Remove the alert after 3 seconds
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

        function formatExcelDate(dateString) {
            return moment(dateString).format('MMMM D, YYYY');
        }

        function getPaidUpStatus(status) {
            switch(status) {
                case '1':
                    return 'Paid';
                case '2':
                    return 'Not Paid';
                default:
                    return 'Not Paid';
            }
        }
    </script>
</body>
</html> 