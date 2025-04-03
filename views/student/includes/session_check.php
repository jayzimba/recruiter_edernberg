<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['student_id'])) {
    // Store the current page URL in session
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    // Redirect to login page
    header("Location: /login.php");
    exit();
}

// Prevent direct access to login page when already logged in
if (basename($_SERVER['PHP_SELF']) === 'login.php' && isset($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Handle browser back button
if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
    // Only allow navigation to dashboard or other student pages
    if (!str_contains($referer, '/student/') && !str_contains($referer, 'dashboard.php')) {
        header("Location: dashboard.php");
        exit();
    }
}
?> 