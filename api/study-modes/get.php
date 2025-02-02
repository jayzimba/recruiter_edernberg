<?php
header('Content-Type: application/json');
require_once '../../middleware/Auth.php';
require_once '../../models/StudyMode.php';

$auth = new Auth();
if (!$auth->isAuthenticated()) {
    echo json_encode([
        'status' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

$studyMode = new StudyMode();
$modes = $studyMode->getAllModes();

echo json_encode([
    'status' => true,
    'data' => $modes
]);
