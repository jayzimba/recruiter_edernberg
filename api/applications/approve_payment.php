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
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['application_id'])) {
        throw new Exception('Missing application ID');
    }

    $database = new Database();
    $conn = $database->getConnection();

    // Start transaction
    $conn->beginTransaction();

    // Update student status to Paid
    $updateQuery = "UPDATE students 
                   SET student_status = 1
                   WHERE id = :id";

    $stmt = $conn->prepare($updateQuery);
    $stmt->bindParam(':id', $data['application_id']);

    if (!$stmt->execute()) {
        throw new Exception('Failed to update payment status');
    }

    $conn->commit();

    echo json_encode([
        'status' => true,
        'message' => 'Payment approved successfully'
    ]);
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    error_log("Error approving payment: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
