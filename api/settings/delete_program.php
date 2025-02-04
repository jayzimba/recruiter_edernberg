<?php
header('Content-Type: application/json');
require_once '../../middleware/Auth.php';
require_once '../../config/database.php';

$auth = new Auth();
if (!$auth->isAuthenticated() || $_SESSION['user_role'] !== 'lead_recruiter') {
    echo json_encode([
        'status' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

try {
    if (!isset($_POST['id'])) {
        throw new Exception('Program ID is required');
    }

    $database = new Database();
    $conn = $database->getConnection();

    // Check if program has associated students
    $checkQuery = "SELECT COUNT(*) as count FROM students WHERE program_id = :id";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bindParam(':id', $_POST['id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        throw new Exception('Cannot delete program with enrolled students');
    }

    // Delete program
    $query = "DELETE FROM programs WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_POST['id']);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => true,
            'message' => 'Program deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete program');
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
} 