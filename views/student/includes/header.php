<?php
// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | <?php echo ucfirst($page); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/student-dashboard.css" rel="stylesheet">
    <?php 
    if ($page === 'exams'): ?>
    <link href="../../assets/css/exams.css" rel="stylesheet">
    <?php endif; ?>
    <?php if ($page === 'accommodation'): ?>
    <link href="../../assets/css/accommodation.css" rel="stylesheet">
    <?php endif; ?>
    <?php if ($page === 'fees'): ?>
    <link href="../../assets/css/fees.css" rel="stylesheet">
    <?php endif; ?>
    <link rel="icon" type="image/png" href="../../assets/icons/edernberg.png">
</head>

<body>
    <style>
    @media (max-width: 768px) {
        .brand-text {
            display: none;
        }
        
        .user-dropdown span {
            display: none;
        }
        
        .navbar-brand img {
            margin-right: 0;
        }
        
        .navbar .container-fluid {
            padding-right: 0.5rem;
        }
        
        .dropdown {
            margin-left: auto;
        }
        
        .user-dropdown {
            padding: 0.5rem;
            font-size: 1.2rem;
        }
        
        .navbar-logo {
            height: 30px;
        }
    }

    .navbar {
        background: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 0.5rem 1rem;
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .navbar-logo {
        height: 35px;
    }

    .brand-text {
        font-weight: 500;
        color: #333;
    }

    .user-dropdown {
        background: none;
        border: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #333;
        padding: 0.5rem 1rem;
    }

    .user-dropdown:hover,
    .user-dropdown:focus {
        background: rgba(0,0,0,0.05);
    }

    .user-dropdown i {
        font-size: 1.2rem;
    }
    </style>

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
                        <span class="ms-2"><?php echo htmlspecialchars($_SESSION['student_name']); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">
                    <i class="fas fa-home me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $page === 'courses' ? 'active' : ''; ?>" href="index.php?page=courses">
                    <i class="fas fa-book me-2"></i>Courses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $page === 'exams' ? 'active' : ''; ?>" href="index.php?page=exams">
                    <i class="fas fa-file-alt me-2"></i>Exams
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $page === 'fees' ? 'active' : ''; ?>" href="index.php?page=fees">
                    <i class="fas fa-money-bill-wave me-2"></i>Fees
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $page === 'transcripts' ? 'active' : ''; ?>" href="index.php?page=transcripts">
                    <i class="fas fa-file-certificate me-2"></i>Transcripts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $page === 'accommodation' ? 'active' : ''; ?>" href="index.php?page=accommodation">
                    <i class="fas fa-bed me-2"></i>Accommodation
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $page === 'program' ? 'active' : ''; ?>" href="index.php?page=program">
                    <i class="fas fa-graduation-cap me-2"></i>Program
                </a>
            </li>
        </ul>
    </div>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to logout?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    function confirmLogout() {
        var logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
        logoutModal.show();
    }
    </script>
</body>
</html> 