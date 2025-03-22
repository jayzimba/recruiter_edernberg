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
    
    // Get lead ID from query parameter
    $leadId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($leadId <= 0) {
        throw new Exception('Invalid lead ID');
    }
    
    // Prepare query with proper joins
    $query = "SELECT 
                l.*,
                p.program_name as program_name,
                p.school_id,
                CONCAT(u.firstname, ' ', u.lastname) as recruiter_name,
                ls.name as source_name
              FROM leads l 
              LEFT JOIN programs p ON l.program_id = p.id 
              LEFT JOIN users u ON l.lead_recruiter_id = u.id
              LEFT JOIN lead_sources ls ON l.source = ls.name
              WHERE l.id = ?";
    
    // Execute query
    $stmt = $conn->prepare($query);
    $stmt->execute([$leadId]);
    
    // Fetch lead details
    $lead = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$lead) {
        throw new Exception('Lead not found');
    }

    // Get lead activities
    $activityQuery = "SELECT 
                        la.*,
                        CONCAT(u.firstname, ' ', u.lastname) as user_name
                     FROM lead_activities la
                     LEFT JOIN users u ON la.user_id = u.id
                     WHERE la.lead_id = ?
                     ORDER BY la.created_at DESC";
    
    $activityStmt = $conn->prepare($activityQuery);
    $activityStmt->execute([$leadId]);
    $activities = $activityStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get scheduled followups
    $followupQuery = "SELECT 
                        lf.*,
                        CONCAT(u.firstname, ' ', u.lastname) as user_name
                     FROM lead_followups lf
                     LEFT JOIN users u ON lf.user_id = u.id
                     WHERE lf.lead_id = ?
                     ORDER BY lf.followup_date DESC";
    
    $followupStmt = $conn->prepare($followupQuery);
    $followupStmt->execute([$leadId]);
    $followups = $followupStmt->fetchAll(PDO::FETCH_ASSOC);

    // Return success response with all related data
    echo json_encode([
        'status' => true,
        'data' => [
            'lead' => $lead,
            'activities' => $activities,
            'followups' => $followups
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
} 