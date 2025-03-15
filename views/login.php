<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Recruitment - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4A90E2;
        }

        body {
            min-height: 100vh;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .split-screen {
            display: flex;
            min-height: 100vh;
        }

        .left-side {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-color), #2c5282);
            color: white;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .left-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../assets/icons/pattern.png') repeat;
            opacity: 0.1;
        }

        .left-content {
            position: relative;
            z-index: 1;
            max-width: 500px;
            margin: 0 auto;
        }

        .right-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background-color: white;
        }

        .login-form-container {
            width: 100%;
            max-width: 400px;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.1);
        }

        .btn-primary {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-weight: 500;
            background-color: var(--primary-color);
            border: none;
        }

        .btn-primary:hover {
            background-color: #357abd;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-container img {
            max-width: 180px;
            height: auto;
        }

        .welcome-text {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .feature-list li i {
            margin-right: 1rem;
            font-size: 1.5rem;
        }

        @media (max-width: 768px) {
            .split-screen {
                flex-direction: column;
            }

            .left-side {
                display: none;
            }

            .right-side {
                padding: 2rem 1rem;
                min-height: 100vh;
            }

            .login-form-container {
                padding: 1.5rem;
            }

            .welcome-text {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>
    <div class="split-screen">
        <!-- Left Side - Features -->
        <div class="left-side">
            <div class="left-content">
                <h1 class="welcome-text">Welcome to Edernberg University</h1>
                <p class="lead mb-4">Access the recruitment portal to manage applications and student enrollments</p>
                
                <ul class="feature-list">
                    <li>
                        <i class="bi bi-people-fill"></i>
                        <span>Manage student applications efficiently</span>
                    </li>
                    <li>
                        <i class="bi bi-graph-up"></i>
                        <span>Track recruitment progress in real-time</span>
                    </li>
                    <li>
                        <i class="bi bi-shield-check"></i>
                        <span>Secure and streamlined process</span>
                    </li>
                    <li>
                        <i class="bi bi-calendar-check"></i>
                        <span>Automated application tracking</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="right-side">
            <div class="login-form-container">
                <div class="logo-container">
                    <img src="../assets/icons/edernberg.png" alt="Edernberg University Logo" class="img-fluid">
                    <h4 class="mt-3 text-center">Recruitment Portal</h4>
                </div>

                <div id="error-message" class="alert alert-danger d-none"></div>

                <form id="loginForm" class="mt-4">
                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" class="form-control border-start-0" id="email" name="email" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="password" name="password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Sign In
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            };

            fetch('../api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        localStorage.setItem('jwt_token', data.token);
                        switch (data.user.role) {
                            case 'Admin':
                                window.location.href = '../views/admin/dashboard.php';
                                break;
                            case 'lead_recruiter':
                                window.location.href = '../views/lead_recruiter/dashboard.php';
                                break;
                            case 'recruiter':
                                window.location.href = '../views/recruiter/dashboard.php';
                                break;
                            default:
                                showError('Invalid user role');
                        }
                    } else {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    showError('An error occurred. Please try again.');
                });
        });

        function showError(message) {
            const errorDiv = document.getElementById('error-message');
            errorDiv.textContent = message;
            errorDiv.classList.remove('d-none');
        }
    </script>
</body>

</html>