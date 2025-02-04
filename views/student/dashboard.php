<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../../config/database.php';

// Fetch student details
$database = new Database();
$conn = $database->getConnection();

$query = "SELECT 
    s.*, 
    p.program_name,
    p.duration,
    l.description as level,
    sm.mode_name as study_mode,
    i.intake_description as intake,
    adm.admission_description as admission_type
FROM students s
LEFT JOIN programs p ON s.program_id = p.id
LEFT JOIN levels l ON p.level_id = l.id
LEFT JOIN study_modes sm ON s.study_mode_id = sm.id
LEFT JOIN intake i ON s.intake_id = i.id
LEFT JOIN admission_type adm ON s.admission_type = adm.id
WHERE s.id = :id";

$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $_SESSION['student_id']);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-color: #4A90E2;
            --secondary-color: #6c757d;
        }

        .sidebar {
            min-height: 100vh;
            background: #1a237e;
            padding: 1rem;
            color: white;
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

        .header {
            background: white;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .status-banner {
            background: #4CAF50;
            color: white;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 2rem;
        }

        .dashboard-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .dashboard-icon {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
        }

        .info-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .student-avatar {
            width: 100px;
            height: 100px;
            background: #4A90E2;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .detail-label {
            font-weight: 600;
            color: #666;
            margin-bottom: 5px;
        }

        .detail-value {
            color: #333;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar d-none d-md-block">
                <div class="d-flex flex-column">
                    <div class="text-center mb-4">
                        <img src="../../assets/icons/edernberg.png" alt="Logo" class="img-fluid mb-3"
                            style="max-width: 120px;">
                        <h5>Student Portal</h5>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a class="nav-link" href="program.php"><i class="bi bi-journal-text"></i>My Program </a>
                        <a class="nav-link" href="change-password.php">
                            <i class="bi bi-key"></i> Change Password
                        </a>
                        <a class="nav-link" href="#" onclick="logout()">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header -->
                <div class="header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Welcome, <?php echo htmlspecialchars($student['firstname']); ?>!</h4>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($student['student_id_number']); ?></p>
                    </div>
                    <button class="btn btn-primary">Make Online Payment</button>
                </div>

                <!-- Registration Status -->
                <div class="program-banner">
                    Registration Status : You are Registered for <?php echo htmlspecialchars($student['study_mode']); ?>
                    -
                    <?php echo date('Y'); ?>
                </div>

                <!-- Dashboard Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="dashboard-card" onclick="window.location.href='program.php'">
                            <i class="bi bi-journal-text dashboard-icon"></i>
                            <h6>My Programs</h6>
                            <p class="text-muted small">View your program details</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-card" onclick="window.location.href='finances.php'">
                            <i class="bi bi-cash-coin dashboard-icon"></i>
                            <h6>My Finances</h6>
                            <p class="text-muted small">View invoices and payments</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-card">
                            <i class="bi bi-house-door dashboard-icon"></i>
                            <h6>My Accommodation</h6>
                            <p class="text-muted small">Manage your accommodation</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dashboard-card">
                            <i class="bi bi-mortarboard dashboard-icon"></i>
                            <h6>Transcript</h6>
                            <p class="text-muted small">View academic results</p>
                        </div>
                    </div>
                </div>

                <!-- Second Row of Cards -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="dashboard-card">
                            <i class="bi bi-person-circle dashboard-icon"></i>
                            <h6>Profile</h6>
                            <p class="text-muted small">Manage your profile</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dashboard-card">
                            <i class="bi bi-book dashboard-icon"></i>
                            <h6>Learning Management</h6>
                            <p class="text-muted small">Access your courses</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dashboard-card">
                            <i class="bi bi-calendar3 dashboard-icon"></i>
                            <h6>Class Timetable</h6>
                            <p class="text-muted small">View your schedule</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function logout() {
            fetch('../../api/student/logout.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        window.location.href = 'login.php';
                    }
                })
                .catch(error => {
                    console.error('Logout failed:', error);
                });
        }
    </script>
</body>

</html>