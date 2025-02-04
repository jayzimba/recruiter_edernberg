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

    $query = "SELECT id, firstname, lastname, email, nrc_number, status 
              FROM users 
              WHERE role_id = (SELECT id FROM user_roles WHERE name = 'recruiter')
              ORDER BY firstname, lastname";
    
    $stmt = $conn->query($query);
    $recruiters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => true,
        'data' => $recruiters
    ]);
} catch (Exception $e) {
    error_log("Error fetching recruiters: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => 'Failed to fetch recruiters'
    ]);
} 