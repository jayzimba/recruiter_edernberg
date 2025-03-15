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

    if (!isset($data['id'])) {
        throw new Exception('Recruiter ID is required');
    }

    $database = new Database();
    $conn = $database->getConnection();

    // Check if recruiter exists and is actually a recruiter
    $query = "SELECT id FROM users 
              WHERE id = :id AND role_id = (SELECT id FROM user_roles WHERE name = 'recruiter')";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $data['id']);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        throw new Exception('Recruiter not found');
    }

    // Check if recruiter has any applications
    $query = "SELECT COUNT(*) as count FROM students WHERE recruiter_id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $data['id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        // Instead of deleting, deactivate the recruiter
        $query = "UPDATE users SET status = 0 WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $data['id']);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to deactivate recruiter');
        }

        echo json_encode([
            'status' => true,
            'message' => 'Recruiter has been deactivated due to existing applications'
        ]);
    } else {
        // Delete the recruiter if no applications exist
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $data['id']);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to delete recruiter');
        }

        echo json_encode([
            'status' => true,
            'message' => 'Recruiter deleted successfully'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
} 