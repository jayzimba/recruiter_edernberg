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

    // Get the authenticated user's ID
    $userId = $auth->getUserId();

    // Get and decode the input data
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['leads']) || !isset($input['recruiter_id']) || 
        empty($input['leads']) || empty($input['recruiter_id'])) {
        throw new Exception('Missing required fields');
    }

    $leads = array_map('intval', $input['leads']);
    $recruiterId = filter_var($input['recruiter_id'], FILTER_VALIDATE_INT);

    if (!$recruiterId) {
        throw new Exception('Invalid recruiter ID');
    }

    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();

    // Start transaction
    $conn->beginTransaction();

    try {
        // Update leads
        $placeholders = str_repeat('?,', count($leads) - 1) . '?';
        $params = array_merge($leads, [$recruiterId]);
        
        $stmt = $conn->prepare("
            UPDATE leads 
            SET lead_recruiter_id = ?, 
                updated_at = NOW() 
            WHERE id IN ($placeholders)
        ");
        
        $stmt->execute([$recruiterId, ...$leads]);

        // Log activity for each lead
        $stmt = $conn->prepare("
            INSERT INTO lead_activities (
                lead_id, user_id, activity_type, description, created_at
            ) VALUES (
                ?, ?, 'assigned', 'Lead assigned to recruiter', NOW()
            )
        ");

        foreach ($leads as $leadId) {
            $stmt->execute([$leadId, $userId]);
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'status' => true,
            'message' => 'Leads assigned successfully'
        ]);

    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
} 