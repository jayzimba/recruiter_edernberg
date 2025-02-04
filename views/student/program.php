<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Program - Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
    :root {
        --primary-color: #4A90E2;
        --secondary-color: #6c757d;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
    }

    .sidebar {
        min-height: 100vh;
        background: #1a237e;
        padding: 1rem;
        color: white;
    }

    .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 5px;
        transition: all 0.3s ease;
    }

    .nav-link:hover,
    .nav-link.active {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .nav-link i {
        margin-right: 10px;
    }

    .main-content {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 20px;
    }

    .header {
        background: white;
        padding: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        border-radius: 10px;
    }

    .program-info {
        background: white;
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .info-label {
        color: #6c757d;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-weight: 500;
        margin-bottom: 1rem;
    }

    .year-section {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
    }

    .year-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
    }

    .year-badge {
        background: var(--primary-color);
        color: white;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        margin-right: 1rem;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .semester-title {
        background: #f8f9fa;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        margin: 1.5rem 0 1rem;
        color: var(--primary-color);
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .semester-title i {
        margin-right: 0.75rem;
        font-size: 1.2rem;
    }

    .course-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .course-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-color);
    }

    .course-code {
        color: var(--primary-color);
        font-weight: 500;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .credits-badge {
        background: var(--primary-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }

    .credits-badge i {
        margin-right: 0.5rem;
    }

    .back-button {
        margin-bottom: 1.5rem;
    }

    .back-button .btn {
        padding: 0.6rem 1.2rem;
        font-weight: 500;
    }

    .back-button i {
        margin-right: 0.5rem;
    }

    .program-banner {
        background: linear-gradient(135deg, #1a237e 0%, #4A90E2 100%);
        color: white;
        padding: 3rem 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .program-banner::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 300px;
        height: 100%;
        background: url('../../assets/icons/pattern.png');
        opacity: 0.1;
    }

    .program-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 1.5rem;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .stat-card i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    @media (max-width: 768px) {
        .sidebar {
            position: fixed;
            left: -100%;
            top: 0;
            z-index: 1000;
            width: 80%;
            max-width: 300px;
            transition: all 0.3s ease;
        }

        .sidebar.show {
            left: 0;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-backdrop.show {
            display: block;
        }
    }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="mobile-nav d-md-none"><button class="btn btn-primary" onclick="toggleSidebar()"><i
                    class="bi bi-list"></i>Menu </button></div>
        <div class="sidebar-backdrop" onclick="toggleSidebar()"></div>
        <div class="row">
            <div class="col-md-3 col-lg-2 sidebar d-none d-md-block">
                <div class="d-flex flex-column">
                    <div class="text-center mb-4"><img src="../../assets/icons/edernberg.png" alt="Logo"
                            class="img-fluid mb-3" style="max-width: 120px;">
                        <h5>Student Portal</h5>
                    </div>
                    <nav class="nav flex-column"><a class="nav-link" href="dashboard.php"><i
                                class="bi bi-speedometer2"></i>Dashboard </a><a class="nav-link active"
                            href="program.php"><i class="bi bi-journal-text"></i>My Program </a><a class="nav-link"
                            href="change-password.php"><i class="bi bi-key"></i>Change
                            Password </a><a class="nav-link" href="#" onclick="logout()"><i
                                class="bi bi-box-arrow-right"></i>Logout </a></nav>
                </div>
            </div>
            <div class="col-md-9 col-lg-10 main-content">
                <div class="back-button"><a href="dashboard.php" class="btn btn-outline-primary"><i
                            class="bi bi-arrow-left"></i>Back to Dashboard </a></div>
                <div id="loadingSpinner" class="text-center my-5">
                    <div class="spinner-border text-primary" role="status"><span
                            class="visually-hidden">Loading...</span></div>
                </div>
                <div id="programContent" style="display: none;">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
    <script>
    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('show');
        document.querySelector('.sidebar-backdrop').classList.toggle('show');
    }

    function logout() {
        fetch('../../api/student/logout.php').then(response => response.json()).then(data => {
            if (data.status) {
                window.location.href = 'login.php';
            }

        }).catch(error => {
            console.error('Logout failed:', error);
        });
    }

    function loadProgramDetails() {
        const spinner = document.getElementById('loadingSpinner');
        const content = document.getElementById('programContent');

        fetch('../../api/student/get_program_details.php')
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    const program = data.data.program;
                    const courses = data.data.courses;

                    let html = `
                            <div class="program-banner">
                                <h3 class="mb-2">${program.program_name}</h3>
                                <p class="mb-4 opacity-75">Program Code: ${program.id}</p>
                                
                                <div class="program-stats">
                                    <div class="stat-card">
                                        <i class="bi bi-building"></i>
                                        <h6>School</h6>
                                        <p class="mb-0">${program.department_name}</p>
                                    </div>
                                    <div class="stat-card">
                                        <i class="bi bi-clock-history"></i>
                                        <h6>Duration</h6>
                                        <p class="mb-0">${program.duration} Years</p>
                                    </div>
                                    <div class="stat-card">
                                        <i class="bi bi-calendar-check"></i>
                                        <h6>Study Mode</h6>
                                        <p class="mb-0">${program.study_mode}</p>
                                    </div>
                                </div>
                            </div>`;

                    // Add courses by year and semester
                    for (const [year, semesters] of Object.entries(courses)) {
                        html += `
                                <div class="year-section">
                                    <div class="year-header">
                                        
                                        <h5 class="mb-0">Year ${year}</h5>
                                    </div>`;

                        for (const [semester, courses] of Object.entries(semesters)) {
                            html += `
                                    <div class="semester-title">
                                        <i class="bi bi-calendar3"></i>
                                        ${semester} - Semester
                                    </div>`;

                            courses.forEach(course => {
                                html += `
                                        <div class="course-card">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="course-code">${course.course_code}</div>
                                                    <h6 class="mb-2">${course.course_name}</h6>
                                                    <p class="text-muted mb-0">${course.description}</p>
                                                </div>
                                                <span class="credits-badge">
                                                    <i class="bi bi-award"></i>
                                                   ${course.credits} Credits
                                                </span>
                                            </div>
                                        </div>`;
                            });
                        }
                        html += `</div>`;
                    }

                    content.innerHTML = html;
                    spinner.style.display = 'none';
                    content.style.display = 'block';
                } else {
                    content.innerHTML = `
                            <div class="alert alert-danger">
                                ${data.message}
                            </div>`;
                    spinner.style.display = 'none';
                    content.style.display = 'block';
                }
            })
            .catch(error => {
                content.innerHTML = `
                        <div class="alert alert-danger">
                            An error occurred while loading program details.
                        </div>`;
                spinner.style.display = 'none';
                content.style.display = 'block';
            });
    }

    document.addEventListener('DOMContentLoaded', loadProgramDetails);
    </script>
</body>

</html>