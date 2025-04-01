<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$page = 'transcripts';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header">
                    <h2>Academic Transcript</h2>
                    <p class="text-muted">View your academic performance and records</p>
                </div>
            </div>
        </div>

        <!-- Student Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="student-info-card">
                    <div class="student-info">
                        <div class="info-item">
                            <span class="label">Student ID</span>
                            <span class="value">STU2024001</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Program</span>
                            <span class="value">Bachelor of Computer Science</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Current Year</span>
                            <span class="value">2nd Year</span>
                        </div>
                    </div>
                    <div class="info-actions">
                        <button class="btn btn-outline-primary">
                            <i class="fas fa-download"></i> Download PDF
                        </button>
                        <button class="btn btn-outline-primary">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="summary-content">
                        <h6>CGPA</h6>
                        <h3>3.75</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="summary-content">
                        <h6>Current GPA</h6>
                        <h3>3.82</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="summary-content">
                        <h6>Total Credits</h6>
                        <h3>45</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="summary-content">
                        <h6>Completed Courses</h6>
                        <h3>15</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transcript Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Course History</h5>
                            <div class="semester-filter">
                                <select class="form-select" onchange="filterSemester(this.value)">
                                    <option value="all">All Semesters</option>
                                    <option value="2023-2">2023-2024 2nd Semester</option>
                                    <option value="2023-1">2023-2024 1st Semester</option>
                                    <option value="2022-2">2022-2023 2nd Semester</option>
                                    <option value="2022-1">2022-2023 1st Semester</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Semester Section -->
                        <div class="semester-section">
                            <div class="semester-header">
                                <h6>2023-2024 2nd Semester</h6>
                                <span class="semester-gpa">GPA: 3.82</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course Name</th>
                                            <th>Grade</th>
                                            <th>Grade Points</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>CSC 201</td>
                                            <td>Data Structures</td>
                                            <td>A</td>
                                            <td>4.00</td>
                                            <td><span class="status-badge completed"><i class="fas fa-check-circle"></i> Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>CSC 203</td>
                                            <td>Database Systems</td>
                                            <td>A-</td>
                                            <td>3.67</td>
                                            <td><span class="status-badge completed"><i class="fas fa-check-circle"></i> Completed</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Previous Semester -->
                        <div class="semester-section">
                            <div class="semester-header">
                                <h6>2023-2024 1st Semester</h6>
                                <span class="semester-gpa">GPA: 3.75</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course Name</th>
                                            <th>Grade</th>
                                            <th>Grade Points</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>CSC 101</td>
                                            <td>Introduction to Programming</td>
                                            <td>A</td>
                                            <td>4.00</td>
                                            <td><span class="status-badge completed"><i class="fas fa-check-circle"></i> Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>CSC 102</td>
                                            <td>Computer Architecture</td>
                                            <td>A-</td>
                                            <td>3.67</td>
                                            <td><span class="status-badge completed"><i class="fas fa-check-circle"></i> Completed</span></td>
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
</div>

<style>
.page-header {
    margin-bottom: 2rem;
}

.page-header h2 {
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.student-info-card {
    background: #fff;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid #eee;
}

.student-info {
    display: flex;
    gap: 2rem;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-item .label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.info-item .value {
    font-weight: 600;
    color: #2c3e50;
}

.info-actions {
    display: flex;
    gap: 1rem;
}

.summary-card {
    background: #fff;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid #eee;
}

.summary-icon {
    width: 50px;
    height: 50px;
    background: #f8f9fa;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.summary-icon i {
    font-size: 1.5rem;
    color: #0d6efd;
}

.summary-content h6 {
    margin: 0;
    color: #6c757d;
    font-size: 0.875rem;
}

.summary-content h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.5rem;
    font-weight: 600;
}

.semester-section {
    margin-bottom: 2rem;
}

.semester-section:last-child {
    margin-bottom: 0;
}

.semester-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #eee;
}

.semester-header h6 {
    margin: 0;
    color: #2c3e50;
    font-weight: 600;
}

.semester-gpa {
    color: #0d6efd;
    font-weight: 500;
}

.table th {
    font-weight: 600;
    color: #2c3e50;
    border-top: none;
}

.table td {
    vertical-align: middle;
}

.badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
}

.semester-filter {
    width: 250px;
}

.semester-filter .form-select {
    border-radius: 20px;
    padding: 0.5rem 1rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    gap: 0.5rem;
}

.status-badge i {
    font-size: 0.9rem;
}

.status-badge.completed {
    background-color: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #c8e6c9;
}

.status-badge.completed i {
    color: #2e7d32;
}

.status-badge.in-progress {
    background-color: #e3f2fd;
    color: #1976d2;
    border: 1px solid #bbdefb;
}

.status-badge.in-progress i {
    color: #1976d2;
}

.status-badge.failed {
    background-color: #ffebee;
    color: #d32f2f;
    border: 1px solid #ffcdd2;
}

.status-badge.failed i {
    color: #d32f2f;
}
</style>

<script>
function filterSemester(semester) {
    // Semester filter logic here
    console.log('Filtering semester:', semester);
}
</script>

<?php include 'includes/footer.php'; ?> 