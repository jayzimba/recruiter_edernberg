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
    <title>Make Payment - Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../assets/css/style.css" rel="stylesheet">
    <style>
        .payment-option {
            border: 2px solid #dee2e6;
            border-radius: 5px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .payment-option.selected {
            border-color: var(--primary-color);
            background-color: rgba(var(--primary-rgb), 0.05);
        }

        .payment-icon {
            font-size: 1rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .mobile-money-options {
            display: none;
        }

        .mobile-money-options.show {
            display: block;
        }

        .card-payment-form {
            display: none;
        }

        .card-payment-form.show {
            display: block;
        }

        .payment-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .summary-total {
            border-top: 1px solid #dee2e6;
            padding-top: 0.5rem;
            margin-top: 0.5rem;
            font-weight: bold;
        }

        .sidebar {
            min-height: 100vh;
            background: #1a237e;
            padding: 1rem;
            color: white;
        }
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 5px 10px;
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

    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="d-flex flex-column">
                    <div class="text-center mb-4">
                        <img src="../../assets/icons/edernberg.png" alt="Logo" class="img-fluid mb-3" style="max-width: 120px;">
                        <h5>Student Portal</h5>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a class="nav-link" href="program.php">
                            <i class="bi bi-journal-text"></i> My Program
                        </a>
                        <a class="nav-link active" href="payment.php">
                            <i class="bi bi-credit-card"></i> Make Payment
                        </a>
                        <a class="nav-link" href="change-password.php">
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
                <div class="container py-4">
                    <h4 class="mb-4">Make Payment</h4>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Country Selection -->
                            <div class="mb-4">
                                <label class="form-label">Select Your Country</label>
                                <select class="form-select" id="countrySelect" onchange="updatePaymentOptions()">
                                    <option value="">Select Country</option>
                                    <option value="ZM">Zambia</option>
                                    <option value="OTHER">Other Countries</option>
                                </select>
                            </div>

                            <!-- Payment Options -->
                            <div id="paymentOptions" style="display: none;">
                                <!-- Mobile Money Option (Only for Zambia) -->
                                <div id="mobileMoneyOption" class="payment-option" onclick="selectPaymentMethod('mobile')">
                                    <div class="payment-icon">
                                        <i class="bi bi-phone"></i>
                                    </div>
                                    <h6>Mobile Money</h6>
                                    <p class="text-muted">Pay using MTN, Airtel, or Zamtel Money</p>
                                </div>

                                <!-- Credit Card Option -->
                                <div id="cardOption" class="payment-option" onclick="selectPaymentMethod('card')">
                                    <div class="payment-icon">
                                        <i class="bi bi-credit-card"></i>
                                    </div>
                                    <h6>Credit/Debit Card</h6>
                                    <p class="text-muted">Pay using Visa or Mastercard</p>
                                </div>
                            </div>

                            <!-- Mobile Money Form -->
                            <div id="mobileMoneyForm" class="mobile-money-options mt-4">
                                <h6 class="mb-3">Mobile Money Payment</h6>
                                <div class="mb-3">
                                    <label class="form-label">Select Provider</label>
                                    <select class="form-select" id="mobileProvider">
                                        <option value="mtn">MTN Money</option>
                                        <option value="airtel">Airtel Money</option>
                                        <option value="zamtel">Zamtel Money</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" placeholder="Enter mobile money number">
                                </div>
                                <button class="btn btn-primary" onclick="processMobilePayment()">
                                    Pay Now
                                </button>
                            </div>

                            <!-- Credit Card Form -->
                            <div id="cardPaymentForm" class="card-payment-form mt-4">
                                <h6 class="mb-3">Card Payment</h6>
                                <div class="mb-3">
                                    <label class="form-label">Card Number</label>
                                    <input type="text" class="form-control" placeholder="1234 5678 9012 3456">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Expiry Date</label>
                                        <input type="text" class="form-control" placeholder="MM/YY">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">CVV</label>
                                        <input type="text" class="form-control" placeholder="123">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cardholder Name</label>
                                    <input type="text" class="form-control" placeholder="Name on card">
                                </div>
                                <button class="btn btn-primary" onclick="processCardPayment()">
                                    Pay Now
                                </button>
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="col-md-4">
                            <div class="payment-summary">
                                <h5 class="mb-4">Payment Summary</h5>
                                <div class="summary-item">
                                    <span>Invoice Number</span>
                                    <span>INV-1234567890</span>
                                </div>
                                <div class="summary-item">
                                    <span>Tuition Fee</span>
                                    <span>ZMW 1,000.00</span>
                                </div>
                                <div class="summary-item">
                                    <span>Registration Fee</span>
                                    <span>ZMW 50.00</span>
                                </div>
                                <div class="summary-item">
                                    <span>Processing Fee</span>
                                    <span>ZMW 10.00</span>
                                </div>
                                <div class="summary-item summary-total">
                                    <span>Total</span>
                                    <span>ZMW 1,060.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updatePaymentOptions() {
            const country = document.getElementById('countrySelect').value;
            const paymentOptions = document.getElementById('paymentOptions');
            const mobileMoneyOption = document.getElementById('mobileMoneyOption');

            if (country) {
                paymentOptions.style.display = 'block';
                mobileMoneyOption.style.display = country === 'ZM' ? 'block' : 'none';
            } else {
                paymentOptions.style.display = 'none';
            }

            // Reset forms
            document.getElementById('mobileMoneyForm').classList.remove('show');
            document.getElementById('cardPaymentForm').classList.remove('show');
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('selected');
            });
        }

        function selectPaymentMethod(method) {
            // Reset all options and forms
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('selected');
            });
            document.getElementById('mobileMoneyForm').classList.remove('show');
            document.getElementById('cardPaymentForm').classList.remove('show');

            // Show selected option and form
            if (method === 'mobile') {
                document.getElementById('mobileMoneyOption').classList.add('selected');
                document.getElementById('mobileMoneyForm').classList.add('show');
            } else {
                document.getElementById('cardOption').classList.add('selected');
                document.getElementById('cardPaymentForm').classList.add('show');
            }
        }

        function processMobilePayment() {
            // Add mobile money payment processing logic here
            alert('Processing mobile money payment...');
        }

        function processCardPayment() {
            // Add card payment processing logic here
            alert('Processing card payment...');
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