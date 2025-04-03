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

                <!-- Mobile Level Info Cards -->
                <div class="level-info-mobile">
                    <div class="level-details-mobile">
                        <div class="info-card">
                            <span class="label">Academic Year</span>
                            <span class="value">2023/2024</span>
                        </div>
                        <div class="info-card">
                            <span class="label">Year of Study</span>
                            <span class="value">2nd Year</span>
                        </div>
                        <div class="info-card">
                            <span class="label">Current Semester</span>
                            <span class="value">2nd Semester</span>
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
                        <h3>1</h3>
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
                        <h3>6</h3>
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
                        <h3>ZMW1,500</h3>
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

        <!-- Current Semester Courses -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Current Semester Courses</h5>
                        <a href="courses.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6 col-lg-4">
                                <div class="course-card">
                                    <div class="course-header">
                                        <div class="course-code">CSC 201</div>
                                        <div class="course-status">
                                            <span class="status-badge">
                                                <i class="fas fa-check-circle"></i>
                                                In Progress
                                            </span>
                                        </div>
                                    </div>
                                    <div class="course-body">
                                        <h6 class="course-title">Data Structures</h6>
                                        <div class="course-info">
                                            <p><i class="fas fa-user-tie"></i> Dr. John Doe</p>
                                            <p><i class="fas fa-book-reader"></i> 3 Credits</p>
                                            <p><i class="fas fa-chart-line"></i> Progress: 65%</p>
                                        </div>
                                        <div class="course-progress">
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" style="width: 65%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="course-footer">
                                        <a href="course-details.php?id=CSC201" class="btn btn-sm btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="course-card">
                                    <div class="course-header">
                                        <div class="course-code">CSC 203</div>
                                        <div class="course-status">
                                            <span class="status-badge">
                                                <i class="fas fa-check-circle"></i>
                                                In Progress
                                            </span>
                                        </div>
                                    </div>
                                    <div class="course-body">
                                        <h6 class="course-title">Database Systems</h6>
                                        <div class="course-info">
                                            <p><i class="fas fa-user-tie"></i> Dr. John Doe</p>
                                            <p><i class="fas fa-book-reader"></i> 3 Credits</p>
                                            <p><i class="fas fa-chart-line"></i> Progress: 45%</p>
                                        </div>
                                        <div class="course-progress">
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" style="width: 45%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="course-footer">
                                        <a href="course-details.php?id=CSC203" class="btn btn-sm btn-outline-primary">View Details</a>
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
</style>

<?php include 'includes/footer.php'; ?>