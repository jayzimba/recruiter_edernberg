<?php
require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

try {
    $password = "Password@2025";
    $hashed_password = hash('sha256', $password);

    $query = "UPDATE users SET password = :password WHERE email = 'john.doe@example.com'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':password', $hashed_password);

    if ($stmt->execute()) {
        echo "Password updated successfully!\n";
        echo "Email: john.doe@example.com\n";
        echo "Password: Password@2025\n";
        echo "Hash: $hashed_password\n";
    } else {
        echo "Failed to update password";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
