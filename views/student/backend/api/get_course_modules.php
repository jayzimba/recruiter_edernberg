<?php
session_start();
require_once '../models/Student.php';

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['course_code'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Course code is required']);
    exit();
}

$student = new Student();
$modules = $student->getCourseModules($_GET['course_code']);

echo json_encode(['modules' => $modules]);
?> 