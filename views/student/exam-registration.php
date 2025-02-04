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
    sm.mode_name as study_mode
FROM students s
LEFT JOIN programs p ON s.program_id = p.id
LEFT JOIN levels l ON p.level_id = l.id
LEFT JOIN study_modes sm ON s.study_mode_id = sm.id
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
    <title>Exam Registration</title>
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
            padding: 2rem;
        }

        .exam-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .student-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .info-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
        }

        .info-item strong {
            color: #1a237e;
            display: block;
            margin-bottom: 0.5rem;
        }

        .payment-status {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 2rem;
        }

        .exam-slip-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .exam-slip-btn:hover {
            background: #357abd;
            transform: translateY(-2px);
        }

        .progress {
            height: 8px;
            margin-bottom: 1rem;
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
                <h2 class="mb-4">Exam Management</h2>

                <div class="exam-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4><?php echo htmlspecialchars($student['program_name']); ?></h4>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars($student['study_mode']); ?></p>
                        </div>
                        <div class="text-end">
                            <p class="mb-1">Payment Percentage</p>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%" 
                                     aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-success">100%</small>
                        </div>
                    </div>

                    <div class="alert alert-success">
                        <strong>Download Status:</strong> Approved, you can download your Examination Slip
                    </div>

                    <h5 class="mb-3">Courses to be examined</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 15%;">Course Code</th>
                                    <th style="width: 85%;">Course Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>BIT 4421</td>
                                    <td>Entrepreneurship</td>
                                </tr>
                                <tr>
                                    <td>BIT 4400</td>
                                    <td>Project</td>
                                </tr>
                                <tr>
                                    <td>BIT 4410</td>
                                    <td>IT Project Management</td>
                                </tr>
                                <tr>
                                    <td>BIT 4411</td>
                                    <td>E-Commerce and ERP</td>
                                </tr>
                                <tr>
                                    <td>BIT 4420</td>
                                    <td>Enterprise Architecture</td>
                                </tr>
                                <tr>
                                    <td>BIT 4430</td>
                                    <td>Social and Ethical Issues</td>
                                </tr>
                                <tr>
                                    <td>BIT 4440</td>
                                    <td>Cloud Computing & Big Data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class=" mt-4">
                        <button class="exam-slip-btn" onclick="downloadExamSlip()">
                            <i class="bi bi-download me-2"></i>Download Exam Slip
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function downloadExamSlip() {
            window.location.href = '../../api/student/download_exam_slip.php';
        }

        function logout() {
            fetch('../../api/student/logout.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        window.location.href = 'login.php';
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>

</html> 