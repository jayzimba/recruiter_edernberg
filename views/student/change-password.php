<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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

    .sidebar {
        min-height: 100vh;
        background: #1a237e;
        padding: 20px;
        color: white;
    }

    .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 5px;
        transition: all 0.3s ease;
    }

    .nav-link:hover,
    .nav-link.active {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .nav-link i {
        margin-right: 10px;
    }

    .main-content {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 20px;
    }

    .info-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .student-avatar {
        width: 100px;
        height: 100px;
        background: #4A90E2;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        border-radius: 50%;
        margin-bottom: 20px;
    }

    .detail-label {
        font-weight: 600;
        color: #666;
        margin-bottom: 5px;
    }

    .detail-value {
        color: #333;
        margin-bottom: 15px;
    }

    .header {
        background: white;
        padding: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }
    </style>
</head>

<body>
    <div class="container-fluid">
        <!-- Mobile Navigation -->
        <div class="mobile-nav d-md-none">
            <button class="btn btn-primary" onclick="toggleSidebar()">
                <i class="bi bi-list"></i> Menu
            </button>
        </div>

        <!-- Sidebar Backdrop -->
        <div class="sidebar-backdrop" onclick="toggleSidebar()"></div>

        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar d-none d-md-block">

                <div class="d-flex flex-column">
                    <div class="text-center mb-4">
                        <img src="../../assets/icons/edernberg.png" alt="Logo" class="img-fluid mb-3"
                            style="max-width: 120px;">
                        <h5>Student Portal</h5>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a class="nav-link" href="program.php"><i class="bi bi-journal-text"></i>My Program </a>
                        <a class="nav-link active" href="change-password.php">
                            <i class="bi bi-key"></i> Change Password
                        </a>
                        <a class="nav-link" href="#" onclick="logout()">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </nav>

                </div>
            </div>
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="info-card">
                            <h5 class="mb-4">Change Password</h5>
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

    <script>
    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('show');
        document.querySelector('.sidebar-backdrop').classList.toggle('show');
    }

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

        fetch('../../api/student/change_password.php', {
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
        fetch('../../api/student/logout.php')
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    window.location.href = 'login.php';
                }
            })
            .catch(error => {
                console.error('Logout failed:', error);
            });
    }
    </script>
</body>

</html>