<?php
ob_start();
include 'includes/header.php';
require_once 'db.php';

$success_message = '';
$error_message = '';

if (isset($_GET['success_bkg'])) {
    $booking_id = (int)$_GET['success_bkg'];
    $success_message = "Reservation confirmed! Your Booking Reference is #BKG-" . str_pad($booking_id, 5, "0", STR_PAD_LEFT);
}

// 1. Fetch all active rooms
$all_rooms = [];
$rooms_query = $conn->query("SELECT * FROM rooms WHERE is_active = 1");
if ($rooms_query) {
    while ($r = $rooms_query->fetch_assoc()) {
        $all_rooms[] = $r;
    }
}

// 2. NEW FIX: Fetch booked dates for ALL rooms (moved OUTSIDE the POST block)
// We group them by room_id so JavaScript can instantly switch dates when the user changes the dropdown
$all_booked_dates = [];
$dates_query = $conn->query("SELECT room_id, check_in_date, check_out_date FROM bookings WHERE status != 'Cancelled'");
if ($dates_query) {
    while ($row = $dates_query->fetch_assoc()) {
        $all_booked_dates[$row['room_id']][] = [
            "from" => $row['check_in_date'],
            "to" => $row['check_out_date']
        ];
    }
}
$booked_dates_json = json_encode($all_booked_dates);

$room_id_param = $_GET['room_id'] ?? $_GET['room'] ?? null;

// 3. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name       = trim($_POST['first_name'] ?? '');
    $last_name        = trim($_POST['last_name'] ?? '');
    $guest_name       = $first_name . ' ' . $last_name;
    $guest_email      = 'N/A';
    $guest_phone      = trim($_POST['phone'] ?? '');

    $selected_room_id = (int)($_POST['room_id'] ?? 0);
    $check_in_date    = $_POST['check_in'] ?? '';
    $check_out_date   = $_POST['check_out'] ?? '';

    if (!empty($first_name) && !empty($last_name) && !empty($guest_phone) && !empty($check_in_date) && !empty($check_out_date) && $selected_room_id > 0) {

        // CHECK FOR DOUBLE BOOKINGS
        $check_stmt = $conn->prepare("
            SELECT id FROM bookings 
            WHERE room_id = ? AND check_in_date < ? AND check_out_date > ? AND status != 'Cancelled'
        ");
        $check_stmt->bind_param("iss", $selected_room_id, $check_out_date, $check_in_date);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error_message = "Sorry, this room is already booked for these dates. Please select different dates.";
            $check_stmt->close();
        } else {
            $check_stmt->close();

            // Calculate totals and insert
            $price_stmt = $conn->prepare("SELECT base_price FROM rooms WHERE id = ?");
            $price_stmt->bind_param("i", $selected_room_id);
            $price_stmt->execute();
            $price_result = $price_stmt->get_result();
            $room_data = $price_result->fetch_assoc();
            $price_per_night = (float)($room_data['base_price'] ?? 0);
            $price_stmt->close();

            $d1 = new DateTime($check_in_date);
            $d2 = new DateTime($check_out_date);
            $nights = $d1->diff($d2)->days;
            if ($nights < 1) $nights = 1;

            $total_amount = $price_per_night * $nights;
            $payment_method = $_POST['payment_method'] ?? 'cash';

            $insert_stmt = $conn->prepare("INSERT INTO bookings (guest_name, guest_email, guest_phone, room_id, check_in_date, check_out_date, total_amount, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("sssissds", $guest_name, $guest_email, $guest_phone, $selected_room_id, $check_in_date, $check_out_date, $total_amount, $payment_method);

            if ($insert_stmt->execute()) {
                $new_booking_id = $insert_stmt->insert_id;
                $insert_stmt->close();
                header("Location: checkout.php?success_bkg=" . $new_booking_id);
                exit;
            } else {
                $error_message = "Database error: " . $conn->error;
                $insert_stmt->close();
            }
        }
    } else {
        $error_message = "Please fill in all required fields.";
    }
}
?>

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Custom Styles -->
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
        margin-bottom: 3rem;
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

    @media (max-width: 768px) {
        .checkout-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<section class="page-banner">
    <div class="container">
        <h1 class="section-title">Complete Reservation</h1>
        <p style="color: var(--primary-gold); font-size: 1.2rem;">Secure your slice of paradise.</p>
    </div>
</section>

<section class="section-padding" style="padding: 40px 0 80px;">
    <div class="container">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" style="text-align: center; padding: 3rem;">
                <h2 style="color: #155724; margin-bottom: 1rem;">🎉 Thank You!</h2>
                <p style="color: #155724; font-size: 1.1rem;"><?= htmlspecialchars($success_message) ?></p>
                <a href="index.php" class="btn btn-primary" style="margin-top: 2rem; padding: 12px 30px;">Return to Home</a>
            </div>
        <?php else: ?>
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <div class="booking-nav">
                <a href="checkout.php" class="nav-active">🏨 Book a Room</a>
                <a href="pass.php" class="nav-inactive">🎟️ Book a Day Pass</a>
            </div>

            <form action="" method="POST" id="checkout-form">
                <div class="checkout-grid" style="display: grid; grid-template-columns: 1.8fr 1.2fr; gap: 3rem;">

                    <!-- Left Column: Booking Details -->
                    <div>
                        <h3 style="margin-bottom: 1.5rem; color: var(--primary-gold);">Guest Information</h3>
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

                        <h3 style="margin-bottom: 1.5rem; margin-top: 3rem; color: var(--primary-gold);">Reservation Details</h3>
                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label class="form-label">Select Room *</label>
                            <select name="room_id" id="room_select" class="form-control" required>
                                <option value="" disabled selected>-- Choose a Room --</option>
                                <?php foreach ($all_rooms as $r): ?>
                                    <option value="<?= $r['id'] ?>" data-price="<?= $r['base_price'] ?>" <?= ($r['id'] == $room_id_param) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['name']) ?> — ₱<?= number_format($r['base_price'], 0) ?> / night
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- made by ryu do not copy -->
                        <!-- contact ryujosephbalanay@gmail for more info -->

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div class="form-group">
                                <label class="form-label">Check-in Date *</label>
                                <input type="text" name="check_in" id="check_in" class="form-control" placeholder="Select Date" required style="background-color: white; color: black;">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Check-out Date *</label>
                                <input type="text" name="check_out" id="check_out" class="form-control" placeholder="Select Date" required style="background-color: white; color: black;">
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Payment & Summary -->
                    <div>
                        <div class="summary-card">
                            <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Order Summary</h3>

                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.8rem; font-size: 1.05rem; font-weight: 600;">
                                <span id="summary-room-name" style="color: var(--text-color);">Select a room</span>
                                <span id="summary-room-price" style="color: var(--primary-gold);">₱ 0</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 0.9rem; color: #777;">
                                <span>Duration:</span>
                                <span id="summary-nights">0 Night(s)</span>
                            </div>

                            <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.4rem; margin-bottom: 2rem; border-top: 2px dashed var(--border-color); padding-top: 1.5rem;">
                                <span>Total</span>
                                <span style="color: var(--primary-gold);" id="summary-total">₱ 0</span>
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

                            <div id="qr-section" style="display: none; text-align: center; border: 1px solid var(--primary-gold); padding: 1.5rem; border-radius: 8px; background: rgba(200,165,75,0.05); margin-bottom: 1.5rem;">
                                <p style="margin-bottom: 1rem; font-weight: bold; color: var(--primary-gold);">Scan to Pay via GCash</p>
                                <img src="path/to/your/gcash-qr-code.jpg" alt="GCash QR" style="max-width: 200px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                <p style="font-size: 0.85rem; color: #666; margin-top: 1rem; line-height: 1.4;">
                                    Please save a screenshot of your receipt and show it to the front desk upon arrival.
                                </p>
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem; border-radius: 8px; font-weight: bold;">Confirm Reservation</button>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // 1. Load booked dates securely grouped by room ID
    const allBookedDates = <?php echo $booked_dates_json; ?>;

    // Define calendar variables
    let checkInPicker, checkOutPicker;

    // UI Elements
    const roomSelect = document.getElementById('room_select');
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const nameSpan = document.getElementById('summary-room-name');
    const priceSpan = document.getElementById('summary-room-price');
    const nightsSpan = document.getElementById('summary-nights');
    const totalSpan = document.getElementById('summary-total');

    function toggleQR(show) {
        document.getElementById('qr-section').style.display = show ? 'block' : 'none';
    }

    function updateTotals() {
        const selectedOpt = roomSelect.options[roomSelect.selectedIndex];
        if (!selectedOpt.value) return;

        const basePrice = parseFloat(selectedOpt.getAttribute('data-price')) || 0;
        const roomName = selectedOpt.text.split(' — ')[0].trim();

        nameSpan.textContent = roomName;
        priceSpan.textContent = '₱ ' + basePrice.toLocaleString('en-US');

        let nights = 0;
        if (checkInInput.value && checkOutInput.value) {
            const start = new Date(checkInInput.value);
            const end = new Date(checkOutInput.value);
            const diffTime = end - start;
            nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            if (nights < 1) nights = 1;
        } else {
            nights = 1;
        }

        const total = basePrice * nights;
        nightsSpan.textContent = nights + ' Night(s)';
        totalSpan.textContent = '₱ ' + total.toLocaleString('en-US');
    }

    // Initialize Flatpickr calendars based on the chosen room
    function initCalendars(roomId) {
        // Get dates for this specific room, or empty array if none
        let disabledDates = allBookedDates[roomId] || [];

        // Destroy existing calendars if they exist so we can rebuild them
        if (checkInPicker) checkInPicker.destroy();
        if (checkOutPicker) checkOutPicker.destroy();

        checkInPicker = flatpickr("#check_in", {
            minDate: "today",
            disable: disabledDates,
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates[0]) {
                    let nextDay = new Date(selectedDates[0]);
                    nextDay.setDate(nextDay.getDate() + 1);
                    checkOutPicker.set('minDate', nextDay);
                }
                updateTotals();
            }
        });

        checkOutPicker = flatpickr("#check_out", {
            minDate: "today",
            disable: disabledDates,
            dateFormat: "Y-m-d",
            onChange: updateTotals
        });
    }

    // Run when the page loads
    document.addEventListener('DOMContentLoaded', () => {
        if (roomSelect.value) {
            initCalendars(roomSelect.value);
            updateTotals();
        }

        // Whenever the user changes the room, rebuild the calendars to block the right dates
        roomSelect.addEventListener('change', function() {
            // Clear existing date inputs when room changes
            checkInInput.value = '';
            checkOutInput.value = '';

            initCalendars(this.value);
            updateTotals();
        });
    });
</script>

<?php include 'includes/footer.php'; ?>