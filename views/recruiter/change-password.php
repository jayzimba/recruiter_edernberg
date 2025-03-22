<?php
require_once __DIR__ . '/../../middleware/check_auth.php';
checkAuth(['recruiter', 'lead_recruiter']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4A90E2;
            --secondary-color: #6c757d;
        }

        /* Copy all styles from dashboard.php */
        /* Add password-specific styles */
        .password-requirements {
            font-size: 0.9rem;
            color: #666;
            margin-top: 10px;
        }

        .requirement {
            margin-bottom: 5px;
        }

        .requirement i {
            margin-right: 5px;
        }

        .requirement.valid {
            color: #198754;
        }

        .requirement.invalid {
            color: #dc3545;
        }


        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 2rem;
        }

        .password-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            max-width: 500px;
            margin: 2rem auto;
        }

        .required::after {
            content: "*";
            color: red;
            margin-left: 4px;
        }

        .sidebar {
            min-height: 100vh;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            padding-top: 1rem;
            transition: transform 0.3s ease;
        }

        .stat-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            margin-bottom: 1rem;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .nav-link {
            padding: 0.8rem 1rem;
            color: #6c757d;
            border-radius: 5px;
            margin: 0.2rem 0;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .nav-link i {
            margin-right: 10px;
        }

        .header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .recent-applications {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        /* Sidebar toggle button */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Close button for sidebar on mobile */
        .sidebar-close {
            display: none;
            /* Hidden by default */
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #6c757d;
            cursor: pointer;
            z-index: 1001;
            /* Ensure it's above the sidebar */
        }

        @media (max-width: 767.98px) {
            .sidebar-close {
                display: block;
                /* Show on mobile */
            }
        }

        @media (max-width: 767.98px) {
            .sidebar-toggle {
                display: block;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1000;
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar" id="sidebar">
                <div class="d-flex flex-column">
                    <!-- Close Button for Mobile -->
                    <button class="sidebar-close d-md-none" id="sidebarClose">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <h4 class="mb-4 px-3">Recruitment</h4>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
                        <a class="nav-link" href="new-application.php"><i class="bi bi-plus-circle"></i> New
                            Application</a>
                        <a class="nav-link" href="applications.php"><i class="bi bi-list-ul"></i>
                            Applications</a>
                        <a class="nav-link" href="leads.php">
                            <i class="bi bi-people"></i> My Leads
                        </a>
                        <a class="nav-link active" href="change-password.php">
                            <i class="bi bi-key"></i> Change Password
                        </a>
                        <a class="nav-link" href="#" onclick="logout(); return false;"><i
                                class="bi bi-box-arrow-right"></i> Logout</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header -->
                <div class="header mb-4 d-flex justify-content-between align-items-center">
                    <button class="sidebar-toggle d-md-none" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h4 class="m-0 d-md-block d-none">Change Password</h4>


                    <div class="user-profile">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['user_email'], 0, 1)); ?>
                        </div>
                        <div>
                            <!-- <small class="text-muted">Welcome,</small> -->
                            <div class="fw-bold"><?php echo $_SESSION['user_email']; ?></div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="info-card">
                            <div id="alertMessage"></div>
                            <form id="changePasswordForm" onsubmit="handlePasswordChange(event)">
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="currentPassword" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="newPassword" required
                                        onkeyup="checkPasswordStrength(this.value)">
                                    <div class="password-requirements">
                                        <div class="requirement" id="length">
                                            <i class="bi bi-x-circle"></i> At least 8 characters
                                        </div>
                                        <div class="requirement" id="uppercase">
                                            <i class="bi bi-x-circle"></i> At least one uppercase letter
                                        </div>
                                        <div class="requirement" id="lowercase">
                                            <i class="bi bi-x-circle"></i> At least one lowercase letter
                                        </div>
                                        <div class="requirement" id="number">
                                            <i class="bi bi-x-circle"></i> At least one number
                                        </div>
                                        <div class="requirement" id="special">
                                            <i class="bi bi-x-circle"></i> At least one special character
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirmPassword" required>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function checkPasswordStrength(password) {
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[^A-Za-z0-9]/.test(password)
            };

            for (const [req, valid] of Object.entries(requirements)) {
                const element = document.getElementById(req);
                element.classList.toggle('valid', valid);
                element.classList.toggle('invalid', !valid);
                element.querySelector('i').className = valid ? 'bi bi-check-circle' : 'bi bi-x-circle';
            }

            return Object.values(requirements).every(Boolean);
        }

        function handlePasswordChange(event) {
            event.preventDefault();
            const button = event.target.querySelector('button');
            const alert = document.getElementById('alertMessage');

            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                alert.innerHTML = `
                    <div class="alert alert-danger">
                        Passwords do not match
                    </div>
                `;
                return;
            }

            if (!checkPasswordStrength(newPassword)) {
                alert.innerHTML = `
                    <div class="alert alert-danger">
                        Please meet all password requirements
                    </div>
                `;
                return;
            }

            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Updating...';

            fetch('../../api/recruiter/change_password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        current_password: document.getElementById('currentPassword').value,
                        new_password: newPassword
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        alert.innerHTML = `
                        <div class="alert alert-success">
                            ${data.message}
                        </div>
                    `;
                        event.target.reset();
                    } else {
                        alert.innerHTML = `
                        <div class="alert alert-danger">
                            ${data.message}
                        </div>
                    `;
                    }
                    button.disabled = false;
                    button.innerHTML = 'Change Password';
                })
                .catch(error => {
                    alert.innerHTML = `
                    <div class="alert alert-danger">
                        An error occurred. Please try again.
                    </div>
                `;
                    button.disabled = false;
                    button.innerHTML = 'Change Password';
                });
        }

        function logout() {
            fetch('../../api/logout.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        window.location.href = '../login.php';
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>

</html>