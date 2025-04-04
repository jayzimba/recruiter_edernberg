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

                <?php if (isset($_GET['session_expired'])): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-clock me-2"></i>Your session has expired. Please login again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form id="loginForm" onsubmit="handleLogin(event)">
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="studentId" 
                               placeholder="Enter your Student ID" 
                               pattern="[A-Za-z0-9]+"
                               title="Please enter your student ID (letters and numbers only)" 
                               required
                               autocomplete="off">
                        <label for="studentId">
                            <i class="fas fa-id-card me-2"></i>Student ID
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
                            Be sure to change your default password after login.
                        </small>
                    </div>

                    <div class="forgot-password">
                        <a href="forgot-password.php">
                            <i class="fas fa-key"></i> Forgot Password?
                        </a>
                    </div>

                    <div id="loginAlert"></div>

                    <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="footer">
        Powered by <a href="https://www.lampsyc.com" target="_blank">Lampsync Technologies Ltd</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function handleLogin(event) {
            event.preventDefault();
            const button = document.getElementById('submitBtn');
            const alert = document.getElementById('loginAlert');
            const originalButtonText = button.innerHTML;

            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing in...';

            // Create FormData object
            const formData = new FormData();
            formData.append('student_id', document.getElementById('studentId').value);
            formData.append('password', document.getElementById('password').value);

            fetch('backend/api/auth.php?action=login', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Use the redirect URL from the response if available
                    const redirectUrl = data.redirect || 'dashboard.php';
                    window.location.href = redirectUrl;
                } else {
                    alert.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            ${data.error || 'Invalid credentials'}
                        </div>
                    `;
                    button.disabled = false;
                    button.innerHTML = originalButtonText;
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                alert.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        An error occurred. Please try again.
                    </div>
                `;
                button.disabled = false;
                button.innerHTML = originalButtonText;
            });
        }
    </script>
</body>
</html> 