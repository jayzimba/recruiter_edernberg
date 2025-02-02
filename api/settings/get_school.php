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
        throw new Exception('School ID is required');
    }

    $database = new Database();
    $conn = $database->getConnection();

    $query = "SELECT * FROM schools WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();

    $school = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$school) {
        throw new Exception('School not found');
    }

    echo json_encode([
        'status' => true,
        'data' => $school
    ]);
} catch (Exception $e) {
    error_log("Error fetching school: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
