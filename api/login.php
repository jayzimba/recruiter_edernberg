<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../middleware/Auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->email) && !empty($data->password)) {
        $auth = new Auth();
        $result = $auth->login($data->email, $data->password);

        echo json_encode($result);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'Email and password are required'
        ]);
    }
}
