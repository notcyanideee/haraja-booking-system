<?php
require_once __DIR__ . "/../db.php";

// Protect the page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch active rooms once to use in both Add and Edit forms
$active_rooms = [];
$rooms_sql = "SELECT id, name, base_price FROM rooms WHERE is_active = 1";
$rooms_result = $conn->query($rooms_sql);
if ($rooms_result) {
    while ($room = $rooms_result->fetch_assoc()) {
        $active_rooms[] = $room;
    }
}

// 1. Fetch all active rooms
$all_rooms = [];
$rooms_query = $conn->query("SELECT * FROM rooms WHERE is_active = 1");
if ($rooms_query) {
    while ($r = $rooms_query->fetch_assoc()) {
        $all_rooms[] = $r;
    }
}

// 2. Fetch booked dates for ALL rooms
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

// ---------------------------------------------------------
// HANDLE FORM SUBMISSIONS (Add, Edit, Delete, Update Status)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ADD NEW BOOKING
    if (isset($_POST['action']) && $_POST['action'] === 'add_booking') {
        $guest_name = $conn->real_escape_string($_POST['guest_name']);
        $guest_phone = $conn->real_escape_string($_POST['guest_phone']);
        $room_id = (int)$_POST['room_id'];
        $check_in = $conn->real_escape_string($_POST['check_in']);
        $check_out = $conn->real_escape_string($_POST['check_out']);
        $total_amount = (float)$_POST['total_amount'];
        $status = $conn->real_escape_string($_POST['status']);

        $insert_sql = "INSERT INTO bookings (guest_name, guest_phone, room_id, check_in_date, check_out_date, total_amount, status) 
                       VALUES ('$guest_name', '$guest_phone', $room_id, '$check_in', '$check_out', $total_amount, '$status')";
        $conn->query($insert_sql);

        header("Location: manage.php");
        exit();
    }

    // UPDATE BOOKING STATUS DIRECTLY FROM TABLE
    if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
        $booking_id = (int)$_POST['booking_id'];
        $new_status = $conn->real_escape_string($_POST['status']);

        $update_sql = "UPDATE bookings SET status = '$new_status' WHERE id = $booking_id";
        $conn->query($update_sql);

        header("Location: manage.php");
        exit();
    }

    // DELETE BOOKING
    if (isset($_POST['action']) && $_POST['action'] === 'delete_booking') {
        $booking_id = (int)$_POST['booking_id'];
        $delete_sql = "DELETE FROM bookings WHERE id = $booking_id";
        $conn->query($delete_sql);

        header("Location: manage.php");
        exit();
    }

    // EDIT BOOKING DETAILS
    if (isset($_POST['action']) && $_POST['action'] === 'edit_booking') {
        $booking_id = (int)$_POST['booking_id'];

        $guest_name = $conn->real_escape_string($_POST['guest_name']);
        $guest_phone = $conn->real_escape_string($_POST['guest_phone']);
        $room_id = (int)$_POST['room_id'];
        $check_in = $conn->real_escape_string($_POST['check_in']);
        $check_out = $conn->real_escape_string($_POST['check_out']);
        $total_amount = (float)$_POST['total_amount'];

        $update_sql = "UPDATE bookings SET 
                        guest_name = '$guest_name',
                        guest_phone = '$guest_phone',
                        room_id = $room_id,
                        check_in_date = '$check_in',
                        check_out_date = '$check_out',
                        total_amount = $total_amount
                       WHERE id = $booking_id";
        $conn->query($update_sql);

        header("Location: manage.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Harajā Resort</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        /* Shared Dashboard Styles */
        :root {
            --primary-gold: #C8A54B;
            --bg-color: #f4f6f9;
            --card-bg: #ffffff;
            --text-color: #333333;
            --sidebar-bg: #1A1A1A;
            --border-color: #e0e0e0;
        }

        body.dark-mode {
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #e0e0e0;
            --border-color: #333333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Lato', sans-serif;
        }

        body {
            background: var(--bg-color);
            color: var(--text-color);
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: var(--sidebar-bg);
            color: #fff;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 {
            color: var(--primary-gold);
            text-align: center;
            margin-bottom: 2rem;
            font-family: 'Playfair Display', serif;
        }

        .nav-link {
            color: #ccc;
            text-decoration: none;
            padding: 12px 15px;
            display: block;
            border-radius: 4px;
            margin-bottom: 0.5rem;
            transition: 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(200, 165, 75, 0.2);
            color: var(--primary-gold);
        }

        .logout-btn {
            margin-top: auto;
            border: 1px solid var(--primary-gold);
            color: var(--primary-gold);
            text-align: center;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        /* Layout Grid */
        .manage-grid {
            display: grid;
            grid-template-columns: 1fr 2.5fr;
            gap: 2rem;
        }

        .panel {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .panel h3 {
            margin-bottom: 1.5rem;
            color: var(--primary-gold);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.5rem;
        }

        /* Forms */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-color);
            border-radius: 4px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-gold);
        }

        select.form-control option {
            background: var(--card-bg);
            color: var(--text-color);
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: bold;
        }

        .btn-primary {
            background: var(--primary-gold);
            color: #fff;
            width: 100%;
        }

        .btn-primary:hover {
            opacity: 0.8;
        }

        .btn-secondary {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-color);
        }

        .btn-secondary:hover {
            background: var(--border-color);
        }

        .btn-danger {
            background: #d32f2f;
            color: #fff;
        }

        .btn-danger:hover {
            opacity: 0.85;
        }

        .btn-action-edit {
            background: transparent;
            color: var(--primary-gold);
            border: 1px solid var(--primary-gold);
            padding: 5px 12px;
            font-size: 0.85rem;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-action-edit:hover {
            background: var(--primary-gold);
            color: #fff;
        }

        .btn-action-delete {
            background: transparent;
            color: #d32f2f;
            border: 1px solid #d32f2f;
            padding: 5px 12px;
            font-size: 0.85rem;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-action-delete:hover {
            background: #d32f2f;
            color: #fff;
        }

        /* Table */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.9rem;
        }

        th {
            color: var(--primary-gold);
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        .actions-flex {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        /* Inline Status Select */
        .status-select {
            padding: 5px;
            font-size: 0.85rem;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            border: 1px solid var(--border-color);
            background-color: var(--card-bg);
            color: var(--text-color);
        }

        .status-select:focus {
            outline: none;
            border-color: var(--primary-gold);
        }

        /* Modals */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-box {
            background: var(--card-bg);
            color: var(--text-color);
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            border-top: 4px solid var(--primary-gold);
            padding: 2rem;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-overlay.active .modal-box {
            transform: translateY(0);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-header h3 {
            color: var(--primary-gold);
            font-family: 'Playfair Display', serif;
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-color);
            font-size: 1.5rem;
            cursor: pointer;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }
    </style>
</head>

<body class="dark-mode">

    <div class="sidebar">
        <h2>Harajā Admin</h2>
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="manage.php" class="nav-link active">Manage Bookings</a>
        <a href="managepasses.php" class="nav-link ">Manage Passes</a>
        <a href="edit.php" class="nav-link">Manage Rooms & Rates</a>
        <a href="logout.php" class="nav-link logout-btn">Logout</a>
    </div>

    <div class="main-content">
        <h1 style="margin-bottom: 2rem;">Manage Bookings</h1>

        <div class="manage-grid">
            <!-- Left Side: Add Booking Form -->
            <div class="panel">
                <h3>Add Manual Booking</h3>
                <form action="manage.php" method="POST">
                    <input type="hidden" name="action" value="add_booking">

                    <div class="form-group">
                        <label>Guest Name</label>
                        <input type="text" name="guest_name" class="form-control" required placeholder="e.g. Maria Santos">
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="guest_phone" class="form-control" required placeholder="0917 123 4567">
                    </div>

                    <div class="form-group">
                        <label>Select Room</label>
                        <select name="room_id" id="add_room_id" class="form-control" required>
                            <option value="">-- Choose Room --</option>
                            <?php
                            foreach ($active_rooms as $room) {
                                echo "<option value='{$room['id']}'>{$room['name']} (₱" . number_format($room['base_price']) . "/night)</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <div class="form-group" style="flex: 1;">
                            <label>Check In</label>
                            <!-- Changed to type="text" for better Flatpickr compatibility -->
                            <input type="text" name="check_in" id="add_check_in" class="form-control" required placeholder="YYYY-MM-DD">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>Check Out</label>
                            <input type="text" name="check_out" id="add_check_out" class="form-control" required placeholder="YYYY-MM-DD">
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <div class="form-group" style="flex: 1;">
                            <label>Total Amount (₱)</label>
                            <input type="number" name="total_amount" class="form-control" required placeholder="0.00" step="0.01">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="Confirmed">Confirmed</option>
                                <option value="Pending">Pending</option>
                                <option value="Done">Done</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Booking</button>
                </form>
            </div>

            <!-- Right Side: Existing Bookings List -->
            <div class="panel">
                <h3>All Bookings</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Guest Details</th>
                                <th>Room</th>
                                <th>Dates</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT b.*, r.name AS room_name 
                                    FROM bookings b 
                                    LEFT JOIN rooms r ON b.room_id = r.id 
                                    ORDER BY b.id DESC";
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $safe_name = htmlspecialchars($row['guest_name']);
                                    $safe_phone = htmlspecialchars($row['guest_phone']);
                            ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $safe_name; ?></strong><br>
                                            <span style="font-size: 0.8rem; opacity: 0.7;"><?php echo $safe_phone; ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['room_name'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span style="font-size: 0.85rem;"><strong>In:</strong> <?php echo $row['check_in_date']; ?></span><br>
                                            <span style="font-size: 0.85rem;"><strong>Out:</strong> <?php echo $row['check_out_date']; ?></span>
                                        </td>
                                        <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>

                                        <!-- Inline Status Form -->
                                        <td>
                                            <form action="manage.php" method="POST" style="margin: 0;">
                                                <input type="hidden" name="action" value="update_status">
                                                <!-- made by ryu do not copy -->
                                                <!-- contact ryujosephbalanay@gmail for more info -->
                                                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                                <select name="status" class="status-select" onchange="this.form.submit()">
                                                    <option value="Confirmed" <?php if ($row['status'] == 'Confirmed') echo 'selected'; ?>>Confirmed</option>
                                                    <option value="Pending" <?php if ($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                                    <option value="Done" <?php if ($row['status'] == 'Done') echo 'selected'; ?>>Done</option>
                                                    <option value="Cancelled" <?php if ($row['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                                </select>
                                            </form>
                                        </td>

                                        <td>
                                            <div class="actions-flex">
                                                <button type="button" class="btn btn-action-edit"
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo $safe_name; ?>"
                                                    data-phone="<?php echo $safe_phone; ?>"
                                                    data-room="<?php echo $row['room_id']; ?>"
                                                    data-checkin="<?php echo $row['check_in_date']; ?>"
                                                    data-checkout="<?php echo $row['check_out_date']; ?>"
                                                    data-total="<?php echo $row['total_amount']; ?>"
                                                    onclick="openEditModal(this)">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-action-delete"
                                                    onclick="openDeleteModal(<?php echo $row['id']; ?>, '<?php echo addslashes($safe_name); ?>')">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align: center;'>No bookings found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================
         1. EDIT BOOKING MODAL (No Email, No Status)
         ========================================================= -->
    <div class="modal-overlay" id="editModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Edit Booking Information</h3>
                <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <form action="manage.php" method="POST">
                <input type="hidden" name="action" value="edit_booking">
                <input type="hidden" name="booking_id" id="edit_booking_id">

                <div class="form-group">
                    <label>Guest Name</label>
                    <input type="text" name="guest_name" id="edit_guest_name" class="form-control" required>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <div class="form-group" style="flex: 1;">
                        <label>Phone Number</label>
                        <input type="text" name="guest_phone" id="edit_guest_phone" class="form-control" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Room</label>
                        <select name="room_id" id="edit_room_id" class="form-control" required>
                            <option value="">-- Choose Room --</option>
                            <?php
                            foreach ($active_rooms as $room) {
                                echo "<option value='{$room['id']}'>{$room['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <div class="form-group" style="flex: 1;">
                        <label>Check In</label>
                        <input type="date" name="check_in" id="edit_check_in" class="form-control" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Check Out</label>
                        <input type="date" name="check_out" id="edit_check_out" class="form-control" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Total (₱)</label>
                        <input type="number" name="total_amount" id="edit_total_amount" class="form-control" required step="0.01">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="width: auto;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- =========================================================
         2. DELETE CONFIRMATION MODAL
         ========================================================= -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box" style="max-width: 420px; text-align: center;">
            <div class="modal-header">
                <h3 style="color: #d32f2f;">Confirm Deletion</h3>
                <button type="button" class="modal-close" onclick="closeDeleteModal()">&times;</button>
            </div>
            <p style="margin-bottom: 1.5rem; opacity: 0.9;">
                Are you sure you want to delete the booking for <strong id="delete_booking_title" style="color: var(--primary-gold);"></strong>? This action cannot be undone.
            </p>
            <form action="manage.php" method="POST">
                <input type="hidden" name="action" value="delete_booking">
                <input type="hidden" name="booking_id" id="delete_booking_id">

                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()" style="flex: 1;">Cancel</button>
                    <button type="submit" class="btn btn-danger" style="flex: 1;">Delete Booking</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript to handle Modals and Date Logic -->
    <script>
        // 1. DYNAMIC DATE LOGIC SETUP
        // Parse the PHP array into a usable JavaScript object
        const bookedDates = <?php echo $booked_dates_json; ?>;

        // Initialize Add Form Date Pickers
        let addCheckInPicker = flatpickr("#add_check_in", {
            minDate: "today",
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                // Ensure check out is strictly after check in
                addCheckOutPicker.set("minDate", dateStr);
            }
        });

        let addCheckOutPicker = flatpickr("#add_check_out", {
            minDate: "today",
            dateFormat: "Y-m-d"
        });

        // Initialize Edit Form Date Pickers
        let editCheckInPicker = flatpickr("#edit_check_in", {
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                editCheckOutPicker.set("minDate", dateStr);
            }
        });

        let editCheckOutPicker = flatpickr("#edit_check_out", {
            dateFormat: "Y-m-d"
        });

        // Function to update disabled dates based on selected room
        function updateDisabledDates(roomId, checkInInstance, checkOutInstance) {
            let disabledRanges = [];

            if (roomId && bookedDates[roomId]) {
                // Map the bookings for this room into Flatpickr's required format
                disabledRanges = bookedDates[roomId].map(booking => {
                    return {
                        from: booking.from,
                        to: booking.to
                    };
                });
            }

            // Apply the disabled dates to the calendars
            checkInInstance.set("disable", disabledRanges);
            checkOutInstance.set("disable", disabledRanges);

            // Clear the input fields to prevent invalid submissions when room changes
            checkInInstance.clear();
            checkOutInstance.clear();
        }

        // Attach Event Listeners to Room Dropdowns
        document.getElementById("add_room_id").addEventListener("change", function(e) {
            updateDisabledDates(e.target.value, addCheckInPicker, addCheckOutPicker);
        });

        document.getElementById("edit_room_id").addEventListener("change", function(e) {
            // Pass true to bypass clearing inputs if we are just opening the modal
            updateDisabledDates(e.target.value, editCheckInPicker, editCheckOutPicker);
        });


        // 2. MODAL CONTROLS
        function openEditModal(button) {
            const roomId = button.getAttribute('data-room');
            const checkInDate = button.getAttribute('data-checkin');
            const checkOutDate = button.getAttribute('data-checkout');

            document.getElementById('edit_booking_id').value = button.getAttribute('data-id');
            document.getElementById('edit_guest_name').value = button.getAttribute('data-name');
            document.getElementById('edit_guest_phone').value = button.getAttribute('data-phone');
            document.getElementById('edit_room_id').value = roomId;
            document.getElementById('edit_total_amount').value = button.getAttribute('data-total');

            // Apply disabled dates for the current room BEFORE setting the current values
            let disabledRanges = [];
            if (roomId && bookedDates[roomId]) {
                // Filter out the CURRENT booking's dates so the user can keep their existing dates
                disabledRanges = bookedDates[roomId].filter(booking => {
                    return !(booking.from === checkInDate && booking.to === checkOutDate);
                }).map(booking => {
                    return {
                        from: booking.from,
                        to: booking.to
                    };
                });
            }

            editCheckInPicker.set("disable", disabledRanges);
            editCheckOutPicker.set("disable", disabledRanges);

            // Now set the actual dates in the pickers
            editCheckInPicker.setDate(checkInDate);
            editCheckOutPicker.setDate(checkOutDate);

            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // DELETE MODAL CONTROL
        function openDeleteModal(id, guestName) {
            document.getElementById('delete_booking_id').value = id;
            document.getElementById('delete_booking_title').innerText = guestName;
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
        }

        // Close modal when clicking outside box
        window.onclick = function(event) {
            const editModal = document.getElementById('editModal');
            const deleteModal = document.getElementById('deleteModal');
            if (event.target === editModal) closeEditModal();
            if (event.target === deleteModal) closeDeleteModal();
        }
    </script>
</body>

</html>