<?php
require_once __DIR__ . "/../db.php";

// Protect the page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// ---------------------------------------------------------
// HANDLE FORM SUBMISSIONS (Add, Edit, Update Status, Delete)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ADD NEW DAY PASS
    if (isset($_POST['action']) && $_POST['action'] === 'add_pass') {
        $guest_name = $conn->real_escape_string($_POST['guest_name']);
        $guest_phone = $conn->real_escape_string($_POST['guest_phone']);
        $visit_date = $conn->real_escape_string($_POST['visit_date']);
        $pass_type = $conn->real_escape_string($_POST['pass_type']);
        $number_of_adults = (int)$_POST['number_of_adults'];
        $number_of_children = (int)$_POST['number_of_children'];
        $amenity = $conn->real_escape_string($_POST['amenity']);
        $total_amount = (float)$_POST['total_amount'];
        $payment_method = $conn->real_escape_string($_POST['payment_method']);
        $status = $conn->real_escape_string($_POST['status']);

        $insert_sql = "INSERT INTO day_passes 
                        (guest_name, guest_phone, visit_date, pass_type, number_of_adults, number_of_children, amenity, total_amount, payment_method, status) 
                       VALUES 
                        ('$guest_name', '$guest_phone', '$visit_date', '$pass_type', $number_of_adults, $number_of_children, '$amenity', $total_amount, '$payment_method', '$status')";
        $conn->query($insert_sql);

        header("Location: managepasses.php");
        exit();
    }

    // EDIT EXISTING DAY PASS
    if (isset($_POST['action']) && $_POST['action'] === 'edit_pass') {
        $pass_id = (int)$_POST['pass_id'];
        $guest_name = $conn->real_escape_string($_POST['guest_name']);
        $guest_phone = $conn->real_escape_string($_POST['guest_phone']);
        $visit_date = $conn->real_escape_string($_POST['visit_date']);
        $pass_type = $conn->real_escape_string($_POST['pass_type']);
        $number_of_adults = (int)$_POST['number_of_adults'];
        $number_of_children = (int)$_POST['number_of_children'];
        $amenity = $conn->real_escape_string($_POST['amenity']);
        $total_amount = (float)$_POST['total_amount'];
        $payment_method = $conn->real_escape_string($_POST['payment_method']);
        $status = $conn->real_escape_string($_POST['status']);

        $update_sql = "UPDATE day_passes SET 
                        guest_name = '$guest_name', 
                        guest_phone = '$guest_phone', 
                        visit_date = '$visit_date',
                        pass_type = '$pass_type',
                        number_of_adults = $number_of_adults, 
                        number_of_children = $number_of_children, 
                        amenity = '$amenity',
                        total_amount = $total_amount, 
                        payment_method = '$payment_method',
                        status = '$status' 
                      WHERE id = $pass_id";
        $conn->query($update_sql);

        header("Location: managepasses.php");
        exit();
    }

    // DELETE DAY PASS
    if (isset($_POST['action']) && $_POST['action'] === 'delete_pass') {
        $pass_id = (int)$_POST['pass_id'];
        $delete_sql = "DELETE FROM day_passes WHERE id = $pass_id";
        $conn->query($delete_sql);

        header("Location: managepasses.php");
        exit();
    }

    // UPDATE DAY PASS STATUS ONLY (From the table dropdown)
    if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
        $pass_id = (int)$_POST['pass_id'];
        $new_status = $conn->real_escape_string($_POST['status']);
        $update_sql = "UPDATE day_passes SET status = '$new_status' WHERE id = $pass_id";
        $conn->query($update_sql);

        header("Location: managepasses.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Day Passes - Harajā Resort</title>
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

        .form-row {
            display: flex;
            gap: 1rem;
        }

        .form-row .form-group {
            flex: 1;
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
            margin-bottom: 10px;
        }

        .btn-primary:hover {
            opacity: 0.8;
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-color);
            border: 1px solid var(--border-color);
            width: 100%;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-danger {
            background: transparent;
            color: #d32f2f;
            border: 1px solid #d32f2f;
            padding: 5px 10px;
            font-size: 0.8rem;
        }

        .btn-danger:hover {
            background: #d32f2f;
            color: #fff;
        }

        /* Table */
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
        }

        .actions-flex {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        /* Status Badges for context */
        .text-pending {
            color: #e65100;
        }

        .text-confirmed {
            color: #00897b;
        }

        .text-cancelled {
            color: #c62828;
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

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            border: 1px solid var(--border-color);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-color);
            cursor: pointer;
        }
    </style>
</head>

<body class="dark-mode">

    <div class="sidebar">
        <h2>Harajā Admin</h2>
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="manage.php" class="nav-link">Manage Bookings</a>
        <a href="managepasses.php" class="nav-link active">Manage Passes</a>
        <a href="edit.php" class="nav-link">Manage Rooms & Rates</a>
        <a href="logout.php" class="nav-link logout-btn">Logout</a>
    </div>

    <div class="main-content">
        <h1 style="margin-bottom: 2rem;">Manage Day Passes</h1>

        <div class="manage-grid">
            <!-- Left Side: Add Pass Form -->
            <div class="panel" style="overflow-y: auto; max-height: calc(100vh - 120px);">
                <h3>Add Manual Day Pass</h3>
                <form id="add_pass_form" action="managepasses.php" method="POST">
                    <input type="hidden" name="action" value="add_pass">

                    <div class="form-group">
                        <label>Guest Name</label>
                        <input type="text" name="guest_name" class="form-control" required placeholder="e.g. Maria Santos">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="guest_phone" class="form-control" required placeholder="0917 123 4567">
                        </div>
                        <div class="form-group">
                            <label>Visit Date</label>
                            <input type="date" name="visit_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Pass Type</label>
                            <select name="pass_type" class="form-control" required>
                                <option value="Day Pass">Day Pass</option>
                                <option value="Night Pass">Night Pass</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amenity/Cabana</label>
                            <select name="amenity" class="form-control" required>
                                <option value="None">None</option>
                                <option value="Poolside Cabana">Poolside Cabana</option>
                                <option value="Garden Gazebo">Garden Gazebo</option>
                                <option value="VIP Lounge">VIP Lounge</option>
                                <option value="Picnic Table">Picnic Table</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Adults</label>
                            <input type="number" name="number_of_adults" class="form-control" min="1" required placeholder="1">
                        </div>
                        <div class="form-group">
                            <label>Children</label>
                            <input type="number" name="number_of_children" class="form-control" min="0" required value="0">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Total Amount (₱)</label>
                            <input type="number" name="total_amount" class="form-control" required placeholder="0.00" step="0.01">
                        </div>
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="Cash">Cash</option>
                                <option value="GCash">GCash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Card">Card</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="Confirmed">Confirmed</option>
                            <option value="Pending">Pending</option>
                            <option value="Done">Done</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Day Pass</button>
                </form>
            </div>

            <!-- Right Side: Existing Passes List -->
            <div class="panel" style="overflow-y: auto; max-height: calc(100vh - 120px);">
                <h3>All Day Passes</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Guest Info</th>
                            <th>Visit Details</th>
                            <th>Payment & Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $passes_sql = "SELECT * FROM day_passes ORDER BY id DESC";
                        $passes_result = $conn->query($passes_sql);

                        if ($passes_result && $passes_result->num_rows > 0) {
                            while ($row = $passes_result->fetch_assoc()) {
                                $visit_date = date('M d, Y', strtotime($row['visit_date']));
                                $raw_date = $row['visit_date'];

                                // Determine Status Color
                                $status_class = 'text-pending';
                                $current_status = isset($row['status']) ? $row['status'] : 'Pending';
                                if ($current_status == 'Confirmed') $status_class = 'text-confirmed';
                                if ($current_status == 'Cancelled') $status_class = 'text-cancelled';

                                echo "<tr>";

                                // Guest Info Column
                                echo "<td>
                                        <strong>" . htmlspecialchars($row['guest_name']) . "</strong><br>
                                        <span style='font-size: 0.8rem; opacity: 0.7;'>" . htmlspecialchars($row['guest_phone']) . "</span>
                                      </td>";

                                // Visit Details Column
                                echo "<td>
                                        <span style='color: var(--primary-gold);'>" . htmlspecialchars($row['pass_type']) . "</span><br>
                                        <span style='font-size: 0.85rem;'>" . $visit_date . "</span><br>
                                        <span style='font-size: 0.8rem; opacity: 0.8;'>" . htmlspecialchars($row['number_of_adults']) . " Adult(s), " . htmlspecialchars($row['number_of_children']) . " Child(ren)</span><br>
                                        <span style='font-size: 0.8rem; opacity: 0.7;'>Amenity: " . htmlspecialchars($row['amenity']) . "</span>
                                      </td>";

                                // Payment & Status Column
                                echo "<td>
                                        <strong>₱" . number_format($row['total_amount'], 2) . "</strong><br>
                                        <span style='font-size: 0.8rem;'>" . htmlspecialchars($row['payment_method']) . "</span><br><br>
                                        <form action='managepasses.php' method='POST' class='actions-flex'>
                                            <input type='hidden' name='action' value='update_status'>
                                            <input type='hidden' name='pass_id' value='{$row['id']}'>
                                            <select name='status' class='form-control' style='padding: 5px; width: 110px; font-size: 0.8rem;' onchange='this.form.submit()'>
                                                <option value='Pending' " . ($current_status == 'Pending' ? 'selected' : '') . ">Pending</option>
                                                <option value='Confirmed' " . ($current_status == 'Confirmed' ? 'selected' : '') . ">Confirmed</option>
                                                <option value='Done' " . ($current_status == 'Done' ? 'selected' : '') . ">Done</option>
                                                <option value='Cancelled' " . ($current_status == 'Cancelled' ? 'selected' : '') . ">Cancelled</option>
                                            </select>
                                        </form>
                                      </td>";

                                // Actions Column (Edit & Delete)
                                echo "<td>
                                        <div class='actions-flex' style='flex-direction: column; align-items: flex-start;'>
                                            <button type='button' class='btn btn-action-edit' 
                                                data-id='{$row['id']}'
                                                data-name='" . htmlspecialchars($row['guest_name'], ENT_QUOTES) . "'
                                                data-phone='" . htmlspecialchars($row['guest_phone'], ENT_QUOTES) . "'
                                                data-date='{$raw_date}'
                                                data-passtype='" . htmlspecialchars($row['pass_type'], ENT_QUOTES) . "'
                                                data-adults='{$row['number_of_adults']}'
                                                data-children='{$row['number_of_children']}'
                                                data-amenity='" . htmlspecialchars($row['amenity'], ENT_QUOTES) . "'
                                                data-amount='{$row['total_amount']}'
                                                data-payment='" . htmlspecialchars($row['payment_method'], ENT_QUOTES) . "'
                                                data-status='{$current_status}'
                                                style='width: 100%; margin: 0;'>Edit</button>
                                            
                                            <button type='button' class='btn btn-action-delete' 
                                                onclick='openDeleteModal({$row['id']}, \"" . htmlspecialchars($row['guest_name'], ENT_QUOTES) . "\")' 
                                                style='width: 100%; margin-top: 5px;'>Delete</button>
                                        </div>
                                      </td>";

                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No day passes found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- =========================================================
         1. EDIT DAY PASS MODAL
         ========================================================= -->
    <div class="modal-overlay" id="editModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 style="color: var(--primary-gold); margin: 0; border: none; padding: 0;">Edit Day Pass</h3>
                <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>

            <form id="edit_pass_form" action="managepasses.php" method="POST">
                <input type="hidden" name="action" value="edit_pass">
                <input type="hidden" name="pass_id" id="edit_pass_id" value="">

                <div class="form-group">
                    <label>Guest Name</label>
                    <input type="text" name="guest_name" id="edit_guest_name" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="guest_phone" id="edit_guest_phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Visit Date</label>
                        <input type="date" name="visit_date" id="edit_visit_date" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Pass Type</label>
                        <select name="pass_type" id="edit_pass_type" class="form-control" required>
                            <option value="Day Pass">Day Pass</option>
                            <option value="Night Pass">Night Pass</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amenity/Cabana</label>
                        <select name="amenity" id="edit_amenity" class="form-control" required>
                            <option value="None">None</option>
                            <option value="Poolside Cabana">Poolside Cabana</option>
                            <option value="Garden Gazebo">Garden Gazebo</option>
                            <option value="VIP Lounge">VIP Lounge</option>
                            <option value="Picnic Table">Picnic Table</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Adults</label>
                        <input type="number" name="number_of_adults" id="edit_number_of_adults" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Children</label>
                        <input type="number" name="number_of_children" id="edit_number_of_children" class="form-control" min="0" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Total Amount (₱)</label>
                        <input type="number" name="total_amount" id="edit_total_amount" class="form-control" required step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" id="edit_payment_method" class="form-control" required>
                            <option value="Cash">Cash</option>
                            <option value="GCash">GCash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Card">Card</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="edit_status" class="form-control">
                        <option value="Confirmed">Confirmed</option>
                        <option value="Pending">Pending</option>
                        <option value="Done">Done</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; margin: 0;">Update Day Pass</button>
                    <button type="button" class="btn btn-secondary" style="flex: 1;" onclick="closeEditModal()">Cancel</button>
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
                <h3 style="color: #d32f2f; margin: 0; border: none; padding: 0;">Confirm Deletion</h3>
                <button type="button" class="modal-close" onclick="closeDeleteModal()">&times;</button>
            </div>
            <p style="margin-bottom: 1.5rem; opacity: 0.9;">
                Are you sure you want to delete the pass for <strong id="delete_booking_title" style="color: var(--primary-gold);"></strong>? This action cannot be undone.
            </p>
            <form action="managepasses.php" method="POST">
                <input type="hidden" name="action" value="delete_pass">
                <input type="hidden" name="pass_id" id="delete_booking_id">

                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()" style="flex: 1;">Cancel</button>
                    <button type="submit" class="btn btn-danger" style="flex: 1; padding: 10px; font-size: 1rem;">Delete Pass</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript to handle Modals -->
    <script>
        // EDIT MODAL CONTROL
        document.querySelectorAll('.btn-action-edit').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_pass_id').value = this.dataset.id;
                document.getElementById('edit_guest_name').value = this.dataset.name;
                document.getElementById('edit_guest_phone').value = this.dataset.phone;
                document.getElementById('edit_visit_date').value = this.dataset.date;
                document.getElementById('edit_pass_type').value = this.dataset.passtype;
                document.getElementById('edit_number_of_adults').value = this.dataset.adults;
                document.getElementById('edit_number_of_children').value = this.dataset.children;
                document.getElementById('edit_amenity').value = this.dataset.amenity;
                document.getElementById('edit_total_amount').value = this.dataset.amount;
                document.getElementById('edit_payment_method').value = this.dataset.payment;
                document.getElementById('edit_status').value = this.dataset.status;

                document.getElementById('editModal').classList.add('active');
            });
        });

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

        // Close modals if user clicks outside the modal box
        window.onclick = function(event) {
            let editModal = document.getElementById('editModal');
            let deleteModal = document.getElementById('deleteModal');

            if (event.target == editModal) {
                closeEditModal();
            } else if (event.target == deleteModal) {
                closeDeleteModal();
            }
        }
    </script>
</body>

</html>