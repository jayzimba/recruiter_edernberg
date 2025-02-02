<?php
$password = "Password@2025";
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

if (password_verify($password, $hash)) {
    echo "Password is valid!";
} else {
    echo "Password is invalid!";

    // Generate a new hash for comparison
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    echo "\nNew hash for Password@2025: " . $newHash;
}
