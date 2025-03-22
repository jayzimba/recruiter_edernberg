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
    
    // Validate input data
    if (!isset($data['leadId']) || !isset($data['followupDate']) || !isset($data['notes'])) {
        throw new Exception('Missing required fields: leadId, followupDate, or notes');
    }
    
    $leadId = intval($data['leadId']);
    $followupDate = trim($data['followupDate']);
    $notes = trim($data['notes']);
    $userId = $auth->getUserId();
    
    if ($leadId <= 0) {
        throw new Exception('Invalid lead ID');
    }
    
    // Validate date format
    $date = DateTime::createFromFormat('Y-m-d H:i', $followupDate);
    if (!$date) {
        throw new Exception('Invalid date format. Expected format: YYYY-MM-DD HH:mm');
    }
    
    // Begin transaction
    $conn->beginTransaction();
    
    try {
        // Insert follow-up
        $query = "INSERT INTO lead_followups (lead_id, user_id, followup_date, notes, status, created_at) 
                 VALUES (?, ?, ?, ?, 'pending', NOW())";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([
            $leadId,
            $userId,
            $followupDate,
            $notes
        ]);
        
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception('Failed to create follow-up: ' . print_r($errorInfo, true));
        }
        
        // Add activity record
        $activityQuery = "INSERT INTO lead_activities (lead_id, user_id, activity_type, description) 
                         VALUES (?, ?, 'schedule_added', ?)";
        $activityStmt = $conn->prepare($activityQuery);
        $activityDescription = "Follow-up scheduled for " . $followupDate;
        $activityResult = $activityStmt->execute([
            $leadId,
            $userId,
            $activityDescription
        ]);
        
        if (!$activityResult) {
            $errorInfo = $activityStmt->errorInfo();
            throw new Exception('Failed to record activity: ' . print_r($errorInfo, true));
        }
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'status' => true,
            'message' => 'Follow-up scheduled successfully',
            'data' => [
                'leadId' => $leadId,
                'followupDate' => $followupDate,
                'notes' => $notes
            ]
        ]);
        
    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => 'Failed to schedule follow-up: ' . $e->getMessage()
    ]);
} 