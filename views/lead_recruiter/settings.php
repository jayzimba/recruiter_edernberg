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
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .table td {
        vertical-align: middle;
    }

    .btn-action {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        padding: 1rem 1.5rem;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        color: var(--primary-color);
        border-bottom: 2px solid var(--primary-color);
        background: none;
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

    .modal-header .btn-close {
        color: white;
    }

    .required::after {
        content: "*";
        color: red;
        margin-left: 4px;
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

                <div class="settings-card">
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#schools">
                                <i class="bi bi-building me-2"></i>Schools
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#programs">
                                <i class="bi bi-journal-text me-2"></i>Programs
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Schools Tab -->
                        <div class="tab-pane fade show active" id="schools">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">Schools Management</h5>
                                <button class="btn btn-primary" onclick="showSchoolModal()">
                                    <i class="bi bi-plus-circle me-2"></i>Add School
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>School Name</th>
                                            <th class="text-end">Actions</th>
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
                                <h5 class="mb-0">Programs Management</h5>
                                <button class="btn btn-primary" onclick="showProgramModal()">
                                    <i class="bi bi-plus-circle me-2"></i>Add Program
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Program Name</th>
                                            <th>Study Mode</th>
                                            <th>School</th>
                                            <th>Duration</th>
                                            <th>Tuition Fee</th>
                                            <th class="text-end">Actions</th>
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
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="schoolForm">
                        <input type="hidden" id="schoolId">
                        <div class="mb-3">
                            <label for="schoolName" class="form-label required">School Name</label>
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
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="programForm">
                        <input type="hidden" id="programId">
                        <div class="mb-3">
                            <label for="programName" class="form-label required">Program Name</label>
                            <input type="text" class="form-control" id="programName" required>
                        </div>
                        <div class="mb-3">
                            <label for="programSchool" class="form-label required">School</label>
                            <select class="form-select" id="programSchool" required>
                                <option value="">Select School</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="programLevel" class="form-label required">Level</label>
                            <select class="form-select" id="programLevel" required>
                                <option value="">Select Level</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="programStudyMode" class="form-label required">Study Mode</label>
                            <select class="form-select" id="programStudyMode" required>
                                <option value="">Select Study Mode</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="programDuration" class="form-label required">Duration (years)</label>
                            <input type="number" class="form-control" id="programDuration" required min="1">
                        </div>
                        <div class="mb-3">
                            <label for="programFee" class="form-label required">Tuition Fee (ZMW)</label>
                            <input type="number" class="form-control" id="programFee" required min="0">
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        loadSchools();
        loadPrograms();
        loadLevels();
        loadStudyModes();
    });

    // Schools Functions
    function loadSchools() {
        fetch('../../api/settings/get_schools.php')
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    updateSchoolsTable(data.data);
                    updateSchoolDropdown(data.data);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function updateSchoolsTable(schools) {
        const tbody = document.getElementById('schoolsTable');
        if (!schools.length) {
            tbody.innerHTML =
                '<tr><td colspan="2" class="text-center">No schools found</td></tr>';
            return;
        }

        tbody.innerHTML = schools
            .map(
                school => `
        <tr>
            <td>${school.school_name}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-primary me-2" onclick="editSchool(${school.id})">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteSchool(${school.id})">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </td>
        </tr>
    `
            )
            .join('');
    }

    function updateSchoolDropdown(schools) {
        const select = document.getElementById('programSchool');
        select.innerHTML =
            '<option value="">Select School</option>' +
            schools
            .map(
                school => `<option value="${school.id}">${school.school_name}</option>`
            )
            .join('');
    }

    function showSchoolModal(isEdit = false, value = '', id = '') {
        document.getElementById('schoolModalTitle').textContent = isEdit ?
            'Edit School' :
            'Add School';

            document.getElementById('schoolForm').reset();

        if (isEdit) {
            document.getElementById('schoolName').value = value;
            document.getElementById('schoolId').value = id;
        }else{
            document.getElementById('schoolName').value = '';
            document.getElementById('schoolId').value = '';
        }


        new bootstrap.Modal(document.getElementById('schoolModal')).show();
    }

    function editSchool(id) {
        fetch(`../../api/settings/get_school.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.status) {

                    const school = data.data;
                    document.getElementById('schoolId').value = school.id;
                    document.getElementById('schoolName').value = school.school_name;
                    showSchoolModal(true, school.school_name, school.id);
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while fetching school details');
            });
    }

    function saveSchool() {
        const schoolData = {
            id: document.getElementById('schoolId').value,
            name: document.getElementById('schoolName').value,
        };

        if (!schoolData.name.trim()) {
            showAlert('danger', 'School name is required');
            return;
        }

        const submitButton = document.querySelector('#schoolModal .btn-primary');
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

        fetch('../../api/settings/save_school.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(schoolData),
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    bootstrap.Modal.getInstance(document.getElementById('schoolModal')).hide();
                    loadSchools();
                    showAlert('success', data.message);
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

    function deleteSchool(id) {
        if (
            confirm(
                'Are you sure you want to delete this school? This action cannot be undone.'
            )
        ) {
            const formData = new FormData();
            formData.append('id', id);

            fetch('../../api/settings/delete_school.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        loadSchools();
                        showAlert('success', data.message);
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

    // Programs Functions
    function loadPrograms() {
        fetch('../../api/settings/get_programs.php')
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    updateProgramsTable(data.data);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function loadStudyModes() {
        fetch('../../api/settings/get_study_modes.php')
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    //load the study modes into the study mode dropdown
                    const studyModeSelect = document.getElementById('programStudyMode');
                    studyModeSelect.innerHTML = '<option value="">Select Study Mode</option>' +
                        data.data.map(studyMode => `<option value="${studyMode.id}">${studyMode.mode_name}</option>`).join('');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    

    function updateProgramsTable(programs) {
        const tbody = document.getElementById('programsTable');
        if (!programs.length) {
            tbody.innerHTML =
                '<tr><td colspan="5" class="text-center">No programs found</td></tr>';
            return;
        }

        tbody.innerHTML = programs
            .map(
                program => `
        <tr>
            <td>${program.program_name}</td>
            <td>${program.mode_name}</td>   
            <td>${program.school_name}</td>
            <td>${program.duration} Years</td>
            <td>${formatCurrency (program.tuition_fee)}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary me-2" onclick="editProgram(${program.id})">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteProgram(${program.id})">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </td>
        </tr>
    `
            )
            .join('');
    }

    function showProgramModal(isEdit = false, program = null) {
        document.getElementById('programModalTitle').textContent = isEdit ? 'Edit Program' : 'Add Program';
        document.getElementById('programForm').reset();

        if (isEdit && program) {
            document.getElementById('programId').value = program.id;
            document.getElementById('programName').value = program.program_name;
            document.getElementById('programSchool').value = program.school_id;
            document.getElementById('programLevel').value = program.level_id;
            document.getElementById('programDuration').value = program.duration;
            document.getElementById('programFee').value = program.tuition_fee;
            document.getElementById('programStudyMode').value = program.study_mode_id;
        } else {
            document.getElementById('programId').value = '';
        }

        new bootstrap.Modal(document.getElementById('programModal')).show();
    }

    function editProgram(id) {
        fetch(`../../api/settings/get_program.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    const program = data.data;
                    showProgramModal(true, program);
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while fetching program details');
            });
    }

    function saveProgram() {
        const programData = {
            id: document.getElementById('programId').value,
            name: document.getElementById('programName').value,
            school_id: document.getElementById('programSchool').value,
            level_id: document.getElementById('programLevel').value,
            duration: document.getElementById('programDuration').value,
            tuition_fee: document.getElementById('programFee').value,
            study_mode_id: document.getElementById('programStudyMode').value,
        };

        fetch('../../api/settings/save_program.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(programData),
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    bootstrap.Modal
                        .getInstance(document.getElementById('programModal'))
                        .hide();
                    loadPrograms();
                    showAlert('success', data.message);
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while saving');
            });
    }

    function deleteProgram(id) {
        if (
            confirm(
                'Are you sure you want to delete this program? This action cannot be undone.'
            )
        ) {
            const formData = new FormData();
            formData.append('id', id);

            fetch('../../api/settings/delete_program.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        loadPrograms();
                        showAlert('success', data.message);
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

    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-ZM', {
            style: 'currency',
            currency: 'ZMW',
        }).format(amount);
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

    function logout() {
        fetch('../../api/logout.php')
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    window.location.href = '../login.php';
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function loadLevels() {
        fetch('../../api/settings/get_levels.php')
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.status) {
                    updateLevelsDropdown(data.data);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function updateLevelsDropdown(levels) {
        const select = document.getElementById('programLevel');
        select.innerHTML = '<option value="">Select Level</option>' +
            levels.map(level => 
                `<option value="${level.id}">${level.description}</option>`
            ).join('');
    }
    </script>
</body>

</html>