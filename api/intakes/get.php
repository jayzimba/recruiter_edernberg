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
    $stmt = $conn->query("SELECT id, intake_description FROM intake ORDER BY intake_description");
    $intakes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => true,
        'data' => $intakes
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => false,
        'message' => 'Failed to fetch intakes'
    ]);
}
