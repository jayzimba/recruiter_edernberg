<?php
require_once __DIR__ . '/../controllers/AuthController.php';

// Set error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/error.log');

// Ensure proper JSON response
header('Content-Type: application/json');

try {
    $action = $_GET['action'] ?? '';
    $auth = new AuthController();

    switch ($action) {
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                exit;
            }

            // Log the received data for debugging
            error_log('Login attempt - POST data: ' . print_r($_POST, true));

            $auth->login();
            break;
        
        case 'logout':
            $auth->logout();
            break;
        
        case 'check':
            if ($auth->checkAuth()) {
                echo json_encode([
                    'success' => true,
                    'user' => $auth->getCurrentUser()
                ]);
            }
            break;
        
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    error_log('Auth error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred during authentication']);
}
?> 