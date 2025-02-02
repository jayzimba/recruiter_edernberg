<?php
require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

try {
    // First ensure roles exist
    $roles_query = "INSERT INTO user_roles (name) VALUES 
                   ('Admin'), ('recruiter'), ('lead_recruiter')
                   ON DUPLICATE KEY UPDATE name = VALUES(name)";
    $conn->exec($roles_query);

    // Create test user with SHA-256 hashed password
    $password = "Password@2025";
    $hashed_password = hash('sha256', $password);

    $query = "INSERT INTO users (
        firstname, lastname, nrc_number, email, phone_number, password, role_id
    ) VALUES (
        'Test', 'User', 'TEST123/45/6', 'test@example.com', '+260123456789',
        :password, (SELECT id FROM user_roles WHERE name = 'recruiter')
    ) ON DUPLICATE KEY UPDATE password = :password";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->execute();

    echo "Test user created successfully!\n";
    echo "Email: test@example.com\n";
    echo "Password: Password@2025\n";
    echo "Hash: $hashed_password\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
