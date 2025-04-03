<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Clear any remaining session data
session_unset();

// Add a small delay to ensure session is properly destroyed
sleep(1);

// Redirect to student login page with a success message
header("Location: ./login.php?logout=success");
exit();
?> 