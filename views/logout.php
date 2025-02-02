<?php
require_once '../middleware/Auth.php';

$auth = new Auth();
$auth->logout();

// Clear any stored tokens
if (isset($_COOKIE['jwt_token'])) {
    setcookie('jwt_token', '', time() - 3600, '/');
}

header("Location: login.php");
exit;
