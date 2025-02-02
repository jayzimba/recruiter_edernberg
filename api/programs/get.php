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

$database = new Database();
$conn = $database->getConnection();

try {
    $school_id = isset($_GET['school_id']) ? intval($_GET['school_id']) : 0;

    if ($school_id <= 0) {
        throw new Exception('Invalid school ID');
    }

    $query = "SELECT id, program_name FROM programs WHERE
               school_id = :school_id 
              ORDER BY program_name";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':school_id', $school_id);
    $stmt->execute();

    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => true,
        'data' => $programs
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
