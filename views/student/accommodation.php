<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: /login.php");
    exit();
}

$page = 'accommodation';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="page-title">
            <h1>Accommodation</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Accommodation</li>
                </ol>
            </nav>
        </div>

        <!-- Accommodation Status Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="accommodation-status-card">
                    <div class="status-content">
                        <div class="status-info">
                            <span class="label">Current Room</span>
                            <span class="value">Block B, Room 205</span>
                        </div>
                        <div class="divider"></div>
                        <div class="status-info">
                            <span class="label">Room Type</span>
                            <span class="value">Double Sharing</span>
                        </div>
                        <div class="divider"></div>
                        <div class="status-info">
                            <span class="label">Fee Status</span>
                            <span class="value">Paid</span>
                        </div>
                        <div class="divider"></div>
                        <div class="status-info">
                            <span class="label">Valid Until</span>
                            <span class="value">Dec 31, 2025</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Details and Booking Section -->
        <div class="row">
            <!-- Current Room Details -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Room Details</h5>
                        <span class="badge bg-success">Currently Occupied</span>
                    </div>
                    <div class="card-body">
                        <div class="room-details">
                            <div class="room-image mb-4">
                                <!-- Image Slider -->
                                <div id="roomImageSlider" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-indicators">
                                        <button type="button" data-bs-target="#roomImageSlider" data-bs-slide-to="0" class="active"></button>
                                        <button type="button" data-bs-target="#roomImageSlider" data-bs-slide-to="1"></button>
                                        <button type="button" data-bs-target="#roomImageSlider" data-bs-slide-to="2"></button>
                                    </div>
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img src="../../assets/images/room1.png" alt="Room View 1" class="img-fluid room-preview" data-bs-toggle="modal" data-bs-target="#imageModal">
                                        </div>
                                        <div class="carousel-item">
                                            <img src="../../assets/images/room2.png" alt="Room View 2" class="img-fluid room-preview" data-bs-toggle="modal" data-bs-target="#imageModal">
                                        </div>
                                        <div class="carousel-item">
                                            <img src="../../assets/images/room3.png" alt="Room View 3" class="img-fluid room-preview" data-bs-toggle="modal" data-bs-target="#imageModal">
                                        </div>
                                        <div class="carousel-item">
                                            <img src="../../assets/images/room4.png" alt="Room View 4" class="img-fluid room-preview" data-bs-toggle="modal" data-bs-target="#imageModal">
                                        </div>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#roomImageSlider" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#roomImageSlider" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            </div>
                            <div class="room-info">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6>Room Features</h6>
                                        <ul class="feature-list">
                                            <li><i class="fas fa-bed"></i> 2 Single Beds</li>
                                            <li><i class="fas fa-desk"></i> Study Desks</li>
                                            <li><i class="fas fa-wifi"></i> Wi-Fi Access</li>
                                            <li><i class="fas fa-bolt"></i> Power Outlets</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6>Amenities</h6>
                                        <ul class="feature-list">
                                            <li><i class="fas fa-bath"></i> Shared Bathroom</li>
                                            <li><i class="fas fa-utensils"></i> Common Kitchen</li>
                                            <li><i class="fas fa-couch"></i> Common Room</li>
                                            <li><i class="fas fa-broom"></i> Cleaning Service</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking and Payment Info -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Payment Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="payment-info">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Room Rate (per semester)</span>
                                <strong>ZMW 1,500</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Status</span>
                                <span class="text-success">Paid</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Payment Date</span>
                                <span>Jan 15, 2025</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Next Payment Due</span>
                                <span>Jun 30, 2025</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Room Booking -->
                <div class="card">
                    <div class="card-header">
                        <h5>Room Booking</h5>
                    </div>
                    <div class="card-body">
                        <div class="booking-info">
                            <div class="booking-status mb-3">
                                <h6 class="text-primary mb-2">Next Semester Booking</h6>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Booking Period</span>
                                    <span class="badge bg-success">Open</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Deadline</span>
                                    <span>May 30, 2025</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Semester</span>
                                    <span>Fall 2025</span>
                                </div>
                            </div>
                            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#bookingModal">
                                Book for Next Semester
                            </button>
                            <small class="d-block text-center mt-2 text-muted">Or</small>
                            
                            <div class="mt-3">
                                <a href="https://www.lampsync.com" target="_blank" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-search me-2"></i>Find Boarding House
                                </a>
                                <small class="d-block text-center mt-2 text-muted">Search for alternative accommodation options</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <img src="" id="modalImage" class="img-fluid w-100">
            </div>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Room Booking for Fall 2025</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bookingForm">
                    <!-- Booking Steps -->
                    <div class="booking-steps mb-4">
                        <div class="step active" id="step1">
                            <span class="step-number">1</span>
                            <span class="step-text">Room Selection</span>
                        </div>
                        <div class="step" id="step2">
                            <span class="step-number">2</span>
                            <span class="step-text">Confirmation</span>
                        </div>
                    </div>

                    <!-- Step 1: Room Selection -->
                    <div class="booking-step-content" id="step1Content">
                        <div class="mb-4">
                            <label class="form-label">Room Type</label>
                            <select class="form-select" name="roomType" required>
                                <option value="">Select Room Type</option>
                                <option value="single">Single Room</option>
                                <option value="double">Double Sharing</option>
                                <option value="triple">Triple Sharing</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Block Preference</label>
                            <select class="form-select" name="block" required>
                                <option value="">Select Block</option>
                                <option value="A">Block A</option>
                                <option value="B">Block B</option>
                                <option value="C">Block C</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Floor Preference</label>
                            <select class="form-select" name="floor" required>
                                <option value="">Select Floor</option>
                                <option value="1">1st Floor</option>
                                <option value="2">2nd Floor</option>
                                <option value="3">3rd Floor</option>
                                <option value="4">4th Floor</option>
                            </select>
                        </div>

                        <div class="room-rates mb-4">
                            <h6>Room Rates (per semester)</h6>
                            <div class="rate-list">
                                <div class="rate-item">
                                    <span>Single Room:</span>
                                    <strong>ZMW 2,000</strong>
                                </div>
                                <div class="rate-item">
                                    <span>Double Sharing:</span>
                                    <strong>ZMW 1,500</strong>
                                </div>
                                <div class="rate-item">
                                    <span>Triple Sharing:</span>
                                    <strong>ZMW 1,200</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Confirmation -->
                    <div class="booking-step-content d-none" id="step2Content">
                        <div class="confirmation-details">
                            <h6 class="mb-3">Booking Summary</h6>
                            <div class="summary-item">
                                <span>Room Type:</span>
                                <strong id="summaryRoomType">-</strong>
                            </div>
                            <div class="summary-item">
                                <span>Block:</span>
                                <strong id="summaryBlock">-</strong>
                            </div>
                            <div class="summary-item">
                                <span>Floor:</span>
                                <strong id="summaryFloor">-</strong>
                            </div>
                            <div class="summary-item">
                                <span>Rate:</span>
                                <strong id="summaryRate">-</strong>
                            </div>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Payment will be due within 7 days of booking confirmation.
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="nextStep">Next</button>
                <button type="button" class="btn btn-primary d-none" id="confirmBooking">Confirm Booking</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<!-- Add this script before closing body tag -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap carousel manually
    var myCarousel = document.getElementById('roomImageSlider');
    var carousel = new bootstrap.Carousel(myCarousel, {
        interval: 3000,
        wrap: true,
        touch: true // Enable touch swiping on mobile
    });

    // Handle modal image preview
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const roomPreviews = document.querySelectorAll('.room-preview');

    roomPreviews.forEach(img => {
        img.addEventListener('click', function() {
            modalImg.src = this.src;
        });
    });

    // Add hover effect
    roomPreviews.forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.cursor = 'pointer';
        });
        
        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Enable carousel controls
    const prevButton = document.querySelector('.carousel-control-prev');
    const nextButton = document.querySelector('.carousel-control-next');
    
    prevButton.addEventListener('click', function() {
        carousel.prev();
    });
    
    nextButton.addEventListener('click', function() {
        carousel.next();
    });

    // Enable indicator buttons
    const indicators = document.querySelectorAll('.carousel-indicators button');
    indicators.forEach((button, index) => {
        button.addEventListener('click', function() {
            carousel.to(index);
        });
    });

    // Booking Form Handling
    const bookingForm = document.getElementById('bookingForm');
    const step1Content = document.getElementById('step1Content');
    const step2Content = document.getElementById('step2Content');
    const nextStepBtn = document.getElementById('nextStep');
    const confirmBookingBtn = document.getElementById('confirmBooking');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');

    // Room rates
    const roomRates = {
        'single': 2000,
        'double': 1500,
        'triple': 1200
    };

    let currentStep = 1;

    nextStepBtn.addEventListener('click', function() {
        if (currentStep === 1) {
            // Validate form
            const roomType = bookingForm.querySelector('[name="roomType"]').value;
            const block = bookingForm.querySelector('[name="block"]').value;
            const floor = bookingForm.querySelector('[name="floor"]').value;

            if (!roomType || !block || !floor) {
                alert('Please fill in all fields');
                return;
            }

            // Update summary
            document.getElementById('summaryRoomType').textContent = 
                bookingForm.querySelector('[name="roomType"] option:checked').text;
            document.getElementById('summaryBlock').textContent = 
                bookingForm.querySelector('[name="block"] option:checked').text;
            document.getElementById('summaryFloor').textContent = 
                bookingForm.querySelector('[name="floor"] option:checked').text;
            document.getElementById('summaryRate').textContent = 
                `$${roomRates[roomType]}`;

            // Show step 2
            step1Content.classList.add('d-none');
            step2Content.classList.remove('d-none');
            step1.classList.remove('active');
            step2.classList.add('active');
            nextStepBtn.classList.add('d-none');
            confirmBookingBtn.classList.remove('d-none');
            currentStep = 2;
        }
    });

    confirmBookingBtn.addEventListener('click', function() {
        // Here you would typically send the booking data to the server
        alert('Booking submitted successfully! You will receive a confirmation email shortly.');
        const modal = bootstrap.Modal.getInstance(document.getElementById('bookingModal'));
        modal.hide();
        
        // Reset form
        bookingForm.reset();
        step1Content.classList.remove('d-none');
        step2Content.classList.add('d-none');
        step1.classList.add('active');
        step2.classList.remove('active');
        nextStepBtn.classList.remove('d-none');
        confirmBookingBtn.classList.add('d-none');
        currentStep = 1;
    });
});
</script>

<?php include '../includes/footer.php'; ?> 