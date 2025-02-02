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
    $stmt = $conn->query("SELECT id, admission_description FROM admission_type ORDER BY admission_description");
    $admissionTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => true,
        'data' => $admissionTypes
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => false,
        'message' => 'Failed to fetch admission types'
    ]);
}
