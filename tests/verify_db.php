<?php
require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

try {
    // Check user_roles table
    $query = "SELECT * FROM user_roles";
    $stmt = $conn->query($query);
    echo "User Roles:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }

    // Check users table
    $query = "SELECT u.*, ur.name as role_name 
             FROM users u 
             LEFT JOIN user_roles ur ON u.role_id = ur.id 
             WHERE u.email = 'john.doe@example.com'";
    $stmt = $conn->query($query);
    echo "\nUser Data:\n";
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($userData);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
