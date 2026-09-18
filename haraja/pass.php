<?php
ob_start(); // Ensure header() redirects work perfectly.

include 'includes/header.php';
require_once 'db.php';

$success_message = '';
$error_message = '';

// Catch the success ID from the URL
if (isset($_GET['success_pass'])) {
    $pass_id = (int)$_GET['success_pass'];
    $success_message = "Your Day Pass request is submitted! Pass Reference: #PASS-" . str_pad($pass_id, 5, "0", STR_PAD_LEFT);
}

// Updated Pass Options with Adult & Child Pricing
$pass_options = [
    'day_pass' => [
        'name' => 'Day Pass (9:00 AM to 4:00 PM)',
        'adult' => 200,
        'child' => 150
    ],
    'night_pass' => [
        'name' => 'Night Pass (5:00 PM to 10:00 PM)',
        'adult' => 200,
        'child' => 150
    ],
    'full_day' => [
        'name' => 'Full Day Pass (9:00 AM to 9:00 PM)',
        'adult' => 300,
        'child' => 200
    ],
];

// Additional Amenities
$amenities = [
    'none' => ['name' => 'No Additional Cottage', 'price' => 0],
    'gazebo' => ['name' => 'Gazebo Cottage (Up to 15 guests)', 'price' => 1000],
    'umbrella' => ['name' => 'Umbrella Cottage (Up to 2 guests)', 'price' => 300],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name       = trim($_POST['first_name'] ?? '');
    $last_name        = trim($_POST['last_name'] ?? '');
    $guest_name       = $first_name . ' ' . $last_name;

    $email            = 'N/A';
    $phone            = trim($_POST['phone'] ?? '');
    $visit_date       = $_POST['visit_date'] ?? '';

    $pass_type        = $_POST['pass_type'] ?? 'day_pass';
    $num_adults       = max(1, (int)($_POST['adults'] ?? 1));
    $num_children     = max(0, (int)($_POST['children'] ?? 0));
    $amenity_type     = $_POST['amenity'] ?? 'none';

    $payment_method   = $_POST['payment_method'] ?? 'cash';

    // Calculate total amount on the backend for security
    $adult_total = $num_adults * $pass_options[$pass_type]['adult'];
    $child_total = $num_children * $pass_options[$pass_type]['child'];
    $amenity_total = $amenities[$amenity_type]['price'];

    $total_amount = $adult_total + $child_total + $amenity_total;

    if (!empty($first_name) && !empty($last_name) && !empty($phone) && !empty($visit_date)) {

        // Updated Prepared Statement matching the new DB structure
        $stmt = $conn->prepare("INSERT INTO day_passes (guest_name, email, guest_phone, visit_date, pass_type, number_of_adults, number_of_children, amenity, total_amount, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Types: s=string, i=integer, d=double(decimal) -> 5 strings, 2 ints, 1 string, 1 double, 1 string
        $stmt->bind_param("sssssiisds", $guest_name, $email, $phone, $visit_date, $pass_type, $num_adults, $num_children, $amenity_type, $total_amount, $payment_method);

        if ($stmt->execute()) {
            $new_pass_id = $stmt->insert_id;
            $stmt->close();
            header("Location: pass.php?success_pass=" . $new_pass_id);
            exit;
        } else {
            $error_message = "Error submitting order: " . $conn->error;
            $stmt->close();
        }
    } else {
        $error_message = "Please fill in all required fields.";
    }
}
?>

<!-- Custom Styles matching checkout.php -->
<style>
    body {
        background: var(--bg-color);
        color: var(--text-color);
    }

    .page-banner {
        padding: 120px 0 60px;
        background: var(--hero-bg);
        color: var(--hero-text);
        text-align: center;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        font-weight: 500;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* Nav Tabs */
    .booking-nav {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 1rem;
    }

    .booking-nav a {
        flex: 1;
        text-align: center;
        padding: 15px;
        text-decoration: none;
        font-weight: bold;
        border-radius: 8px;
        transition: 0.3s;
        font-size: 1.1rem;
    }

    .nav-active {
        background: var(--primary-gold);
        color: #fff !important;
        box-shadow: 0 4px 15px rgba(200, 165, 75, 0.4);
    }

    .nav-inactive {
        background: transparent;
        color: var(--text-color);
        border: 1px solid var(--border-color);
    }

    .nav-inactive:hover {
        border-color: var(--primary-gold);
        color: var(--primary-gold);
    }

    /* Forms */
    .form-control {
        width: 100%;
        padding: 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--card-bg);
        color: var(--text-color);
        font-size: 1rem;
        transition: 0.3s;
    }

    .form-control:focus {
        border-color: var(--primary-gold);
        outline: none;
        box-shadow: 0 0 0 3px rgba(200, 165, 75, 0.2);
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        font-size: 0.95rem;
    }

    /* Order Summary Card */
    .summary-card {
        background: var(--card-bg);
        padding: 2.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border-color);
        position: sticky;
        top: 100px;
    }

    /* Radio Buttons */
    .payment-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        cursor: pointer;
        transition: 0.3s;
    }

    .payment-option:hover {
        border-color: var(--primary-gold);
        background: rgba(200, 165, 75, 0.05);
    }

    .payment-option input[type="radio"] {
        transform: scale(1.2);
        accent-color: var(--primary-gold);
    }

    /* Slideshow Styles */
    .slideshow-container {
        position: relative;
        max-width: 100%;
        height: 350px;
        overflow: hidden;
        border-radius: 12px;
        margin-bottom: 3rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .mySlides {
        display: none;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .fade {
        animation: fadeEffect 1.5s;
    }

    @keyframes fadeEffect {
        from {
            opacity: 0.5;
        }

        to {
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        .checkout-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<section class="page-banner">
    <div class="container">
        <h1 class="section-title">Resort Pass Booking</h1>
        <p style="color: var(--primary-gold); font-size: 1.2rem;">Enjoy luxury resort access for the day.</p>
    </div>
</section>

<section class="section-padding" style="padding: 40px 0 80px;">
    <div class="container">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" style="text-align: center; padding: 3rem;">
                <h2 style="color: #155724; margin-bottom: 1rem;">🎟️ Pass Reserved!</h2>
                <p style="color: #155724; font-size: 1.1rem;"><?= htmlspecialchars($success_message) ?></p>
                <p style="font-size: 0.95rem; margin-top: 1rem; color: #155724; opacity: 0.9;">Please present your Pass Reference at the Resort Front Desk upon arrival.</p>
                <a href="index.php" class="btn btn-primary" style="margin-top: 2rem; padding: 12px 30px;">Return to Home</a>
            </div>
        <?php else: ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <!-- Navigation Toggler -->
            <div class="booking-nav">
                <a href="checkout.php" class="nav-inactive">🏨 Book a Room</a>
                <a href="pass.php" class="nav-active">🎟️ Book a Day Pass</a>
            </div>



            <form action="" method="POST" id="pass-form">
                <div class="checkout-grid" style="display: grid; grid-template-columns: 1.8fr 1.2fr; gap: 3rem;">

                    <!-- Left Column: Details -->
                    <div>
                        <h3 style="margin-bottom: 1.5rem; color: var(--primary-gold);">Visitor Information</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div class="form-group">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-control" required placeholder="Juan">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-control" required placeholder="Dela Cruz">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 1.5rem;">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" name="phone" class="form-control" required placeholder="0917 123 4567">
                        </div>

                        <h3 style="margin-bottom: 1.5rem; margin-top: 3rem; color: var(--primary-gold);">Pass Selection & Date</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                            <div class="form-group">
                                <label class="form-label">Select Pass Type *</label>
                                <select name="pass_type" id="pass_type" class="form-control" required>
                                    <?php foreach ($pass_options as $key => $pass): ?>
                                        <option value="<?= $key ?>" data-adult="<?= $pass['adult'] ?>" data-child="<?= $pass['child'] ?>">
                                            <?= htmlspecialchars($pass['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date of Visit *</label>
                                <input type="date" name="visit_date" class="form-control" min="<?= date('Y-m-d') ?>" style="background-color: white; color: black;" required>
                            </div>
                        </div>

                        <!-- Adults and Children Guests -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                            <div class="form-group">
                                <label class="form-label">Number of Adults *</label>
                                <input type="number" name="adults" id="adults" min="1" max="50" value="1" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Number of Children (Under 12)</label>
                                <input type="number" name="children" id="children" min="0" max="50" value="0" class="form-control">
                            </div>
                        </div>

                        <h3 style="margin-bottom: 1.5rem; margin-top: 3rem; color: var(--primary-gold);">Additional Amenities</h3>
                        <div class="form-group" style="margin-bottom: 2rem;">
                            <label class="form-label">Select Cottage / Gazebo</label>
                            <select name="amenity" id="amenity" class="form-control">
                                <?php foreach ($amenities as $key => $amenity): ?>
                                    <option value="<?= $key ?>" data-price="<?= $amenity['price'] ?>">
                                        <?= htmlspecialchars($amenity['name']) ?> <?= $amenity['price'] > 0 ? "— ₱" . number_format($amenity['price']) : "" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Right Column: Summary & Payment -->
                    <div>
                        <div class="summary-card">
                            <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Booking Summary</h3>

                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.8rem; font-size: 1.05rem; font-weight: 600;">
                                <span id="summary-pass-name" style="color: var(--primary-gold);">Day Pass</span>
                            </div>

                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem; color: #555;">
                                <span id="summary-adults-text">1 Adult(s)</span>
                                <span id="summary-adults-price">₱ 200</span>
                            </div>

                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem; color: #555;">
                                <span id="summary-children-text">0 Child(ren)</span>
                                <span id="summary-children-price">₱ 0</span>
                            </div>

                            <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 0.9rem; color: #555;">
                                <span id="summary-amenity-text">No Additional Cottage</span>
                                <span id="summary-amenity-price">₱ 0</span>
                            </div>

                            <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.4rem; margin-bottom: 2rem; border-top: 2px dashed var(--border-color); padding-top: 1.5rem;">
                                <span>Total</span>
                                <span style="color: var(--primary-gold);" id="summary-total">₱ 200</span>
                            </div>

                            <h4 style="margin-bottom: 1rem;">Payment Method</h4>
                            <div style="display: flex; flex-direction: column; gap: 0.8rem; margin-bottom: 1.5rem;">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="cash" checked onclick="toggleQR(false)">
                                    <span style="font-weight: 500;">Cash (Pay at Resort)</span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="qr" onclick="toggleQR(true)">
                                    <span style="font-weight: 500;">GCash / QR Payment</span>
                                </label>
                            </div>

                            <!-- Hidden QR Code Section -->
                            <div id="qr-section" style="display: none; text-align: center; border: 1px solid var(--primary-gold); padding: 1.5rem; border-radius: 8px; background: rgba(200,165,75,0.05); margin-bottom: 1.5rem;">
                                <p style="margin-bottom: 1rem; font-weight: bold; color: var(--primary-gold);">Scan to Pay via GCash</p>
                                <img src="path/to/your/gcash-qr-code.jpg" alt="GCash QR Code" style="max-width: 200px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                <p style="font-size: 0.85rem; color: #666; margin-top: 1rem; line-height: 1.4;">
                                    Please save a screenshot of your transaction receipt and show it to the front desk upon arrival.
                                </p>
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem; border-radius: 8px; font-weight: bold;">Checkout Pass</button>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>

<script>
    // --- QR Toggle ---
    function toggleQR(show) {
        document.getElementById('qr-section').style.display = show ? 'block' : 'none';
    }

    // --- Dynamic Pricing Summary ---
    document.addEventListener('DOMContentLoaded', () => {
        const passSelect = document.getElementById('pass_type');
        const amenitySelect = document.getElementById('amenity');
        const adultsInput = document.getElementById('adults');
        const childrenInput = document.getElementById('children');

        function updatePassSummary() {
            // Get Prices
            const selectedPass = passSelect.options[passSelect.selectedIndex];
            const adultPrice = parseFloat(selectedPass.getAttribute('data-adult')) || 0;
            const childPrice = parseFloat(selectedPass.getAttribute('data-child')) || 0;

            const selectedAmenity = amenitySelect.options[amenitySelect.selectedIndex];
            const amenityPrice = parseFloat(selectedAmenity.getAttribute('data-price')) || 0;

            // Get Quantities
            const adults = parseInt(adultsInput.value) || 0;
            const children = parseInt(childrenInput.value) || 0;

            // Calculate Totals
            const totalAdults = adults * adultPrice;
            const totalChildren = children * childPrice;
            const grandTotal = totalAdults + totalChildren + amenityPrice;

            // Update UI
            document.getElementById('summary-pass-name').textContent = selectedPass.text.split('(')[0].trim();

            document.getElementById('summary-adults-text').textContent = adults + ' Adult(s) @ ₱' + adultPrice;
            document.getElementById('summary-adults-price').textContent = '₱ ' + totalAdults.toLocaleString('en-US');

            document.getElementById('summary-children-text').textContent = children + ' Child(ren) @ ₱' + childPrice;
            document.getElementById('summary-children-price').textContent = '₱ ' + totalChildren.toLocaleString('en-US');

            document.getElementById('summary-amenity-text').textContent = selectedAmenity.text.split('—')[0].trim();
            document.getElementById('summary-amenity-price').textContent = '₱ ' + amenityPrice.toLocaleString('en-US');

            document.getElementById('summary-total').textContent = '₱ ' + grandTotal.toLocaleString('en-US');
        }

        // Listen for changes
        passSelect.addEventListener('change', updatePassSummary);
        amenitySelect.addEventListener('change', updatePassSummary);
        adultsInput.addEventListener('input', updatePassSummary);
        childrenInput.addEventListener('input', updatePassSummary);

        // Initialize Summary on load
        updatePassSummary();
    });

    // --- Automatic Image Slideshow ---
    let slideIndex = 0;
    showSlides();

    function showSlides() {
        let i;
        let slides = document.getElementsByClassName("mySlides");
        // Hide all slides
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;
        // Reset index if out of bounds
        if (slideIndex > slides.length) {
            slideIndex = 1
        }
        // Display current slide
        if (slides.length > 0) {
            slides[slideIndex - 1].style.display = "block";
        }
        // Change image every 4 seconds
        setTimeout(showSlides, 4000);
    }
</script>

<?php include 'includes/footer.php'; ?>