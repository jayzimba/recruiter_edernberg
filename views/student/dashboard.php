<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'backend/models/Student.php';

$student = new Student();
$dashboard_data = $student->getDashboardData($_SESSION['student_id']);
$currentSemesterCourses = $student->getCurrentSemesterCourses($_SESSION['student_id']);
$academicCalendar = $student->getAcademicCalendar($_SESSION['student_id']);
$profileData = $student->getProfileData($_SESSION['student_id']);

// Debug logging
error_log("Student ID: " . $_SESSION['student_id']);
error_log("Profile Data: " . print_r($profileData, true));

if (!$dashboard_data) {
    // Handle error - maybe redirect to error page or show error message
    $error_message = "Unable to load dashboard data. Please try again later.";
}

if (!$profileData) {
    // Handle error for profile data
    $error_message = isset($error_message) ? $error_message . " Profile data could not be loaded." : "Profile data could not be loaded.";
    error_log("Failed to load profile data for student: " . $_SESSION['student_id']);
}

$page = 'dashboard';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $error_message; ?>
            </div>
        <?php else: ?>
            <!-- Internal CSS for Level Info Card -->
            <style>
                /* Desktop/Web View Styles */
                .level-info-card {
                    background: linear-gradient(135deg, rgba(13, 110, 253, 0.85), rgba(0, 123, 255, 0.85)), url('../../assets/icons/bg.png');
                    background-size: cover;
                    background-position: center;
                    border-radius: 12px;
                    padding: 1.5rem;
                    color: #fff;
                    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
                }

                .level-details {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    text-align: center;
                }

                .year-info, .semester-info {
                    flex: 1;
                }

                .divider {
                    width: 1px;
                    height: 40px;
                    background: rgba(255, 255, 255, 0.2);
                }

                .label {
                    display: block;
                    font-size: 0.8rem;
                    opacity: 0.9;
                    margin-bottom: 0.25rem;
                }

                .value {
                    font-size: 1.1rem;
                    font-weight: 600;
                }

                .level-info-mobile {
                    display: none; /* Hide mobile version by default */
                }

                /* Mobile View Styles */
                @media (max-width: 768px) {
                    .level-info-card {
                        display: none; /* Hide desktop version */
                    }

                    .level-info-mobile {
                        display: block; /* Show mobile version */
                        margin: 0 -1rem;
                        padding: 0 1rem;
                        overflow-x: auto;
                        -webkit-overflow-scrolling: touch;
                        scrollbar-width: none;
                        -ms-overflow-style: none;
                    }

                    .level-details-mobile {
                        display: flex;
                        padding: 0.5rem 0;
                        gap: 1rem;
                        min-width: min-content;
                    }

                    .info-card {
                        background: linear-gradient(135deg, rgba(13, 110, 253, 0.85), rgba(0, 123, 255, 0.85));
                        padding: 0.875rem 1.25rem;
                        border-radius: 12px;
                        min-width: 130px;
                        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
                        color: #fff;
                        transition: transform 0.2s ease;
                        cursor: pointer;
                    }

                    .info-card:active {
                        transform: scale(0.98);
                    }

                    .info-card .label {
                        font-size: 0.7rem;
                        white-space: nowrap;
                    }

                    .info-card .value {
                        font-size: 1rem;
                        white-space: nowrap;
                    }
                }
            </style>

            <!-- Desktop Level Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="level-info-card">
                        <div class="level-details">
                            <div class="year-info">
                                <span class="label">Academic Year</span>
                                <span class="value"><?php echo $dashboard_data['academic_info']['academic_year'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="divider"></div>
                            <div class="year-info">
                                <span class="label">Year of Study</span>
                                <span class="value"><?php echo $dashboard_data['academic_info']['year_of_study'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="divider"></div>
                            <div class="semester-info">
                                <span class="label">Current Semester</span>
                                <span class="value"><?php echo $dashboard_data['academic_info']['current_semester'] ?? 'N/A'; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Level Info Cards -->
                    <div class="level-info-mobile">
                        <div class="level-details-mobile">
                            <div class="info-card">
                                <span class="label">Academic Year</span>
                                <span class="value"><?php echo $dashboard_data['academic_info']['academic_year'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="info-card">
                                <span class="label">Year of Study</span>
                                <span class="value"><?php echo $dashboard_data['academic_info']['year_of_study'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="info-card">
                                <span class="label">Current Semester</span>
                                <span class="value"><?php echo $dashboard_data['academic_info']['current_semester'] ?? 'N/A'; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
        <div class="col-sm-6 col-xl-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stats-info">
                        <p>Registered Programs</p>
                            <h3><?php echo $dashboard_data['programs_count']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stats-info">
                        <p>Registered Courses</p>
                            <h3><?php echo $dashboard_data['courses_count']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stats-info">
                        <p>Upcoming Exams</p>
                            <h3><?php echo $dashboard_data['upcoming_exams']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-money-bill"></i>
                    </div>
                    <div class="stats-info">
                        <p>Fee Balance</p>
                            <h3>ZMW<?php echo number_format($dashboard_data['fee_balance'], 2); ?></h3>
                        </div>
                    </div>
                </div>
            </div>


        <!-- Main Sections -->
        <div class="row">
            <!-- Quick Links -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Links</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="courses.php" class="quick-link-card">
                                    <div class="quick-link-icon">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div class="quick-link-content">
                                        <h6>Course Registration</h6>
                                        <p>Register for new courses</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="transcripts.php" class="quick-link-card">
                                    <div class="quick-link-icon">
                                        <i class="fas fa-scroll"></i>
                                    </div>
                                    <div class="quick-link-content">
                                        <h6>My Transcripts</h6>
                                        <p>View academic records</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="exams.php" class="quick-link-card">
                                    <div class="quick-link-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="quick-link-content">
                                        <h6>Exams Schedule</h6>
                                        <p>View upcoming examinations</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="fees.php" class="quick-link-card">
                                    <div class="quick-link-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="quick-link-content">
                                        <h6>Fee Payment</h6>
                                        <p>Manage your payments</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Academic Calendar</h5>
                    </div>
                    <div class="card-body">
                        <div class="calendar-events">
                            <?php if (empty($academicCalendar)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>No upcoming events in the academic calendar.
                                </div>
                            <?php else: ?>
                                <?php foreach ($academicCalendar as $event): ?>
                            <div class="calendar-event">
                                <div class="event-date">
                                            <span class="month"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                                            <span class="day"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                                        </div>
                                        <div class="event-details">
                                            <h6><?php echo htmlspecialchars($event['event_title']); ?></h6>
                                            <p><?php echo htmlspecialchars($event['event_description']); ?></p>
                                            <span class="event-type badge bg-<?php 
                                                echo match($event['event_type']) {
                                                    'exam' => 'danger',
                                                    'holiday' => 'success',
                                                    'deadline' => 'warning',
                                                    default => 'info'
                                                };
                                            ?>">
                                                <?php echo ucfirst($event['event_type']); ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <!-- Current Semester Courses -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Current Semester Courses</h5>
                            <a href="courses.php" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($currentSemesterCourses)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>No courses are currently available for this semester.
                                </div>
                            <?php else: ?>
                                <div class="row g-4">
                                    <?php foreach ($currentSemesterCourses as $course): ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="course-card">
                                                <div class="course-header">
                                                    <div class="course-code"><?php echo htmlspecialchars($course['course_code']); ?></div>
                                                    <div class="course-status">
                                                        <?php if ($course['is_enrolled']): ?>
                                                            <span class="status-badge">
                                                                <i class="fas fa-check-circle"></i>
                                                                In Progress
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="status-badge status-badge-warning">
                                                                <i class="fas fa-clock"></i>
                                                                Not Enrolled
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="course-body">
                                                    <h6 class="course-title"><?php echo htmlspecialchars($course['course_name']); ?></h6>
                                                    <div class="course-info">
                                                        <p><i class="fas fa-user-tie"></i> <?php echo htmlspecialchars($course['instructor_name']); ?></p>
                                                        <p><i class="fas fa-book-reader"></i> <?php echo $course['credits']; ?> Credits</p>
                                                       
                                                    </div>
                                                   
                                                </div>
                                                <div class="course-footer">
                                                    <?php if ($course['is_enrolled']): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#courseDetailsModal">
                                                            View Details
                                                        </button>
                                                    <?php else: ?>
                                                        <a href="enroll.php?course=<?php echo $course['course_offering_id']; ?>" class="btn btn-sm btn-primary">
                                                            Enroll Now
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

      
    </div>
</div>

<style>
.quick-link-card {
    display: flex;
    align-items: center;
    padding: 1.5rem;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
    border: 1px solid #eee;
}

.quick-link-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    text-decoration: none;
    color: inherit;
}

.quick-link-icon {
    width: 50px;
    height: 50px;
    background: #f8f9fa;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.quick-link-icon i {
    font-size: 1.5rem;
    color: #0d6efd;
}

.quick-link-content h6 {
    margin: 0;
    font-size: 1rem;
    color: #2c3e50;
}

.quick-link-content p {
    margin: 0;
    font-size: 0.875rem;
    color: #6c757d;
}

.course-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #eee;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.course-header {
    padding: 1rem;
    background: #f8f9fa;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
}

.course-code {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1.1rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.4rem 0.8rem;
    background: #e8f5e9;
    color: #2e7d32;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-badge i {
    margin-right: 0.4rem;
    font-size: 0.9rem;
}

.course-body {
    padding: 1.2rem;
}

.course-title {
    margin-bottom: 1rem;
    color: #2c3e50;
    font-size: 1.1rem;
    font-weight: 600;
}

.course-info p {
    margin-bottom: 0.8rem;
    font-size: 0.9rem;
    color: #6c757d;
    display: flex;
    align-items: center;
}

.course-info i {
    width: 20px;
    color: #0d6efd;
    margin-right: 0.5rem;
}

.course-progress {
    margin-top: 1rem;
}

.progress {
    height: 6px;
    background-color: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
}

.progress-bar {
    background-color: #0d6efd;
    transition: width 0.3s ease;
}

.course-footer {
    padding: 1rem;
    border-top: 1px solid #eee;
    text-align: right;
}

.course-footer .btn {
    padding: 0.4rem 1rem;
    font-size: 0.85rem;
}

.status-badge-warning {
    background: #fff3e0;
    color: #e65100;
}

.status-badge-warning i {
    color: #e65100;
}

.calendar-event {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    border-bottom: 1px solid #eee;
}

.calendar-event:last-child {
    border-bottom: none;
}

.event-date {
    text-align: center;
    min-width: 60px;
    margin-right: 1rem;
    background: #0056b3;
    padding: 0.5rem;
    border-radius: 8px;
}

.event-date .month {
    display: block;
    font-size: 0.8rem;
    color: #ffffff;
    text-transform: uppercase;
    font-weight: 500;
}

.event-date .day {
    display: block;
    font-size: 1.5rem;
    font-weight: 600;
    color: #ffffff;
    line-height: 1;
}

.event-details {
    flex: 1;
}

.event-details h6 {
    margin: 0 0 0.5rem;
    color: #2c3e50;
    font-size: 1rem;
}

.event-details p {
    margin: 0 0 0.5rem;
    color: #6c757d;
    font-size: 0.875rem;
}

.event-type {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
}
</style>

<!-- Course Details Modal -->
<div class="modal fade" id="courseDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <button type="button" class="btn-close modal-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                
                <div class="course-modal-container">
                    <div class="course-modal-header">
                        <div class="course-modal-cover"></div>
                        <div class="course-modal-title">
                            <h2 class="course-code mb-2" id="modalCourseCode"></h2>
                            <p class="course-name" id="modalCourseName"></p>
                            <div class="course-badges mb-3">
                                <span class="badge bg-primary" id="modalCourseCredits"></span>
                                <span class="badge bg-success" id="modalCourseStatus"></span>
                            </div>
                        </div>
                    </div>

                    <div class="course-modal-content">
                        <div class="course-info-grid">
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-code"></i>
                                </div>
                                <div class="info-content">
                                    <label>Course Code</label>
                                    <span id="modalCourseCodeInfo"></span>
                                </div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="info-content">
                                    <label>Instructor</label>
                                    <span id="modalInstructor"></span>
                                </div>
                            </div>
                        </div>

                        <div class="course-description">
                            <h5>Course Description</h5>
                            <p id="modalCourseDescription"></p>
                        </div>

                        <div class="course-outline">
                            <h5>Course Outline</h5>
                            <div class="outline-sections" id="modulesList">
                                <!-- Modules will be loaded dynamically -->
                                <div class="text-center p-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.course-modal-container {
    position: relative;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
}

.course-modal-header {
    position: relative;
    text-align: center;
    margin-bottom: 4rem;
}

.course-modal-cover {
    height: 180px;
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.85), rgba(0, 123, 255, 0.85)), url('../../assets/icons/bg.png');
    background-size: cover;
    background-position: center;
    position: relative;
}

.course-modal-title {
    padding: 0 2rem;
}

.course-modal-title .course-code {
    font-size: 2.5rem;
    color: rgb(39, 39, 39);
    font-weight: 700;
    margin: 0;
}

.course-modal-title .course-name {
    color:rgb(184, 183, 183);
    font-size: 1.3rem;
    font-weight: 500;
    margin: 0.1rem 3;
}

.course-badges {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.course-badges .badge {
    padding: 0.5rem 1rem;
    font-weight: 500;
}

.course-modal-content {
    padding: 0 2rem 2rem;
}

.course-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.info-card {
    background: #f8f9fa;
    padding: 1.25rem;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.info-icon {
    width: 45px;
    height: 45px;
    background: #e9ecef;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0d6efd;
    font-size: 1.25rem;
}

.info-content label {
    display: block;
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.info-content span {
    display: block;
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
}

.course-description {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 2rem;
}

.course-description h5,
.course-outline h5 {
    color: #2c3e50;
    margin-bottom: 1.25rem;
    font-size: 1.3rem;
    font-weight: 600;
}

.course-description p {
    color: #2c3e50;
    line-height: 1.7;
    font-size: 1.1rem;
    margin: 0;
}

.outline-sections {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.outline-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.5rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.outline-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.outline-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.outline-header i {
    color: #0d6efd;
    font-size: 1.1rem;
}

.outline-header h6 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
}

.module-description {
    color: #6c757d;
    font-size: 0.95rem;
    line-height: 1.6;
    margin: 0;
    padding-left: 2rem;
}

.modal-close-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 10;
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    padding: 0.5rem;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s ease;
}

.modal-close-btn:hover {
    background-color: rgba(255, 255, 255, 0.5);
}

@media (max-width: 768px) {
    .course-modal-header {
        margin-bottom: 3.5rem;
    }
    
    .course-modal-cover {
        height: 150px;
    }
    
    .course-modal-title .course-code {
        font-size: 2rem;
    }
    
    .course-modal-title .course-name {
        font-size: 1.1rem;
    }
    
    .course-modal-content {
        padding: 0 1rem 1.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all view details buttons
    const viewDetailsButtons = document.querySelectorAll('[data-bs-target="#courseDetailsModal"]');
    
    // Add click event listener to each button
    viewDetailsButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get the course data from the parent course card
            const courseCard = this.closest('.course-card');
            const courseCode = courseCard.querySelector('.course-code').textContent;
            const courseName = courseCard.querySelector('.course-title').textContent;
            const instructorName = courseCard.querySelector('.course-info p:nth-child(1)').textContent.replace('', '').trim();
            const credits = courseCard.querySelector('.course-info p:nth-child(2)').textContent;

            // Update modal content
            document.getElementById('modalCourseCode').textContent = courseCode;
            document.getElementById('modalCourseCodeInfo').textContent = courseCode;
            document.getElementById('modalCourseName').textContent = courseName;
            document.getElementById('modalCourseCredits').textContent = credits;
            document.getElementById('modalCourseStatus').textContent = 'In Progress';
            document.getElementById('modalInstructor').textContent = instructorName;
            
            // Show loading spinner
            document.getElementById('modulesList').innerHTML = `
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            // Fetch course modules
            fetch('backend/api/get_course_modules.php?course_code=' + encodeURIComponent(courseCode))
                .then(response => response.json())
                .then(data => {
                    if (data.modules && data.modules.length > 0) {
                        const modulesHtml = data.modules.map(module => `
                            <div class="outline-section">
                                <div class="outline-header">
                                    <i class="fas fa-bookmark"></i>
                                    <h6>Module ${module.module_number}: ${module.module_title}</h6>
                                </div>
                                <div class="module-description">
                                    <p>${module.description}</p>
                                </div>
                            </div>
                        `).join('');
                        document.getElementById('modulesList').innerHTML = modulesHtml;
                    } else {
                        document.getElementById('modulesList').innerHTML = `
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No modules found for this course.
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('modulesList').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Failed to load course modules.
                        </div>
                    `;
                });

            // Fetch course description
            fetch('backend/api/get_course_details.php?course_code=' + encodeURIComponent(courseCode))
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalCourseDescription').textContent = data.description;
                })
                .catch(error => console.error('Error:', error));
        });
    });
});
</script>

<style>
/* Add styles for module description */
.module-description {
    margin-top: 0.75rem;
    color: #6c757d;
    font-size: 0.95rem;
    line-height: 1.6;
}

.module-description p {
    margin: 0;
}

/* Update outline section styles */
.outline-section {
    background: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    transition: transform 0.2s ease;
    margin-bottom: 1rem;
}

.outline-section:last-child {
    margin-bottom: 0;
}

.outline-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}

/* Loading spinner styles */
.spinner-border {
    width: 2rem;
    height: 2rem;
}
</style>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <button type="button" class="btn-close modal-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                
                <div class="profile-modal-container">
                    <div class="profile-modal-header">
                        <div class="profile-modal-cover"></div>
                        <div class="profile-modal-title">
                            <div class="profile-image-container mb-3">
                                <?php if (!empty($profileData['profile_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($profileData['profile_image']); ?>" alt="Profile Image" class="profile-image">
                                <?php else: ?>
                                    <div class="profile-image-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h2 class="student-name mb-2"><?php echo htmlspecialchars($profileData['first_name'] . ' ' . $profileData['last_name']); ?></h2>
                            <p class="student-id mb-2"><?php echo htmlspecialchars($profileData['student_id']); ?></p>
                            <div class="student-badges mb-3">
                                <span class="badge bg-primary">Student</span>
                                <span class="badge bg-success">Active</span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-modal-content">
                        <!-- Academic Information -->
                        <div class="profile-section">
                            <h5 class="section-title">
                                <i class="fas fa-graduation-cap"></i>
                                Academic Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="info-card">
                                        <label>Program</label>
                                        <span><?php echo htmlspecialchars($profileData['program_name']); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-card">
                                        <label>Department</label>
                                        <span><?php echo htmlspecialchars($profileData['department_name']); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-card">
                                        <label>Academic Year</label>
                                        <span><?php echo htmlspecialchars($profileData['academic_year']); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-card">
                                        <label>Year of Study</label>
                                        <span><?php echo htmlspecialchars($profileData['current_year']); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-card">
                                        <label>Current Semester</label>
                                        <span><?php echo htmlspecialchars($profileData['current_semester']); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-card">
                                        <label>Program Status</label>
                                        <span class="<?php echo strtolower($profileData['program_status']) === 'active' ? 'text-success' : 'text-warning'; ?>">
                                            <?php echo htmlspecialchars($profileData['program_status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="info-card">
                                        <label>Enrollment Date</label>
                                        <span><?php echo htmlspecialchars($profileData['enrollment_date']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="profile-section">
                            <h5 class="section-title">
                                <i class="fas fa-user"></i>
                                Personal Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="info-card">
                                        <label>Email</label>
                                        <span><?php echo htmlspecialchars($profileData['email']); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-card">
                                        <label>Phone</label>
                                        <span><?php echo htmlspecialchars($profileData['phone']); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-card">
                                        <label>Date of Birth</label>
                                        <span><?php echo htmlspecialchars($profileData['date_of_birth']); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-card">
                                        <label>Nationality</label>
                                        <span><?php echo htmlspecialchars($profileData['nationality']); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-card">
                                        <label>Gender</label>
                                        <span><?php echo htmlspecialchars($profileData['gender']); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-card">
                                        <label>Address</label>
                                        <span><?php echo htmlspecialchars($profileData['address']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Registration Information -->
                        <div class="profile-section">
                            <h5 class="section-title">
                                <i class="fas fa-clock"></i>
                                Registration Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="info-card">
                                        <label>Registration Date</label>
                                        <span><?php echo htmlspecialchars($profileData['registration_date']); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-card">
                                        <label>Status</label>
                                        <span class="text-success">Active</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Profile Modal Styles */
.profile-modal-container {
    position: relative;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
}

.profile-modal-header {
    position: relative;
    text-align: center;
    padding-bottom: 2rem;
}

.profile-modal-cover {
    height: 160px;
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.85), rgba(0, 123, 255, 0.85)), url('../../assets/icons/bg.png');
    background-size: cover;
    background-position: center;
    position: relative;
}

.profile-modal-title {
    margin-top: -75px;
}

.profile-image-container {
    width: 150px;
    height: 150px;
    margin: 0 auto;
    border-radius: 50%;
    overflow: hidden;
    border: 5px solid white;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.profile-image-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #0d6efd, #0043a8);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
}

.student-name {
    font-size: 1.75rem;
    color: #2c3e50;
    font-weight: 600;
    margin: 0;
}

.student-id {
    color: #6c757d;
    font-size: 1.1rem;
    margin: 0;
}

.profile-modal-content {
    padding: 2rem;
}

.profile-section {
    margin-bottom: 2rem;
}

.profile-section:last-child {
    margin-bottom: 0;
}

.section-title {
    color: #2c3e50;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-title i {
    color: #0d6efd;
    font-size: 1.1rem;
}

.info-card {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    height: 100%;
}

.info-card label {
    display: block;
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.info-card span {
    display: block;
    color: #2c3e50;
    font-weight: 500;
}

.student-badges {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.student-badges .badge {
    padding: 0.5rem 1rem;
    font-weight: 500;
}

.modal-close-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 10;
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    padding: 0.5rem;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s ease;
}

.modal-close-btn:hover {
    background-color: rgba(255, 255, 255, 0.5);
}

@media (max-width: 768px) {
    .profile-modal-header {
        padding-bottom: 1.5rem;
    }
    
    .profile-image-container {
        width: 120px;
        height: 120px;
    }
    
    .student-name {
        font-size: 1.5rem;
    }
    
    .profile-modal-content {
        padding: 1.5rem;
    }
    
    .section-title {
        font-size: 1.1rem;
    }
}
</style>

<!-- Add this button where you want to trigger the modal -->
<button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#profileModal">
    <i class="fas fa-user-circle me-2"></i>View Profile
</button>

<script>
// Add this to your existing JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all modals
    var modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        new bootstrap.Modal(modal);
    });
});
</script>

<?php include 'includes/footer.php'; ?>