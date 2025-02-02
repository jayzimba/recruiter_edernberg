<?php
session_start();
require_once 'middleware/Auth.php';

$auth = new Auth();

// If user is not logged in, redirect to login page
if (!$auth->isAuthenticated()) {
    header("Location: views/login.php");
    exit;
}

// If user is logged in, redirect to appropriate dashboard based on role
$userRole = $_SESSION['user_role'];

switch ($userRole) {
    case 'Admin':
        header("Location: views/admin/dashboard.php");
        break;

    case 'lead_recruiter':
        header("Location: views/recruiter/dashboard.php");
        break;

    case 'recruiter':
        header("Location: views/recruiter/dashboard.php");
        break;

    default:
        // If role is not recognized, destroy session and redirect to login
        session_destroy();
        header("Location: views/login.php?error=invalid_role");
        break;
}
exit;
