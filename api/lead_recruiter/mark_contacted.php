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
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $leadId = isset($data['leadId']) ? intval($data['leadId']) : 0;
    
    if ($leadId <= 0) {
        throw new Exception('Invalid lead ID');
    }
    
    // Begin transaction
    $conn->beginTransaction();
    
    // Update lead status
    $query = "UPDATE leads SET contacted = 1, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($query);
    $result = $stmt->execute([$leadId]);
    
    if (!$result) {
        throw new Exception('Failed to update lead status');
    }
    
    // Add activity record
    $activityQuery = "INSERT INTO lead_activities (lead_id, user_id, activity_type, description) 
                     VALUES (?, ?, 'status_changed', 'Lead marked as contacted')";
    $activityStmt = $conn->prepare($activityQuery);
    $activityResult = $activityStmt->execute([
        $leadId,
        $auth->getUserId() // Get current user ID
    ]);
    
    if (!$activityResult) {
        throw new Exception('Failed to record activity');
    }
    
    // Commit transaction
    $conn->commit();
    
    // Return success response
    echo json_encode([
        'status' => true,
        'message' => 'Lead marked as contacted successfully'
    ]);
    
} catch (Exception $e) {
    // Rollback transaction if there was an error
    if (isset($conn)) {
        $conn->rollBack();
    }
    
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
} 