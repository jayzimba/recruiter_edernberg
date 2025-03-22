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

    $query = "SELECT * FROM study_modes ORDER BY mode_name";
    $stmt = $conn->query($query);
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => true,
        'data' => $programs
    ]);
} catch (Exception $e) {
    error_log("Error fetching programs: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => 'Failed to fetch programs'
    ]);
}
