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
    
    // Fetch lead sources
    $query = "SELECT id, name FROM lead_sources WHERE is_active = 1 ORDER BY name ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $sources = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sources[] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }
    
    echo json_encode([
        'status' => true,
        'data' => $sources
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => 'Failed to fetch lead sources: ' . $e->getMessage()
    ]);
} 