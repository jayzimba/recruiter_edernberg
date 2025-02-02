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
    if (!isset($_GET['id'])) {
        throw new Exception('Program ID is required');
    }

    $database = new Database();
    $conn = $database->getConnection();

    $query = "SELECT p.*, s.name as school_name 
              FROM programs p
              LEFT JOIN schools s ON p.school_id = s.id
              WHERE p.id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();

    $program = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$program) {
        throw new Exception('Program not found');
    }

    echo json_encode([
        'status' => true,
        'data' => $program
    ]);
} catch (Exception $e) {
    error_log("Error fetching program: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
