<?php
require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

try {
    $password = "Password@2025";
    $new_hash = password_hash($password, PASSWORD_DEFAULT);

    $query = "UPDATE users SET password = :password WHERE email = 'john.doe@example.com'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':password', $new_hash);

    if ($stmt->execute()) {
        echo "Password updated successfully!\n";
        echo "New hash: " . $new_hash . "\n";
        echo "Try logging in with:\n";
        echo "Email: john.doe@example.com\n";
        echo "Password: Password@2025\n";

        // Verify the hash
        echo "\nVerifying hash:\n";
        var_dump(password_verify($password, $new_hash));
    } else {
        echo "Failed to update password";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
