<?php
require_once '../models/Student.php';

header('Content-Type: application/json');

// Check if course code is provided
if (!isset($_GET['course_code'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Course code is required']);
    exit;
}

try {
    $student = new Student();
    $course_code = $_GET['course_code'];
    
    // Get course details using the Student model method
    $course_details = $student->getCourseDetails($course_code);
    
    if ($course_details) {
        // Format dates
        $course_details['start_date'] = date('M d, Y', strtotime($course_details['start_date']));
        $course_details['end_date'] = date('M d, Y', strtotime($course_details['end_date']));
        
        echo json_encode($course_details);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Course not found']);
    }
} catch (Exception $e) {
    error_log("Error in get_course_details.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?> 