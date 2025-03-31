<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../utils/Mailer.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    $required_fields = ['full_name', 'email', 'phone', 'school_id', 'program_id', 'recruiter_id'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    $database = new Database();
    $conn = $database->getConnection();

    // Insert into leads table
    $sql = "INSERT INTO leads (
                full_name, email, phone, school_id, program_id, 
                recruiter_id, source, created_at
            ) VALUES (
                :full_name, :email, :phone, :school_id, :program_id,
                :recruiter_id, :source, NOW()
            )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':full_name', $data['full_name']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':phone', $data['phone']);
    $stmt->bindParam(':school_id', $data['school_id']);
    $stmt->bindParam(':program_id', $data['program_id']);
    $stmt->bindParam(':recruiter_id', $data['recruiter_id']);
    $stmt->bindParam(':source', $data['source']);

    if ($stmt->execute()) {
        // Send email to lead
        $mailer = new Mailer();
        $mailer->sendLeadConfirmation(
            $data['email'],
            [
                'name' => $data['full_name'],
                'program' => getProgramName($conn, $data['program_id']),
                'school' => getSchoolName($conn, $data['school_id'])
            ]
        );

        // Send notification to recruiter
        $recruiterEmail = getRecruiterEmail($conn, $data['recruiter_id']);
        if ($recruiterEmail) {
            $mailer->sendLeadNotification(
                $recruiterEmail,
                [
                    'lead_name' => $data['full_name'],
                    'lead_email' => $data['email'],
                    'lead_phone' => $data['phone'],
                    'program' => getProgramName($conn, $data['program_id']),
                    'school' => getSchoolName($conn, $data['school_id'])
                ]
            );
        }

        echo json_encode([
            'status' => true,
            'message' => 'Lead created successfully'
        ]);
    } else {
        throw new Exception('Failed to create lead');
    }
} catch (Exception $e) {
    error_log("Lead creation error: " . $e->getMessage());
    echo json_encode([
        'status' => false,
        'message' => 'Failed to process your request. Please try again.'
    ]);
}

function getProgramName($conn, $id) {
    $stmt = $conn->prepare("SELECT program_name FROM programs WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn();
}

function getSchoolName($conn, $id) {
    $stmt = $conn->prepare("SELECT name FROM schools WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn();
}

function getRecruiterEmail($conn, $id) {
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn();
} 