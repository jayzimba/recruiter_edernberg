<?php
session_start();
require_once '../../config/database.php';
require_once '../../utils/DocumentProcessor.php';

if (!isset($_SESSION['student_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Fetch student and exam details
    $query = "SELECT 
        s.*, 
        p.program_name,
        p.duration,
        l.description as level
    FROM students s
    LEFT JOIN programs p ON s.program_id = p.id
    LEFT JOIN levels l ON p.level_id = l.id
    WHERE s.id = :id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_SESSION['student_id']);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // Generate exam slip using DocumentProcessor
    $doc = new DocumentProcessor();
    $examSlip = $doc->generateExamSlip($student);

    // Set headers for download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="exam_slip.pdf"');
    
    // Output the PDF
    echo $examSlip;
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => false,
        'message' => 'Failed to generate exam slip: ' . $e->getMessage()
    ]);
} 