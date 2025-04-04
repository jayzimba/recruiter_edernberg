<?php
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../utils/Helpers.php';

class AuthController {
    private $studentModel;

    public function __construct() {
        $this->studentModel = new Student();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::respondJson(['error' => 'Invalid request method'], 405);
        }

        $student_id = Helpers::sanitizeInput($_POST['student_id'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($student_id) || empty($password)) {
            Helpers::respondJson(['error' => 'Student ID and password are required'], 400);
        }

        // Validate student ID format (assuming it's alphanumeric)
        if (!preg_match('/^[A-Za-z0-9]+$/', $student_id)) {
            Helpers::respondJson(['error' => 'Invalid Student ID format'], 400);
        }

        $student = $this->studentModel->authenticate($student_id, $password);

        if ($student) {
            // Set session variables
            $_SESSION['user'] = $student;
            $_SESSION['student_id'] = $student['student_id'];
            $_SESSION['student_name'] = $student['first_name'] . ' ' . $student['last_name'];
            $_SESSION['last_activity'] = time();
            
            Helpers::respondJson([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => 'dashboard.php',
                'user' => $student
            ]);
        } else {
            Helpers::respondJson(['error' => 'Invalid Student ID or password'], 401);
        }
    }

    public function logout() {
        session_destroy();
        Helpers::redirect('../login.php');
    }

    public function checkAuth() {
        if (!Helpers::isAuthenticated()) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                Helpers::respondJson(['error' => 'Unauthorized'], 401);
            } else {
                Helpers::redirect('../login.php');
            }
        }

        // Check session timeout (30 minutes)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            session_destroy();
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                Helpers::respondJson(['error' => 'Session expired'], 440);
            } else {
                Helpers::redirect('../login.php?session_expired=1');
            }
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

    public function getCurrentUser() {
        return Helpers::getSessionUser();
    }
}
?> 