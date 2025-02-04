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
        throw new Exception('Recruiter ID is required');
    }

    $database = new Database();
    $conn = $database->getConnection();

    $query = "SELECT id, firstname, lastname, email, nrc_number, status 
              FROM users 
              WHERE id = :id AND role_id = (SELECT id FROM user_roles WHERE name = 'recruiter')";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();

    $recruiter = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recruiter) {
        throw new Exception('Recruiter not found');
    }

    echo json_encode([
        'status' => true,
        'data' => $recruiter
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
} 