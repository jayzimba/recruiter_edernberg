<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'backend/models/Student.php';

$student = new Student();
$currentSemesterCourses = $student->getCurrentSemesterCourses($_SESSION['student_id']);
$academicInfo = $student->getAcademicInfo($_SESSION['student_id']);

// Get current semester
$currentSemester = isset($academicInfo['current_semester']) ? $academicInfo['current_semester'] : '2nd Semester';

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
                    <p class="text-muted">View and manage your registered courses for <?php echo htmlspecialchars($currentSemester); ?></p>
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
                            <span class="label">Semester</span>
                            <span class="value"><?php echo htmlspecialchars($currentSemester); ?></span>
                        </div>
                        <div class="status-item">
                            <span class="label">Registered Courses</span>
                            <span class="value"><?php echo count(array_filter($currentSemesterCourses, function($course) { 
                                return isset($course['enrollment_status']) && $course['enrollment_status'] === 'active'; 
                            })); ?></span>
                        </div>
                    </div>
                    <div class="status-actions">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registrationModal">
                            <i class="fas fa-plus"></i> Add Course
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
                            <?php 
                            $registeredCourses = array_filter($currentSemesterCourses, function($course) { 
                                return isset($course['enrollment_status']) && $course['enrollment_status'] === 'active'; 
                            });
                            
                            if (empty($registeredCourses)): 
                            ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> You haven't registered for any courses this semester.
                                </div>
                            <?php else: ?>
                                <?php foreach ($registeredCourses as $course): ?>
                                    <div class="course-item">
                                        <div class="course-info">
                                            <div class="course-header">
                                                <h6><?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?></h6>
                                                <span class="status-badge badge bg-success">
                                                    <i class="fas fa-check-circle"></i>
                                                    Active
                                                </span>
                                            </div>
                                            <p class="course-description"><?php echo htmlspecialchars($course['description'] ?? ''); ?></p>
                                            <div class="course-meta">
                                                <span><i class="fas fa-user-tie"></i> <?php echo htmlspecialchars($course['instructor_name'] ?? 'TBA'); ?></span>
                                                <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($course['credits']); ?> Credits</span>
                                                <?php if (isset($course['registration_date'])): ?>
                                                    <span><i class="fas fa-calendar"></i> Registered: <?php echo date('M d, Y', strtotime($course['registration_date'])); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="course-actions">
                                            <button class="btn btn-outline-danger btn-sm" onclick="dropCourse('<?php echo htmlspecialchars($course['course_code']); ?>')">
                                                <i class="fas fa-times"></i> Drop Course
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
                                <span><?php echo count($registeredCourses); ?></span>
                            </div>
                            <div class="summary-item">
                                <span>Total Credits</span>
                                <span><?php 
                                    echo array_reduce($registeredCourses, function($sum, $course) {
                                        return $sum + ($course['credits'] ?? 0);
                                    }, 0);
                                ?></span>
                            </div>
                            <div class="summary-item">
                                <span>Registration Status</span>
                                <span class="text-success">Active</span>
                            </div>
                            <div class="summary-item">
                                <span>Last Updated</span>
                                <span><?php 
                                    $lastRegistered = array_reduce($registeredCourses, function($latest, $course) {
                                        return (!$latest || ($course['registration_date'] ?? '') > $latest) ? 
                                            ($course['registration_date'] ?? '') : $latest;
                                    }, '');
                                    echo $lastRegistered ? date('M d, Y', strtotime($lastRegistered)) : 'N/A';
                                ?></span>
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
                        <?php 
                        $availableCourses = array_filter($currentSemesterCourses, function($course) { 
                            return !isset($course['enrollment_status']) || $course['enrollment_status'] !== 'active'; 
                        });
                        
                        foreach ($availableCourses as $course): 
                        ?>
                            <div class="course-card">
                                <div class="course-card-header">
                                    <h6><?php echo htmlspecialchars($course['course_code']); ?></h6>
                                    <span class="badge bg-primary">Available</span>
                                </div>
                                <div class="course-card-body">
                                    <h5><?php echo htmlspecialchars($course['course_name']); ?></h5>
                                    <p><?php echo htmlspecialchars($course['description'] ?? 'No description available'); ?></p>
                                    <div class="course-meta">
                                        <span><i class="fas fa-user-tie"></i> <?php echo htmlspecialchars($course['instructor_name'] ?? 'TBA'); ?></span>
                                        <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($course['credits']); ?> Credits</span>
                                    </div>
                                </div>
                                <div class="course-card-footer">
                                    <button class="btn btn-primary btn-sm w-100" onclick="selectCourse('<?php echo htmlspecialchars($course['course_code']); ?>')">
                                        Select Course
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
    align-items: center;
    gap: 1rem;
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
async function selectCourse(courseId) {
    try {
        const response = await fetch('backend/api/register_course.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'add',
                course_code: courseId
            })
        });

        const data = await response.json();
        
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Course Added!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            // Reload the page to show updated course list
            setTimeout(() => location.reload(), 2000);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Failed to add course. Please try again.'
        });
    }
}

async function dropCourse(courseId) {
    try {
        const result = await Swal.fire({
            title: 'Drop Course?',
            text: "Are you sure you want to drop this course?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, drop it!'
        });

        if (result.isConfirmed) {
            const response = await fetch('backend/api/register_course.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'drop',
                    course_code: courseId
                })
            });

            const data = await response.json();
            
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Course Dropped!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                // Reload the page to show updated course list
                setTimeout(() => location.reload(), 2000);
            } else {
                throw new Error(data.message);
            }
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Failed to drop course. Please try again.'
        });
    }
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