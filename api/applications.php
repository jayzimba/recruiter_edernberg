<?php
header('Content-Type: application/json');
require_once '../middleware/Auth.php';
require_once '../models/Application.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    echo json_encode([
        'status' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

$application = new Application();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        echo json_encode($application->createApplication($data));
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->application_id) && isset($data->status)) {
            echo json_encode($application->updateApplicationStatus(
                $data->application_id,
                $data->status,
                $data->commencement_date ?? null
            ));
        } else {
            echo json_encode([
                'status' => false,
                'message' => 'Missing required parameters'
            ]);
        }
        break;

    case 'GET':
        if (isset($_GET['id'])) {
            $result = $application->getApplicationDetails($_GET['id']);
            if ($result) {
                echo json_encode([
                    'status' => true,
                    'data' => $result
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => 'Application not found'
                ]);
            }
        }
        break;
}
