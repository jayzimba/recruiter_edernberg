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
    
    if (!isset($data['followupId']) || !isset($data['status'])) {
        throw new Exception('Missing required fields');
    }
    
    $followupId = intval($data['followupId']);
    $status = trim($data['status']);
    $userId = $auth->getUserId();
    
    if (!in_array($status, ['completed', 'cancelled', 'pending'])) {
        throw new Exception('Invalid status value');
    }
    
    // Begin transaction
    $conn->beginTransaction();
    
    try {
        // Update follow-up status
        $query = "UPDATE lead_followups 
                 SET status = ?, updated_at = NOW(), updated_by = ? 
                 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([$status, $userId, $followupId]);
        
        if (!$result) {
            throw new Exception('Failed to update follow-up status');
        }
        
        // Get follow-up details for activity log
        $followupQuery = "SELECT lf.*, l.name as lead_name 
                         FROM lead_followups lf
                         LEFT JOIN leads l ON lf.lead_id = l.id
                         WHERE lf.id = ?";
        $followupStmt = $conn->prepare($followupQuery);
        $followupStmt->execute([$followupId]);
        $followup = $followupStmt->fetch(PDO::FETCH_ASSOC);
        
        // Add activity record
        $activityQuery = "INSERT INTO lead_activities (lead_id, user_id, activity_type, description) 
                         VALUES (?, ?, 'schedule_updated', ?)";
        $activityStmt = $conn->prepare($activityQuery);
        $activityDescription = "Follow-up marked as {$status} for {$followup['lead_name']}";
        $activityResult = $activityStmt->execute([
            $followup['lead_id'],
            $userId,
            $activityDescription
        ]);
        
        if (!$activityResult) {
            throw new Exception('Failed to record activity');
        }
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'status' => true,
            'message' => 'Follow-up status updated successfully'
        ]);
        
    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => 'Failed to update follow-up status: ' . $e->getMessage()
    ]);
} 