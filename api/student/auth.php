<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $student_id = filter_var($_POST['student_id'], FILTER_SANITIZE_NUMBER_INT);
    $password = $_POST['password'];

    try {
        $query = "SELECT s.id, s.student_id, s.password, s.full_name, s.status,
                         p.program_name, sc.school_name
                  FROM students s
                  LEFT JOIN programs p ON s.program_id = p.id
                  LEFT JOIN schools sc ON p.school_id = sc.id
                  WHERE s.student_id = :student_id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $student = $stmt->fetch();
            
            if (password_verify($password, $student['password'])) {
                if ($student['status'] == 1) {
                    // Store student data in session
                    $_SESSION['student_id'] = $student['id'];
                    $_SESSION['student_name'] = $student['full_name'];
                    $_SESSION['program_name'] = $student['program_name'];
                    $_SESSION['school_name'] = $student['school_name'];
                    
                    header("Location: ../../views/student/dashboard.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Your account is inactive. Please contact support.";
                }
            } else {
                $_SESSION['error'] = "Invalid Student ID or password";
            }
        } else {
            $_SESSION['error'] = "Invalid Student ID or password";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "System error. Please try again later.";
        // Log the error for debugging
        error_log("Login error: " . $e->getMessage());
    }

    header("Location: ../../views/student/");
    exit();
} 