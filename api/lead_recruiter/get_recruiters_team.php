<?php
header('Content-Type: application/json');
require_once '../../middleware/Auth.php';
require_once '../../config/database.php';

try {
    // Check authentication
    $auth = new Auth();
    if (!$auth->isAuthenticated()) {
        throw new Exception('Unauthorized access');
    }

    // Get the current user's ID
    $currentUserId = $auth->getUserId();

    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();

    // Get recruiters (users with recruiter role) excluding current user
    $stmt = $conn->prepare("
        SELECT 
            u.id,
            CONCAT(u.firstname, ' ', u.lastname) AS name,
            u.email,
            u.nrc_number,
            u.status,
            u.firstname,
            u.lastname
        FROM 
            users u
        LEFT JOIN 
            user_roles ur ON u.id = ur.id
        ORDER BY 
            name ASC
    ");
    
    $stmt->execute();
    $recruiters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => true,
        'data' => $recruiters
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
} 