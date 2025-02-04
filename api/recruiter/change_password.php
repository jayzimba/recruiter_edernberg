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

    if (!isset($data['current_password']) || !isset($data['new_password'])) {
        throw new Exception('All fields are required');
    }

    $result = $auth->changePassword(
        $_SESSION['user_id'], 
        $data['current_password'], 
        $data['new_password']
    );

    echo json_encode([
        'status' => true,
        'message' => 'Password updated successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
} 