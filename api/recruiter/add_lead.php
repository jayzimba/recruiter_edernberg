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

    // Validate required fields
    $requiredFields = ['name', 'email', 'contact', 'source_id', 'program_id'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            throw new Exception("Missing or empty required field: {$field}");
        }
    }

    // Sanitize and validate input
    $name = filter_var(trim($input['name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL);
    $contact = filter_var(trim($input['contact']), FILTER_SANITIZE_STRING);
    $sourceId = filter_var($input['source_id'], FILTER_VALIDATE_INT);
    $programId = filter_var($input['program_id'], FILTER_VALIDATE_INT);
    $country = isset($input['country']) ? filter_var(trim($input['country']), FILTER_SANITIZE_STRING) : 'Kenya';

    if (!$email) {
        throw new Exception('Invalid email format');
    }

    if (!preg_match('/^\+\d+/', $contact)) {
        throw new Exception('Contact number must include country code');
    }

    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();

    // Start transaction
    $conn->beginTransaction();

    try {
        // Check if email or contact already exists
        $stmt = $conn->prepare("SELECT id FROM leads WHERE email = ? OR contact = ?");
        $stmt->execute([$email, $contact]);
        if ($stmt->fetch()) {
            throw new Exception('A lead with this email or contact already exists');
        }

        // Get source name from lead_sources table
        $stmt = $conn->prepare("SELECT name FROM lead_sources WHERE id = ?");
        $stmt->execute([$sourceId]);
        $sourceResult = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$sourceResult) {
            throw new Exception('Invalid source selected');
        }
        
        $sourceName = $sourceResult['name'];

        // Insert the lead with source name instead of ID
        $stmt = $conn->prepare("
            INSERT INTO leads (
                name, email, contact, country, program_id, source, 
                lead_recruiter_id, created_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, NOW()
            )
        ");

        $stmt->execute([
            $name, $email, $contact, $country, $programId, $sourceName, $userId
        ]);

        $leadId = $conn->lastInsertId();

        // Add activity log
        $stmt = $conn->prepare("
            INSERT INTO lead_activities (
                lead_id, user_id, activity_type, description, created_at
            ) VALUES (
                ?, ?, 'created', 'Lead created', NOW()
            )
        ");
        $stmt->execute([$leadId, $userId]);

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'status' => true,
            'message' => 'Lead added successfully',
            'data' => ['lead_id' => $leadId]
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