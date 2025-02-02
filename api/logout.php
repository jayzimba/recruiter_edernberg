<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../middleware/Auth.php';

$auth = new Auth();
$result = $auth->logout();

// Clear JWT token from client side
echo json_encode([
    'status' => true,
    'message' => 'Logged out successfully',
    'clearToken' => true
]);
