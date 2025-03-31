<?php
session_start();
include_once '../api/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    try {
        $query = "SELECT id, email, password, full_name, status 
                  FROM students 
                  WHERE email = :email";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $student = $stmt->fetch();
            
            if (password_verify($password, $student['password'])) {
                if ($student['status'] == 1) {
                    $_SESSION['student_id'] = $student['id'];
                    $_SESSION['student_name'] = $student['full_name'];
                    $_SESSION['student_email'] = $student['email'];
                    
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Your account is inactive. Please contact support.";
                }
            } else {
                $_SESSION['error'] = "Invalid email or password";
            }
        } else {
            $_SESSION['error'] = "Invalid email or password";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "System error. Please try again later.";
    }

    header("Location: login.php");
    exit();
} 