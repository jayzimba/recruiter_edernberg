<?php
require_once __DIR__ . '/../controllers/ProfileController.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$profile = new ProfileController();

switch ($action) {
    case 'get':
        $profile->getProfile();
        break;
    
    case 'update':
        $profile->updateProfile();
        break;
    
    case 'courses':
        $profile->getCourses();
        break;
    
    case 'financial':
        $profile->getFinancialStatus();
        break;
    
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?> 