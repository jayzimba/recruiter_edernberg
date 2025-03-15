<?php
session_start();
if (isset($_SESSION['student_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: #4A90E2;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 20px;
        }

        .card-body {
            padding: 30px;
        }

        .form-control {
            border-radius: 5px;
            padding: 12px;
        }

        .btn-primary {
            background: #4A90E2;
            border: none;
            padding: 12px;
            width: 100%;
        }

        .university-logo {
            width: 100px;
            height: auto;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-container">
            <div class="text-center"> <img src="../../assets/icons/edernberg.png" alt="University Recruitment Logo"
                    class="img-fluid mb-4" style="max-width: 200px;">
                <h3>Student Portal</h3>
            </div>
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="mb-0">Login</h4>
                </div>
                <div class="card-body">
                    <div id="loginAlert"></div>
                    <form id="loginForm" onsubmit="handleLogin(event)">
                        <div class="mb-3">
                            <label class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="studentId" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function handleLogin(event) {
            event.preventDefault();
            const button = event.target.querySelector('button');
            const alert = document.getElementById('loginAlert');

            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Logging in...';

            fetch('../../api/student/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        student_id: document.getElementById('studentId').value,
                        password: document.getElementById('password').value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        window.location.href = 'dashboard.php';
                    } else {
                        alert.innerHTML = `
                        <div class="alert alert-danger">
                            ${data.message}
                        </div>
                    `;
                        button.disabled = false;
                        button.innerHTML = 'Login';
                    }
                })
                .catch(error => {
                    alert.innerHTML = `
                    <div class="alert alert-danger">
                        An error occurred. Please try again.
                    </div>
                `;
                    button.disabled = false;
                    button.innerHTML = 'Login';
                });
        }
    </script>
</body>

</html>