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
            <!-- Include Sidebar -->
            <?php include '../../includes/lead_recruiter_sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Include Header -->
                <?php include '../../includes/lead_recruiter_header.php'; ?>

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="password-card">
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