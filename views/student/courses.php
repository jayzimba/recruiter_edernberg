<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$page = 'courses';
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
                    <h2>Course Registration</h2>
                    <p class="text-muted">View and manage your registered courses</p>
                </div>
            </div>
        </div>

        <!-- Registration Status -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="registration-status-card">
                    <div class="status-info">
                        <div class="status-item">
                            <span class="label">Registration Period</span>
                            <span class="value">Open</span>
                        </div>
                        <div class="status-item">
                            <span class="label">Deadline</span>
                            <span class="value">March 30, 2025</span>
                        </div>
                        <div class="status-item">
                            <span class="label">Semester</span>
                            <span class="value">2nd Semester</span>
                        </div>
                    </div>
                    <div class="status-actions">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registrationModal">
                            <i class="fas fa-plus"></i> Add Course
                        </button>
                        <button class="btn btn-success">
                            <i class="fas fa-check"></i> Submit Registration
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Selection -->
        <div class="row">
            <!-- Registered Courses -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Registered Courses</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registrationModal">
                            <i class="fas fa-plus"></i> Add Course
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="course-list">
                            <!-- Course Item -->
                            <div class="course-item">
                                <div class="course-info">
                                    <div class="course-header">
                                        <h6>CSC 201 - Data Structures</h6>
                                        <span class="status-badge">
                                            <i class="fas fa-check-circle"></i>
                                            Active
                                        </span>
                                    </div>
                                    <p class="course-description">Study of fundamental data structures and algorithms, including arrays, linked lists, stacks, queues, trees, and graphs.</p>
                                    <div class="course-meta">
                                        <span><i class="fas fa-user-tie"></i> Dr. John Doe</span>
                                    </div>
                                </div>
                                <div class="course-actions">
                                    <button class="btn btn-outline-danger btn-sm" onclick="dropCourse('CSC201')">
                                        <i class="fas fa-times"></i> Drop Course
                                    </button>
                                </div>
                            </div>

                            <!-- Course Item -->
                            <div class="course-item">
                                <div class="course-info">
                                    <div class="course-header">
                                        <h6>CSC 203 - Database Systems</h6>
                                        <span class="status-badge">
                                            <i class="fas fa-check-circle"></i>
                                            Active
                                        </span>
                                    </div>
                                    <p class="course-description">Introduction to database design, SQL, and database management systems.</p>
                                    <div class="course-meta">
                                        <span><i class="fas fa-user-tie"></i> Dr. John Doe</span>
                                    </div>
                                </div>
                                <div class="course-actions">
                                    <button class="btn btn-outline-danger btn-sm" onclick="dropCourse('CSC203')">
                                        <i class="fas fa-times"></i> Drop Course
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Registration Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="summary-list">
                            <div class="summary-item">
                                <span>Total Courses</span>
                                <span>2</span>
                            </div>
                            <div class="summary-item">
                                <span>Registration Status</span>
                                <span class="text-success">Complete</span>
                            </div>
                            <div class="summary-item">
                                <span>Last Updated</span>
                                <span>March 15, 2025</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="registrationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="course-selection">
                    <div class="search-section mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Search for courses..." id="courseSearch">
                        </div>
                    </div>
                    
                    <div class="course-grid">
                        <!-- Course Card -->
                        <div class="course-card">
                            <div class="course-card-header">
                                <h6>CSC 205</h6>
                                <span class="badge bg-primary">Available</span>
                            </div>
                            <div class="course-card-body">
                                <h5>Web Development</h5>
                                <p>Learn modern web development techniques and frameworks</p>
                                <div class="course-meta">
                                    <span><i class="fas fa-user-tie"></i> Dr. Jane Smith</span>
                                </div>
                            </div>
                            <div class="course-card-footer">
                                <button class="btn btn-primary btn-sm w-100" onclick="selectCourse('CSC205')">
                                    Select Course
                                </button>
                            </div>
                        </div>

                        <!-- Course Card -->
                        <div class="course-card">
                            <div class="course-card-header">
                                <h6>CSC 207</h6>
                                <span class="badge bg-primary">Available</span>
                            </div>
                            <div class="course-card-body">
                                <h5>Mobile App Development</h5>
                                <p>Develop mobile applications for iOS and Android</p>
                                <div class="course-meta">
                                    <span><i class="fas fa-user-tie"></i> Dr. Mike Johnson</span>
                                </div>
                            </div>
                            <div class="course-card-footer">
                                <button class="btn btn-primary btn-sm w-100" onclick="selectCourse('CSC207')">
                                    Select Course
                                </button>
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

.registration-status-card {
    background: #fff;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid #eee;
}

.status-info {
    display: flex;
    gap: 2rem;
}

.status-item {
    display: flex;
    flex-direction: column;
}

.status-item .label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.status-item .value {
    font-weight: 600;
    color: #2c3e50;
}

.status-actions {
    display: flex;
    gap: 1rem;
}

.card-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.search-box {
    width: 250px;
}

.course-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.course-item {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.course-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.course-header h6 {
    margin: 0;
    color: #2c3e50;
}

.course-description {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
}

.course-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.85rem;
    color: #6c757d;
}

.course-meta span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.cart-items {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.cart-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 6px;
}

.cart-item-info h6 {
    margin: 0;
    color: #2c3e50;
}

.cart-item-info p {
    margin: 0;
    font-size: 0.875rem;
    color: #6c757d;
}

.credits {
    font-size: 0.75rem;
    color: #0d6efd;
    font-weight: 500;
}

.cart-summary {
    border-top: 1px solid #eee;
    padding-top: 1rem;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    color: #6c757d;
}

.summary-item:last-child {
    margin-bottom: 0;
    font-weight: 500;
    color: #2c3e50;
}

.course-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    max-height: 500px;
    overflow-y: auto;
    padding-right: 0.5rem;
}

.course-grid::-webkit-scrollbar {
    width: 6px;
}

.course-grid::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.course-grid::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.course-card {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.course-card-header {
    padding: 1rem;
    background: #f8f9fa;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
}

.course-card-header h6 {
    margin: 0;
    color: #2c3e50;
    font-weight: 600;
}

.course-card-body {
    padding: 1rem;
}

.course-card-body h5 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.course-card-body p {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.course-card-footer {
    padding: 1rem;
    border-top: 1px solid #eee;
}

.search-section .input-group {
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.search-section .input-group-text {
    background: #fff;
    border-right: none;
}

.search-section .form-control {
    border-left: none;
}

.search-section .form-control:focus {
    box-shadow: none;
}

.summary-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 6px;
}

.summary-item span:first-child {
    color: #6c757d;
}

.summary-item span:last-child {
    font-weight: 500;
    color: #2c3e50;
}
</style>

<script>
function selectCourse(courseId) {
    // Course selection logic here
    console.log('Selected course:', courseId);
    // Show success message
    Swal.fire({
        icon: 'success',
        title: 'Course Added!',
        text: 'The course has been added to your registration.',
        timer: 2000,
        showConfirmButton: false
    });
}

function dropCourse(courseId) {
    // Course drop logic here
    console.log('Dropping course:', courseId);
    // Show confirmation dialog
    Swal.fire({
        title: 'Drop Course?',
        text: "Are you sure you want to drop this course?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, drop it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Handle course drop
            Swal.fire(
                'Dropped!',
                'The course has been dropped from your registration.',
                'success'
            );
        }
    });
}

// Course search functionality
document.getElementById('courseSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const courseCards = document.querySelectorAll('.course-card');
    
    courseCards.forEach(card => {
        const courseTitle = card.querySelector('h5').textContent.toLowerCase();
        const courseCode = card.querySelector('h6').textContent.toLowerCase();
        
        if (courseTitle.includes(searchTerm) || courseCode.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?> 