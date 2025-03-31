<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/student-login.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../assets/icons/edernberg.png">
</head>
<body>
    <div class="split-container">
        <div class="left-side">
            <div class="left-content">
                <h1>Welcome to Student Portal</h1>
                <p>Access your academic information, course materials, and stay connected with your educational journey.</p>
            </div>
        </div>
        
        <div class="right-side">
            <div class="login-container">
                <div class="logo-container">
                    <img src="../../assets/icons/image.png" alt="Logo">
                    <h4>Student Login</h4>
                </div>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php 
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form id="loginForm" onsubmit="handleLogin(event)">
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="studentId" 
                               placeholder="Enter your Student ID" pattern="[0-9]+" 
                               title="Please enter your student ID number" required
                               autocomplete="off">
                        <label for="studentId">
                            <i class="fas fa-id-card me-2"></i>Student ID Number
                        </label>
                        <small class="form-text text-muted">
                            Enter your student identification number
                        </small>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="password" 
                               placeholder="Password" required
                               title="Enter your password">
                        <label for="password">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <small class="form-text text-muted">
                            Default password: Password@2025
                        </small>
                    </div>

                    <div class="forgot-password">
                        <a href="forgot-password.php">
                            <i class="fas fa-key"></i> Forgot Password?
                        </a>
                    </div>

                    <div id="loginAlert"></div>

                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="footer">
        Powered by <a href="https://www.lampsyc.com" target="_blank">Lampsyc Technologies Ltd</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function handleLogin(event) {
            event.preventDefault();
            const button = event.target.querySelector('button');
            const alert = document.getElementById('loginAlert');
            const originalButtonText = button.innerHTML;

            // Show loading state
            button.disabled = true;
            button.classList.add('loading');
            button.innerHTML = '';

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
                            <i class="fas fa-exclamation-circle me-2"></i>
                            ${data.message}
                        </div>
                    `;
                    button.disabled = false;
                    button.classList.remove('loading');
                    button.innerHTML = originalButtonText;
                }
            })
            .catch(error => {
                alert.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        An error occurred. Please try again.
                    </div>
                `;
                button.disabled = false;
                button.classList.remove('loading');
                button.innerHTML = originalButtonText;
            });
        }
    </script>
</body>
</html> 