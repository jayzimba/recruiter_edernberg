<?php
require_once __DIR__ . '/Auth.php';

function checkAuth($allowed_roles = [])
{
    $auth = new Auth();

    if (!$auth->isAuthenticated()) {
        header("Location: /views/login.php");
        exit;
    }

    if (!empty($allowed_roles) && !in_array($_SESSION['user_role'], $allowed_roles)) {
        header("Location: /views/unauthorized.php");
        exit;
    }
}
