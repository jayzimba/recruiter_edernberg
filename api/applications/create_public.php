<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../utils/Mailer.php';

try {
    $data = json_decode(file_get_contents('php://input'));

    if (!isset($data->firstname) || !isset($data->lastname) || 
        !isset($data->email) || !isset($data->contact) || 
        !isset($data->program_id)) {
        throw new Exception('All fields are required');
    }

    $database = new Database();
    $conn = $database->getConnection();

    // Start transaction
    $conn->beginTransaction();

    // Insert application
    $query = "INSERT INTO students 
              (firstname, lastname, email, contact, program_id, 
               application_status, created_at)
              VALUES 
              (:firstname, :lastname, :email, :contact, :program_id,
               (SELECT id FROM application_status WHERE name = 'Pending'),
               NOW())";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':firstname', $data->firstname);
    $stmt->bindParam(':lastname', $data->lastname);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam(':contact', $data->contact);
    $stmt->bindParam(':program_id', $data->program_id);

    if (!$stmt->execute()) {
        throw new Exception('Failed to submit application');
    }

    // Send confirmation email
    $mailer = new Mailer();
    $mailer->sendApplicationConfirmation(
        $data->email,
        $data->firstname . ' ' . $data->lastname,
        'your selected program'
    );

    $conn->commit();

    echo json_encode([
        'status' => true,
        'message' => 'Application submitted successfully'
    ]);
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
} 