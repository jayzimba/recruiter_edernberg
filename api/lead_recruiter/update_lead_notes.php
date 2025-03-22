<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
    // Get raw POST data and log it
    $raw_data = file_get_contents('php://input');
    error_log("Received raw data: " . $raw_data);
    
    // Decode JSON data
    $data = json_decode($raw_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data: ' . json_last_error_msg());
    }
    
    // Log decoded data
    error_log("Decoded data: " . print_r($data, true));
    
    // Validate input data
    if (!isset($data['leadId']) || !isset($data['notes'])) {
        throw new Exception('Missing required fields: ' . 
            (!isset($data['leadId']) ? 'leadId ' : '') . 
            (!isset($data['notes']) ? 'notes' : '')
        );
    }
    
    $database = new Database();
    $conn = $database->getConnection();
    
    $leadId = intval($data['leadId']);
    $notes = trim($data['notes']);
    $userId = $auth->getUserId();
    
    error_log("Processing update for Lead ID: $leadId, User ID: $userId");
    
    if ($leadId <= 0) {
        throw new Exception('Invalid lead ID');
    }
    
    // Verify lead exists
    $checkQuery = "SELECT id FROM leads WHERE id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->execute([$leadId]);
    
    if ($checkStmt->rowCount() === 0) {
        throw new Exception('Lead not found');
    }
    
    // Begin transaction
    $conn->beginTransaction();
    
    try {
        // Update lead notes
        $query = "UPDATE leads SET notes = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([$notes, $leadId]);
        
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception('Failed to update lead notes: ' . print_r($errorInfo, true));
        }
        
        // Add activity record with shorter activity_type
        $activityQuery = "INSERT INTO lead_activities (lead_id, user_id, activity_type, description) 
                         VALUES (?, ?, 'note_added', ?)";
        $activityStmt = $conn->prepare($activityQuery);
        $activityDescription = "Notes updated: " . (strlen($notes) > 50 ? substr($notes, 0, 47) . '...' : $notes);
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
            'message' => 'Notes updated successfully',
            'data' => [
                'leadId' => $leadId,
                'notes' => $notes
            ]
        ]);
        
    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Error in update_lead_notes.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    echo json_encode([
        'status' => false,
        'message' => 'Failed to save notes: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}