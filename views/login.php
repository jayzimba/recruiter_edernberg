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
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body p-5">

                        <div class="text-center"> <img src="../assets/icons/edernberg.png"
                                alt="University Recruitment Logo" class="img-fluid mb-4" style="max-width: 200px;">
                            <h3>Recruitment System</h3>
                        </div>


                        <div id="error-message" class="alert alert-danger d-none"></div>
                        <form id="loginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"
                                style="background-color: var(--primary-color);">
                                Login
                            </button>
                        </form>
                    </div>
                </div>
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
                    console.log(data);
                    if (data.status) {
                        // Store token in localStorage
                        localStorage.setItem('jwt_token', data.token);
                        // Redirect based on role
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