<?php
$password = "Password@2025";
$stored_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

// Test the stored hash
echo "Testing stored hash:\n";
var_dump(password_verify($password, $stored_hash));

// Create a new hash and verify it
echo "\nTesting new hash:\n";
$new_hash = password_hash($password, PASSWORD_DEFAULT);
echo "New hash: " . $new_hash . "\n";
var_dump(password_verify($password, $new_hash));

// Compare the hashes
echo "\nComparing hashes:\n";
echo "Stored hash length: " . strlen($stored_hash) . "\n";
echo "New hash length: " . strlen($new_hash) . "\n";
echo "Stored hash: " . $stored_hash . "\n";
echo "New hash: " . $new_hash . "\n";

// Try creating a user with a new hash
require_once '../config/database.php';
$database = new Database();
$conn = $database->getConnection();

try {
    // Create test user with new hash
    $query = "INSERT INTO users (
        firstname, lastname, nrc_number, email, phone_number, password, role_id
    ) VALUES (
        'Test2', 'User2', 'TEST789/45/6', 'test2@example.com', '+260123456789',
        :password, (SELECT id FROM user_roles WHERE name = 'recruiter')
    )";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':password', $new_hash);
    $stmt->execute();

    echo "\nTest user created with new hash. Try logging in with:\n";
    echo "Email: test2@example.com\n";
    echo "Password: Password@2025\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
