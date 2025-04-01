<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$page = 'exams';
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
                    <h2>Exams Schedule</h2>
                    <p class="text-muted">View and manage your upcoming examinations</p>
                </div>
            </div>
        </div>

        <!-- Exam Status Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="exam-status-card">
                    <div class="status-info">
                        <div class="status-item">
                            <span class="label">Fee Balance</span>
                            <span class="value">$1,500</span>
                        </div>
                        <div class="status-item">
                            <span class="label">Exam Slip Status</span>
                            <span class="value status-badge pending"><i class="fas fa-clock"></i> Pending</span>
                        </div>
                        <div class="status-item">
                            <span class="label">Exam Eligibility</span>
                            <div class="status-content">
                                <span class="value status-badge not-eligible"><i class="fas fa-times-circle"></i> Not Eligible</span>
                                <span class="status-comment">Tuition fee balance ($1,500) is below required threshold (75%)</span>
                            </div>
                        </div>
                    </div>
                    <div class="status-actions">
                        <button class="btn btn-primary" onclick="downloadExamSlip()">
                            <i class="fas fa-download"></i> Download Exam Slip
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Exams -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Upcoming Examinations</h5>
                            <div class="exam-filter">
                                <select class="form-select" onchange="filterExams(this.value)">
                                    <option value="all">All Courses</option>
                                    <option value="CSC201">Data Structures</option>
                                    <option value="CSC203">Database Systems</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="exam-list">
                            <!-- Exam Item -->
                            <div class="exam-item">
                                <div class="exam-info">
                                    <div class="exam-header">
                                        <div class="exam-title">
                                            <span class="course-code">CSC 201</span>
                                            <h6>Data Structures</h6>
                                        </div>
                                    </div>
                                    <div class="exam-status">
                                        <span class="status-badge upcoming">
                                            <i class="fas fa-clock"></i> 
                                            Upcoming • March 15, 2024
                                        </span>
                                    </div>
                                </div>
                                <div class="exam-actions">
                                    <button class="btn btn-outline-primary btn-sm" onclick="viewExamDetails('CSC201')">
                                        <i class="fas fa-info-circle"></i> View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Guidelines -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Exam Guidelines</h5>
                    </div>
                    <div class="card-body">
                        <div class="guidelines-list">
                            <div class="guideline-item">
                                <i class="fas fa-id-card"></i>
                                <div class="guideline-content">
                                    <h6>Required Documents</h6>
                                    <p>Bring your student ID and exam slip to all examinations</p>
                                </div>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-clock"></i>
                                <div class="guideline-content">
                                    <h6>Arrival Time</h6>
                                    <p>Arrive at least 30 minutes before the exam start time</p>
                                </div>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-mobile-alt"></i>
                                <div class="guideline-content">
                                    <h6>Electronic Devices</h6>
                                    <p>Mobile phones and other electronic devices are not allowed in the exam hall</p>
                                </div>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-file-alt"></i>
                                <div class="guideline-content">
                                    <h6>Answer Sheets</h6>
                                    <p>Use only the provided answer sheets and write clearly</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Exam Slip Modal -->
<div class="modal fade" id="examSlipModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exam Slip Download</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="exam-slip-info">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Your fee balance must be below $500 to download the exam slip.
                    </div>
                    <div class="fee-balance-info">
                        <p>Current Balance: <strong>$1,500</strong></p>
                        <p>Required Balance: <strong>$500</strong></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function downloadExamSlip() {
    const feeBalance = 1500; // This should come from your backend
    const threshold = 500;

    if (feeBalance > threshold) {
        // Show modal with fee balance warning
        const modal = new bootstrap.Modal(document.getElementById('examSlipModal'));
        modal.show();
    } else {
        // Proceed with download
        window.location.href = 'download_exam_slip.php';
    }
}

function filterExams(course) {
    // Exam filter logic here
    console.log('Filtering exams for course:', course);
}

function viewExamDetails(courseCode) {
    // View exam details logic here
    console.log('Viewing details for course:', courseCode);
}
</script>

<?php include 'includes/footer.php'; ?> 