<?php
header('Content-Type: application/json');
require_once '../models/Student.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Get JSON data from request
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['action']) || !isset($data['course_code'])) {
        throw new Exception('Missing required parameters');
    }

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if student is logged in
    if (!isset($_SESSION['student_id'])) {
        throw new Exception('Unauthorized access');
    }

    $student = new Student();
    $studentId = $_SESSION['student_id'];
    $courseCode = $data['course_code'];
    $action = $data['action'];

    if ($action === 'add') {
        // Check if student is already registered for this course
        if ($student->isRegisteredForCourse($studentId, $courseCode)) {
            throw new Exception('Already registered for this course');
        }

        // Register the course
        $result = $student->registerCourse($studentId, $courseCode);
        if ($result) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Course registered successfully'
            ]);
        } else {
            throw new Exception('Failed to register course');
        }
    } else if ($action === 'drop') {
        // Drop the course
        $result = $student->dropCourse($studentId, $courseCode);
        if ($result) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Course dropped successfully'
            ]);
        } else {
            throw new Exception('Failed to drop course');
        }
    } else {
        throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 