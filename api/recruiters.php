<?php
header('Content-Type: application/json');
require_once '../middleware/Auth.php';
require_once '../models/Recruiter.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    echo json_encode([
        'status' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Only admin can manage recruiters
if ($_SESSION['user_role'] !== 'Admin') {
    echo json_encode([
        'status' => false,
        'message' => 'Permission denied'
    ]);
    exit;
}

$recruiter = new Recruiter();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        // Validate required fields
        $required_fields = ['firstname', 'lastname', 'nrc_number', 'email', 'phone_number', 'role'];
        foreach ($required_fields as $field) {
            if (!isset($data->$field) || empty($data->$field)) {
                echo json_encode([
                    'status' => false,
                    'message' => ucfirst($field) . ' is required'
                ]);
                exit;
            }
        }

        // Validate email format
        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'status' => false,
                'message' => 'Invalid email format'
            ]);
            exit;
        }

        // Validate role
        if (!in_array($data->role, ['recruiter', 'lead_recruiter'])) {
            echo json_encode([
                'status' => false,
                'message' => 'Invalid role specified'
            ]);
            exit;
        }

        echo json_encode($recruiter->createRecruiter($data));
        break;

    case 'GET':
        $result = $recruiter->getAllRecruiters();
        if ($result) {
            echo json_encode([
                'status' => true,
                'data' => $result
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'message' => 'Failed to fetch recruiters'
            ]);
        }
        break;
} 