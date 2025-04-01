<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$page = 'fees';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header">
                    <h2>Fee Payment</h2>
                    <p class="text-muted">Manage your tuition payments and view payment history</p>
                </div>
            </div>
        </div>

        <!-- Fee Summary -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="fee-summary-card">
                    <div class="fee-summary-grid">
                        <div class="fee-summary-item">
                            <span class="label">Total Tuition Fee</span>
                            <span class="value">ZMW5,000</span>
                        </div>
                        <div class="fee-summary-item">
                            <span class="label">Amount Paid</span>
                            <span class="value paid">ZMW3,500</span>
                        </div>
                        <div class="fee-summary-item">
                            <span class="label">Balance Due</span>
                            <span class="value pending">ZMW1,500</span>
                        </div>
                        <div class="fee-summary-item">
                            <span class="label">Payment Status</span>
                            <span class="status-badge pending">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="documents-section">
                    <div class="card-header">
                        <h5>Financial Documents</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="documentsTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button" role="tab">
                                    <i class="fas fa-file-invoice-dollar me-2"></i>Invoices
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="quotes-tab" data-bs-toggle="tab" data-bs-target="#quotes" type="button" role="tab">
                                    <i class="fas fa-file-invoice me-2"></i>Quotes
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="receipts-tab" data-bs-toggle="tab" data-bs-target="#receipts" type="button" role="tab">
                                    <i class="fas fa-receipt me-2"></i>Receipts
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="documentsTabContent">
                            <!-- Invoices Tab -->
                            <div class="tab-pane fade show active" id="invoices" role="tabpanel">
                                <div class="document-list">
                                    <div class="document-item">
                                        <div class="document-info">
                                            <div class="document-title">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                                <div>
                                                    <h6>Tuition Invoice - March 2024</h6>
                                                    <span class="document-date">March 1, 2024</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <div class="document-amount">ZMW2,000</div>
                                            <button class="btn btn-outline-primary btn-sm" onclick="downloadDocument('invoice', 'march-2024')">
                                                <i class="fas fa-download me-2"></i>Download
                                            </button>
                                        </div>
                                    </div>
                                    <div class="document-item">
                                        <div class="document-info">
                                            <div class="document-title">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                                <div>
                                                    <h6>Tuition Invoice - February 2024</h6>
                                                    <span class="document-date">February 1, 2024</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <div class="document-amount">ZMW1,500</div>
                                            <button class="btn btn-outline-primary btn-sm" onclick="downloadDocument('invoice', 'february-2024')">
                                                <i class="fas fa-download me-2"></i>Download
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Quotes Tab -->
                            <div class="tab-pane fade" id="quotes" role="tabpanel">
                                <div class="document-list">
                                    <div class="document-item">
                                        <div class="document-info">
                                            <div class="document-title">
                                                <i class="fas fa-file-invoice"></i>
                                                <div>
                                                    <h6>Tuition Fee Quote - 2024</h6>
                                                    <span class="document-date">January 1, 2024</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <div class="document-amount">ZMW5,000</div>
                                            <button class="btn btn-outline-primary btn-sm" onclick="downloadDocument('quote', '2024')">
                                                <i class="fas fa-download me-2"></i>Download
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Receipts Tab -->
                            <div class="tab-pane fade" id="receipts" role="tabpanel">
                                <div class="document-list">
                                    <div class="document-item">
                                        <div class="document-info">
                                            <div class="document-title">
                                                <i class="fas fa-receipt"></i>
                                                <div>
                                                    <h6>Payment Receipt - March 2024</h6>
                                                    <span class="document-date">March 1, 2024</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <div class="document-amount paid">ZMW2,000</div>
                                            <div class="payment-method">
                                                <i class="fas fa-credit-card"></i>
                                                <span>Credit Card</span>
                                            </div>
                                            <button class="btn btn-outline-primary btn-sm" onclick="downloadDocument('receipt', 'march-2024')">
                                                <i class="fas fa-download me-2"></i>Download
                                            </button>
                                        </div>
                                    </div>
                                    <div class="document-item">
                                        <div class="document-info">
                                            <div class="document-title">
                                                <i class="fas fa-receipt"></i>
                                                <div>
                                                    <h6>Payment Receipt - February 2024</h6>
                                                    <span class="document-date">February 1, 2024</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <div class="document-amount paid">ZMW1,500</div>
                                            <div class="payment-method">
                                                <i class="fas fa-university"></i>
                                                <span>Bank Transfer</span>
                                            </div>
                                            <button class="btn btn-outline-primary btn-sm" onclick="downloadDocument('receipt', 'february-2024')">
                                                <i class="fas fa-download me-2"></i>Download
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="payment-methods">
                    <div class="card-header">
                        <h5>Payment Methods</h5>
                    </div>
                    <div class="card-body">
                        <div class="payment-methods-grid">
                            <!-- Bank Transfer -->
                            <div class="payment-method-card" onclick="selectPaymentMethod('bank')">
                                <div class="payment-method-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <h6 class="payment-method-name">Bank Transfer</h6>
                                <p class="payment-method-description">Transfer directly to our bank account</p>
                            </div>
                            <!-- Mobile Money -->
                            <div class="payment-method-card" onclick="selectPaymentMethod('mobile')">
                                <div class="payment-method-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <h6 class="payment-method-name">Mobile Money</h6>
                                <p class="payment-method-description">Pay using mobile money services</p>
                            </div>
                            <!-- Credit Card -->
                            <div class="payment-method-card" onclick="selectPaymentMethod('card')">
                                <div class="payment-method-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <h6 class="payment-method-name">Credit Card</h6>
                                <p class="payment-method-description">Pay using credit or debit card</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="row">
            <div class="col-12">
                <div class="payment-form">
                    <h5 class="mb-4">Make a Payment</h5>
                    <form id="paymentForm" onsubmit="return handlePayment(event)">
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" required>
                                <option value="">Select payment method</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="mobile">Mobile Money</option>
                                <option value="card">Credit Card</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">ZMW</span>
                                <input type="number" class="form-control" placeholder="Enter amount" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i> Submit Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectPaymentMethod(method) {
    // Remove selected class from all cards
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selected class to clicked card
    const selectedCard = document.querySelector(`.payment-method-card:nth-child(${
        method === 'bank' ? '1' : method === 'mobile' ? '2' : '3'
    })`);
    selectedCard.classList.add('selected');
    
    // Update select input
    document.querySelector('select').value = method;
}

function handlePayment(event) {
    event.preventDefault();
    // Add payment processing logic here
    alert('Payment submitted successfully!');
    return false;
}

function downloadDocument(type, id) {
    // Add document download logic here
    alert(`Downloading ${type} document: ${id}`);
}
</script>

<?php include 'includes/footer.php'; ?> 