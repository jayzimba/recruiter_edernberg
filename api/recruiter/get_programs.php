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

    // Get all active programs
    $query = "
        SELECT 
            p.id,
            p.program_name as name,
            l.description as level,
            p.duration,
            s.school_name as school_name
        FROM programs p
        LEFT JOIN schools s ON p.school_id = s.id
        LEFT JOIN levels l ON p.level_id = l.id
        ORDER BY p.program_name ASC
    ";

    $stmt = $conn->prepare($query);
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