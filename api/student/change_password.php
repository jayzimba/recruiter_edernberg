<?php
header('Content-Type: application/json');
session_start();

require_once '../../config/database.php';

if (!isset($_SESSION['student_id'])) {
    echo json_encode([
        'status' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['current_password']) || !isset($data['new_password'])) {
        throw new Exception('Missing required fields');
    }

    // Validate current password
    if ($data['current_password'] !== 'Password@2025') {
        throw new Exception('Current password is incorrect');
    }

    // Validate new password
    if (strlen($data['new_password']) < 8) {
        throw new Exception('Password must be at least 8 characters long');
    }
    if (!preg_match('/[A-Z]/', $data['new_password'])) {
        throw new Exception('Password must contain at least one uppercase letter');
    }
    if (!preg_match('/[a-z]/', $data['new_password'])) {
        throw new Exception('Password must contain at least one lowercase letter');
    }
    if (!preg_match('/[0-9]/', $data['new_password'])) {
        throw new Exception('Password must contain at least one number');
    }
    if (!preg_match('/[^A-Za-z0-9]/', $data['new_password'])) {
        throw new Exception('Password must contain at least one special character');
    }

    $database = new Database();
    $conn = $database->getConnection();

    // Hash the new password
    $hashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);

    // Update password in database
    $query = "UPDATE students 
             SET password = :password,
                 password_changed = 1,
                 updated_at = NOW()
             WHERE id = :id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':id', $_SESSION['student_id']);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => true,
            'message' => 'Password updated successfully. Please use your new password for future logins.'
        ]);
    } else {
        throw new Exception('Failed to update password');
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
