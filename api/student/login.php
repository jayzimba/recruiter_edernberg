<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['student_id']) || !isset($data['password'])) {
        throw new Exception('Missing required fields');
    }

    $database = new Database();
    $conn = $database->getConnection();

    $query = "SELECT id, firstname, lastname, student_id_number, email, password, password_changed 
              FROM students 
              WHERE student_id_number = :student_id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':student_id', $data['student_id']);
    $stmt->execute();

    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        throw new Exception('Invalid student ID or password');
    }

    // Check if password is correct
    if (($student['password_changed'] == 0 && $data['password'] === 'Password@2025') ||
        ($student['password_changed'] == 1 && password_verify($data['password'], $student['password']))
    ) {
        // Start session and store student data
        session_start();
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_name'] = $student['firstname'] . ' ' . $student['lastname'];
        $_SESSION['student_number'] = $student['student_id_number'];

        echo json_encode([
            'status' => true,
            'message' => 'Login successful'
        ]);
    } else {
        throw new Exception('Invalid student ID or password');
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
