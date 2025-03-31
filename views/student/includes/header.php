<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/student-dashboard.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../assets/icons/edernberg.png">
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <button id="sidebar-toggle" class="btn">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="navbar-brand">
                <img src="../../assets/icons/image.png" alt="Logo" class="navbar-logo">
                <span class="brand-text">Student Portal</span>
            </div>

            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn dropdown-toggle user-dropdown" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i>
                        <span class="ms-2"><?php echo $_SESSION['student_name']; ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="change-password.php"><i class="fas fa-key me-2"></i>Change Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</body>
</html> 