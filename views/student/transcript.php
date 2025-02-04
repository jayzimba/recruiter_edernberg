<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../../config/database.php';

// Fetch student details and transcript
$database = new Database();
$conn = $database->getConnection();

$query = "SELECT 
    s.*, 
    p.program_name,
    l.description as level
FROM students s
LEFT JOIN programs p ON s.program_id = p.id
LEFT JOIN levels l ON p.level_id = l.id
WHERE s.id = :id";

$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $_SESSION['student_id']);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch courses and grades
$query = "SELECT 
    c.course_code,
    c.course_name,
    g.grade
FROM student_grades g
JOIN courses c ON g.course_id = c.id
WHERE g.student_id = :student_id
ORDER BY c.course_code";

$stmt = $conn->prepare($query);
$stmt->bindParam(':student_id', $_SESSION['student_id']);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Transcript</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <style>
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

        .transcript-container {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }



        .transcript-warning {
            font-size: 0.9rem;
            color: #666;
            font-style: italic;
            margin-bottom: 2rem;
        }

        .student-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .student-info-item {
            margin-bottom: 0.5rem;
        }

        .transcript-table {
            width: 100%;
            margin-bottom: 2rem;
        }

        .transcript-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            padding: 0.75rem;
        }

        .transcript-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #eee;
        }

        .transcript-table td:nth-child(2) {
            text-align: left;
        }

        .grade-column {
            text-align: center;
            font-weight: 600;
        }

        .comment-row {
            background-color: #f8f9fa;
        }

        @media print {

            .sidebar,
            .btn-print {
                display: none;
            }

            .transcript-container {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="d-flex flex-column">
                    <div class="text-center mb-4">
                        <img src="../../assets/icons/edernberg.png" alt="Logo" class="img-fluid mb-3" style="max-width: 120px;">
                        <h5>Student Portal</h5>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a class="nav-link" href="program.php">
                            <i class="bi bi-journal-text"></i> My Program
                        </a>
                        <a class="nav-link active" href="transcript.php">
                            <i class="bi bi-file-text"></i> Transcript
                        </a>
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
            <div class="col-md-9 col-lg-10 main-content p-4">

                <!-- Registration Status -->
                <div class="program-banner">
                    <div class="d-flex justify-content-between">
                        <div class="text-center d-flex flex-column justify-content-center">
                            <div class="fs-4 fw-bold text-white-50 mb-1">Academic Transcript</div>
                        </div>
                        <div class="text-center d-flex flex-column justify-content-center">
                            <button class="btn btn-primary btn-print" onclick="window.print()">
                                <i class="bi bi-printer"></i> Print Transcript
                            </button>
                        </div>
                    </div>
                   

                    <p class="text-white">
                        This transcript may not include all courses required for your program completion. Please verify with the Academics Office.
                    </p>
                </div>



                <div class="transcript-container">

                    <div class="student-info">
                        <div>
                            <div class="student-info-item">
                                <strong>Student Name:</strong> <?php echo htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?>
                            </div>
                            <div class="student-info-item">
                                <strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id_number']); ?>
                            </div>
                        </div>
                        <div>
                            <div class="student-info-item">
                                <strong>Current Program:</strong> <?php echo htmlspecialchars($student['program_name']); ?>
                            </div>
                            <div class="student-info-item">
                                <strong>Current Level / Year of Study:</strong> Year <?php echo htmlspecialchars($student['current_year']); ?>
                            </div>
                        </div>
                    </div>

                    <table class="transcript-table table table-hover table-responsive table-sm">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th class="text-center">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr class="fs-6 fw-300 text-muted">
                                    <td style="width: 15%;"><?php echo htmlspecialchars($course['course_code']); ?></td>
                                    <td style="width: 75%;"><?php echo htmlspecialchars($course['course_name']); ?></td>
                                    <td class="text-center" style="width: 20%;"><?php echo htmlspecialchars($course['grade']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="comment-row">
                                <td><strong>Comment</strong></td>
                                <td colspan="2">Clear Pass</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function logout() {
            fetch('../../api/logout.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        window.location.href = '../login.php';
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>

</html>