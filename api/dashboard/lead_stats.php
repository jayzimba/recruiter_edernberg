<?php
header('Content-Type: application/json');
require_once '../../middleware/Auth.php';
require_once '../../config/database.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    echo json_encode([
        'status' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Get total applications
    $totalQuery = "SELECT COUNT(*) as total FROM students";
    $stmt = $conn->query($totalQuery);
    $totalApplications = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get applications by status
    $statusQuery = "SELECT 
                        s.name as status,
                        COUNT(st.id) as count
                    FROM application_status s
                    LEFT JOIN students st ON s.id = st.application_status
                    GROUP BY s.id, s.name
                    ORDER BY s.id";
    $stmt = $conn->query($statusQuery);
    $applicationsByStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get paid up students
    $paidUpQuery = "SELECT
                            'Paid Up' AS status,
                        COUNT(st.id) AS count
                    FROM
                        students st
                    WHERE
                        st.student_status = 1
                    GROUP BY
                        st.id";
    $stmt = $conn->query($paidUpQuery);
    $paidUpStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get recent applications
    $recentQuery = "SELECT 
                        s.id,
                        s.firstname,
                        s.lastname,
                        s.email,
                        p.program_name,
                        ast.name as status,
                        u.firstname as recruiter_firstname,
                        u.lastname as recruiter_lastname,
                        s.created_at
                    FROM students s
                    LEFT JOIN programs p ON s.program_id = p.id
                    LEFT JOIN application_status ast ON s.application_status = ast.id
                    LEFT JOIN users u ON s.recruiter_id = u.id
                    ORDER BY s.created_at DESC
                    LIMIT 10";
    $stmt = $conn->query($recentQuery);
    $recentApplications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get top recruiters
    $recruitersQuery = "SELECT 
                            u.id,
                            u.firstname,
                            u.lastname,
                            COUNT(s.id) as applications_count
                        FROM users u
                        LEFT JOIN students s ON u.id = s.recruiter_id
                        WHERE u.role_id = (SELECT id FROM user_roles WHERE name = 'recruiter')
                        GROUP BY u.id
                        ORDER BY applications_count DESC";
    $stmt = $conn->query($recruitersQuery);
    $topRecruiters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get applications by program
    $programsQuery = "SELECT 
                        CONCAT(p.program_name, ' - ', sm.mode_name) as program_name,
                        COUNT(s.id) as count
                    FROM programs p
                    LEFT JOIN students s ON p.id = s.program_id
                    LEFT JOIN study_modes sm ON p.study_mode_id = sm.id
                    GROUP BY p.id
                    ORDER BY count DESC";
    $stmt = $conn->query($programsQuery);
    $applicationsByProgram = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => true,
        'data' => [
            'total_applications' => $totalApplications,
            'applications_by_status' => $applicationsByStatus,
            'recent_applications' => $recentApplications,
            'top_recruiters' => $topRecruiters,
            'applications_by_program' => $applicationsByProgram,
            'paid_up_students' => $paidUpStudents
        ]
    ]);
} catch (Exception $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => 'Failed to fetch dashboard statistics'
    ]);
}
