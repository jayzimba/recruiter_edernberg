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
    
    // Get today's follow-ups
    $query = "SELECT 
                lf.id,
                lf.lead_id,
                lf.followup_date,
                lf.notes,
                lf.status,
                l.name as lead_name,
                l.contact as lead_contact,
                l.email as lead_email,
                p.program_name as program_name
            FROM lead_followups lf
            LEFT JOIN leads l ON lf.lead_id = l.id
            LEFT JOIN programs p ON l.program_id = p.id
            WHERE DATE(lf.followup_date) = CURDATE()
            ORDER BY lf.followup_date ASC";
            
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $followups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => true,
        'message' => 'Follow-ups retrieved successfully',
        'data' => $followups
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => 'Failed to retrieve follow-ups: ' . $e->getMessage()
    ]);
} 