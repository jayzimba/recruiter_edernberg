<?php
session_start();

// Define allowed pages
$allowed_pages = [
    'dashboard',
    'courses',
    'exams',
    'fees',
    'transcripts',
    'accommodation',
    'program',
    'finances',
    'change-password',
    'profile',
    'payment'
];

// Get the requested page from URL
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Check if the requested page is allowed
if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    // Store the requested page for redirect after login
    $_SESSION['redirect_url'] = "index.php?page=" . $page;
    header("Location: login.php");
    exit();
}

// Include the requested page
$page_file = $page . '.php';
if (file_exists($page_file)) {
    include 'includes/header.php';
    include $page_file;
    include 'includes/footer.php';
} else {
    // If page file doesn't exist, redirect to dashboard
    header("Location: index.php?page=dashboard");
    exit();
}
?>