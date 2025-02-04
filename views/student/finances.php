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
    <title>My Finances - Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
    :root {
        --primary-color: #4A90E2;
        --secondary-color: #6c757d;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
    }



    .sidebar {
        min-height: 100vh;
        background: #1a237e;
        padding: 1rem;
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

    /* Copy sidebar styles from program.php */

    .finance-banner {
        background: linear-gradient(135deg, #1a237e 0%, #4A90E2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .finance-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 1.5rem;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .transactions-container {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
    }

    .transaction-tabs {
        margin-bottom: 2rem;
    }

    .nav-tabs {
        border: none;
        gap: 1rem;
    }

    .nav-tabs .nav-link {
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        color: var(--secondary-color);
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        background: var(--primary-color);
        color: white;
    }

    .transaction-item {
        display: flex;
        align-items: center;
        padding: 1.25rem;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .transaction-item:hover {
        transform: translateX(5px);
        border-color: var(--primary-color);
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .transaction-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.5rem;
    }

    .icon-receipt {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success-color);
    }

    .icon-invoice {
        background: rgba(255, 193, 7, 0.1);
        color: var(--warning-color);
    }

    .transaction-info {
        flex-grow: 1;
    }

    .transaction-amount {
        font-weight: 600;
        font-size: 1.1rem;
    }

    .amount-paid {
        color: var(--success-color);
    }

    .amount-pending {
        color: var(--warning-color);
    }

    .transaction-date {
        color: var(--secondary-color);
        font-size: 0.875rem;
    }

    .transaction-status {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-paid {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success-color);
    }

    .status-pending {
        background: rgba(255, 193, 7, 0.1);
        color: var(--warning-color);
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 15px;
        border: none;
    }

    .modal-header {
        background: var(--primary-color);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 1.5rem;
    }

    .modal-body {
        padding: 2rem;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 1rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .detail-label {
        color: var(--secondary-color);
        font-weight: 500;
    }

    .detail-value {
        font-weight: 600;
    }

    .download-button {
        background: var(--primary-color);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .download-button:hover {
        background: #357abd;
        color: white;
    }
    </style>
</head>

<body>
    <div class="container-fluid">
        <!-- Copy sidebar structure from program.php -->

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
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a class="nav-link" href="program.php"><i class="bi bi-journal-text"></i>My Program </a>
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
            <div class="col-md-9 col-lg-10 main-content pt-4">


                <!-- Finance Summary -->
                <div class="finance-banner">
                    <h4 class="mb-2">Financial Overview</h4>
                    <div class="finance-stats">
                        <div class="stat-card">
                            <i class="bi bi-cash"></i>
                            <h6>Total Fees</h6>
                            <p class="mb-0">ZMW 150,000</p>
                        </div>
                        <div class="stat-card">
                            <i class="bi bi-check-circle"></i>
                            <h6>Amount Paid</h6>
                            <p class="mb-0">ZMW 100,000</p>
                        </div>
                        <div class="stat-card">
                            <i class="bi bi-exclamation-circle"></i>
                            <h6>Balance</h6>
                            <p class="mb-0">ZMW 50,000</p>
                        </div>
                    </div>
                </div>

                <!-- Transactions List -->
                <div class="transactions-container">
                    <div class="transaction-tabs">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#receipts">
                                    <i class="bi bi-receipt me-2"></i>Receipts
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#invoices">
                                    <i class="bi bi-file-text me-2"></i>Invoices
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="receipts">
                            <!-- Receipts List -->
                            <div class="transaction-item" onclick="showTransactionDetails('receipt-1')">
                                <div class="transaction-icon icon-receipt">
                                    <i class="bi bi-receipt"></i>
                                </div>
                                <div class="transaction-info">
                                    <h6 class="mb-1">Semester 1 Fee Payment</h6>
                                    <span class="transaction-date">Jan 15, 2024</span>
                                </div>
                                <div class="text-end">
                                    <div class="transaction-amount amount-paid">ZMW 50,000</div>
                                    <span class="transaction-status status-paid">Paid</span>
                                </div>
                            </div>
                            <!-- Add more receipt items -->
                        </div>

                        <div class="tab-pane fade" id="invoices">
                            <!-- Invoices List -->
                            <div class="transaction-item" onclick="showTransactionDetails('invoice-1')">
                                <div class="transaction-icon icon-invoice">
                                    <i class="bi bi-file-text"></i>
                                </div>
                                <div class="transaction-info">
                                    <h6 class="mb-1">Semester 2 Fee Invoice</h6>
                                    <span class="transaction-date">Due: Mar 1, 2024</span>
                                </div>
                                <div class="text-end">
                                    <div class="transaction-amount amount-pending">ZMW 50,000</div>
                                    <span class="transaction-status status-pending">Pending</span>
                                </div>
                            </div>
                            <!-- Add more invoice items -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Detail Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transaction Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="transactionDetails">
                    <!-- Details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function showTransactionDetails(transactionId) {
        const modal = new bootstrap.Modal(document.getElementById('transactionModal'));
        const detailsContainer = document.getElementById('transactionDetails');

        // In real implementation, fetch details from API
        const details = `
                <div class="detail-row">
                    <span class="detail-label">Transaction ID</span>
                    <span class="detail-value">#${transactionId}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date</span>
                    <span class="detail-value">January 15, 2024</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount</span>
                    <span class="detail-value">ZMW 50,000</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method</span>
                    <span class="detail-value">Bank Transfer</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value text-success">Paid</span>
                </div>
                <div class="text-center mt-4">
                    <a href="#" class="download-button">
                        <i class="bi bi-download"></i>
                        Download Receipt
                    </a>
                </div>
            `;

        detailsContainer.innerHTML = details;
        modal.show();
    }

    // Keep existing sidebar toggle and logout functions
    </script>
</body>

</html>

</html>