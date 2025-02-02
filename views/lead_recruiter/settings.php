<?php
require_once __DIR__ . '/../../middleware/check_auth.php';
checkAuth(['lead_recruiter']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Lead Recruiter</title>
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

        .settings-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
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
                        <a class="nav-link" href="applications.php"><i class="bi bi-list-ul"></i> All Applications</a>
                        <a class="nav-link" href="reports.php"><i class="bi bi-graph-up"></i> Reports</a>
                        <a class="nav-link active" href="#"><i class="bi bi-gear"></i> Settings</a>
                        <a class="nav-link" href="#" onclick="logout(); return false;"><i
                                class="bi bi-box-arrow-right"></i> Logout</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="settings-card">
                    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="schools-tab" data-bs-toggle="tab"
                                data-bs-target="#schools" type="button">
                                All Schools
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="programs-tab" data-bs-toggle="tab" data-bs-target="#programs"
                                type="button">
                                Available Programs
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mt-4" id="settingsTabContent">
                        <!-- Schools Tab -->
                        <div class="tab-pane fade show active" id="schools">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5>Schools Management</h5>
                                <button class="btn btn-primary" onclick="showSchoolModal()">
                                    <i class="bi bi-plus-circle"></i> Add School
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>School Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="schoolsTable">
                                        <tr>
                                            <td colspan="2" class="text-center">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Programs Tab -->
                        <div class="tab-pane fade" id="programs">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5>Programs Management</h5>
                                <button class="btn btn-primary" onclick="showProgramModal()">
                                    <i class="bi bi-plus-circle"></i> Add Program
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Program Name</th>
                                            <th>School</th>
                                            <th>Duration</th>
                                            <th>Tuition Fee</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="programsTable">
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
    </div>

    <!-- School Modal -->
    <div class="modal fade" id="schoolModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="schoolModalTitle">Add School</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="schoolForm">
                        <input type="hidden" id="schoolId">
                        <div class="mb-3">
                            <label class="form-label required">School Name</label>
                            <input type="text" class="form-control" id="schoolName" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveSchool()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Program Modal -->
    <div class="modal fade" id="programModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="programModalTitle">Add Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="programForm">
                        <input type="hidden" id="programId">
                        <div class="mb-3">
                            <label class="form-label required">Program Name</label>
                            <input type="text" class="form-control" id="programName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">School</label>
                            <select class="form-select" id="programSchool" required>
                                <option value="">Select School</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Duration (months)</label>
                            <input type="number" class="form-control" id="programDuration" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Tuition Fee</label>
                            <input type="number" class="form-control" id="programFee" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveProgram()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/settings.js"></script>
</body>

</html>