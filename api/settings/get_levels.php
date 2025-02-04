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

    $query = "SELECT id, description FROM levels ORDER BY description";
    $stmt = $conn->query($query);
    $levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => true,
        'data' => $levels
    ]);
} catch (Exception $e) {
    error_log("Error fetching levels: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => 'Failed to fetch levels'
    ]);
} 