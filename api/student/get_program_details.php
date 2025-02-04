<?php
header('Content-Type: application/json');
session_start();

require_once '../../config/database.php';

if (!isset($_SESSION['student_id'])) {
    echo json_encode([
        'status' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Fetch program details
    $query = "SELECT 
    s.student_id_number,
    s.firstname,
    p.program_name,
    p.id,
    p.duration,
    p.program_name as program_description,
    l.description as level,
    sm.mode_name as study_mode,
    ss.school_name as department_name,
    i.intake_description as intake,
    adm.admission_description as admission_type
FROM students s
LEFT JOIN programs p ON s.program_id = p.id
LEFT JOIN levels l ON p.level_id = l.id
LEFT JOIN study_modes sm ON s.study_mode_id = sm.id
LEFT JOIN schools ss ON p.school_id = ss.id
LEFT JOIN intake i ON s.intake_id = i.id
LEFT JOIN admission_type adm ON s.admission_type = adm.id
WHERE s.id = :id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_SESSION['student_id']);
    $stmt->execute();
    $program = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$program) {
        throw new Exception('Program details not found');
    }

    // Fetch program courses
    $coursesQuery = "SELECT 
    c.course_code,
    c.course_name,
    c.credits,
    c.description,
    s.semester_name,
    y.year_name
FROM program_courses pc
JOIN courses c ON pc.course_id = c.id
JOIN semesters s ON pc.semester_id = s.id
JOIN academic_years y ON pc.year_id = y.id
WHERE pc.program_id = (SELECT program_id FROM students WHERE id = :student_id)
ORDER BY y.year_name, s.semester_name";

    $stmt = $conn->prepare($coursesQuery);
    $stmt->bindParam(':student_id', $_SESSION['student_id']);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group courses by year and semester
    $groupedCourses = [];
    foreach ($courses as $course) {
        $groupedCourses[$course['year_name']][$course['semester_name']][] = $course;
    }

    echo json_encode([
        'status' => true,
        'data' => [
            'program' => $program,
            'courses' => $groupedCourses
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
