<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$page = 'dashboard';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <!-- Level Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="level-info-card">
                    <div class="current-level">
                        <div class="level-details">
                            <div class="year-info">
                                <span class="label">Academic Year</span>
                                <span class="value">2023/2024</span>
                            </div>
                            <div class="divider"></div>
                            <div class="year-info">
                                <span class="label">Year of Study</span>
                                <span class="value">2nd Year</span>
                            </div>
                            <div class="divider"></div>
                            <div class="semester-info">
                                <span class="label">Current Semester</span>
                                <span class="value">2nd Semester</span>
                            </div>
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
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stats-info">
                        <p>Registered Courses</p>
                        <h3>6</h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="stats-info">
                        <p>Pending Assignments</p>
                        <h3>3</h3>
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
                        <h3>2</h3>
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
                        <h3>$1,500</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Sections -->
        <div class="row">
            <!-- Registered Courses -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Current Semester Courses</h5>
                        <a href="courses.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Credits</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>CSC 201</td>
                                        <td>Data Structures</td>
                                        <td>3</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                    </tr>
                                    <tr>
                                        <td>CSC 203</td>
                                        <td>Database Systems</td>
                                        <td>3</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Important Dates</h5>
                    </div>
                    <div class="card-body">
                        <div class="calendar-events">
                            <div class="calendar-event">
                                <div class="event-date">
                                    <span class="month">MAR</span>
                                    <span class="day">15</span>
                                </div>
                                <div class="event-details">
                                    <h6>Mid Semester Exams</h6>
                                    <p>All Courses</p>
                                </div>
                            </div>
                            <div class="calendar-event">
                                <div class="event-date">
                                    <span class="month">APR</span>
                                    <span class="day">30</span>
                                </div>
                                <div class="event-details">
                                    <h6>End of Semester</h6>
                                    <p>Last day of classes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>