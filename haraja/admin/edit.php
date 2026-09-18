<?php
require_once __DIR__ . "/../db.php";

// Protect the page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// ---------------------------------------------------------
// HANDLE FORM SUBMISSIONS (Add, Edit/Update, Delete)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ADD NEW ROOM
    if (isset($_POST['action']) && $_POST['action'] === 'add_room') {
        $name = $conn->real_escape_string($_POST['name']);
        $base_price = (float)$_POST['base_price'];
        $capacity = $conn->real_escape_string($_POST['capacity']);
        $description = $conn->real_escape_string($_POST['description']);
        $additional_fees = $conn->real_escape_string($_POST['additional_fees']);

        $sql = "INSERT INTO rooms (name, base_price, capacity, description, additional_fees, is_active) 
                VALUES ('$name', $base_price, '$capacity', '$description', '$additional_fees', 1)";
        $conn->query($sql);

        header("Location: edit.php");
        exit();
    }

    // UPDATE ROOM
    if (isset($_POST['action']) && $_POST['action'] === 'update_room') {
        $id = (int)$_POST['room_id'];
        $name = $conn->real_escape_string($_POST['name']);
        $base_price = (float)$_POST['base_price'];
        $capacity = $conn->real_escape_string($_POST['capacity']);
        $description = $conn->real_escape_string($_POST['description']);
        $additional_fees = $conn->real_escape_string($_POST['additional_fees']);

        $sql = "UPDATE rooms SET 
                name = '$name', 
                base_price = '$base_price', 
                capacity = '$capacity', 
                description = '$description', 
                additional_fees = '$additional_fees' 
                WHERE id = $id";
        $conn->query($sql);

        header("Location: edit.php");
        exit();
    }

    // DELETE ROOM
    if (isset($_POST['action']) && $_POST['action'] === 'delete_room') {
        $id = (int)$_POST['room_id'];
        $sql = "DELETE FROM rooms WHERE id = $id";
        $conn->query($sql);

        header("Location: edit.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms & Rates - Harajā Resort</title>
    <style>
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

        /* Grid Layout */
        .edit-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
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

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: bold;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary-gold);
            color: #fff;
            width: 100%;
        }

        .btn-primary:hover {
            opacity: 0.85;
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
        }

        .btn-action-delete:hover {
            background: #d32f2f;
            color: #fff;
        }

        /* Room Cards List */
        .room-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            margin-bottom: 1rem;
            background: var(--card-bg);
            align-items: center;
        }

        .room-item img {
            width: 90px;
            height: 70px;
            object-fit: cover;
            border-radius: 4px;
        }

        .room-info {
            flex: 1;
        }

        .room-info h4 {
            font-size: 1.1rem;
            margin-bottom: 0.2rem;
        }

        .room-info p {
            font-size: 0.85rem;
            opacity: 0.7;
            margin-bottom: 0.2rem;
        }

        .room-info .price {
            color: var(--primary-gold);
            font-weight: bold;
            font-size: 0.95rem;
        }

        .room-actions {
            display: flex;
            gap: 0.5rem;
            flex-direction: column;
        }

        /* ---------------------------------------------------------
           MODAL STYLES
           --------------------------------------------------------- */
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
            max-width: 550px;
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

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <h2>Harajā Admin</h2>
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="manage.php" class="nav-link">Manage Bookings</a>
        <a href="managepasses.php" class="nav-link ">Manage Passes</a>
        <a href="edit.php" class="nav-link active">Manage Rooms & Rates</a>
        <a href="logout.php" class="nav-link logout-btn">Logout</a>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <h1 style="margin-bottom: 2rem;">Manage Rooms & Pricing</h1>

        <div class="edit-grid">
            <!-- Left Side: Add Room Form -->
            <div class="panel">
                <h3>Add New Room</h3>
                <form action="edit.php" method="POST">
                    <input type="hidden" name="action" value="add_room">

                    <div class="form-group">
                        <label>Room Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Ocean View Suite">
                    </div>

                    <div class="form-group">
                        <label>Base Price per Night (₱)</label>
                        <input type="number" step="0.01" name="base_price" class="form-control" required placeholder="15000">
                    </div>

                    <div class="form-group">
                        <label>Capacity</label>
                        <input type="text" name="capacity" class="form-control" required placeholder="e.g. 2 Guests or 2 Adults | 1 Child">
                    </div>

                    <div class="form-group">
                        <label>Additional Fees / Tags</label>
                        <input type="text" name="additional_fees" class="form-control" placeholder="e.g. Breakfast Included or Extra Bed Fee ₱1500">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" required placeholder="Describe the room, ambiance, amenities..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Save New Room</button>
                </form>
            </div>

            <!-- Right Side: Existing Rooms List -->
            <div class="panel">
                <h3>Current Rooms on Website</h3>

                <?php
                $sql = "SELECT * FROM rooms ORDER BY id DESC";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($room = $result->fetch_assoc()) {
                        // Escape outputs safely for HTML data attributes
                        $safe_name = htmlspecialchars($room['name'], ENT_QUOTES);
                        $safe_capacity = htmlspecialchars($room['capacity'], ENT_QUOTES);
                        $safe_desc = htmlspecialchars($room['description'], ENT_QUOTES);
                        $safe_fees = htmlspecialchars($room['additional_fees'], ENT_QUOTES);
                ?>
                        <div class="room-item">

                            <div class="room-info">
                                <h4><?php echo $safe_name; ?></h4>
                                <p class="price">₱ <?php echo number_format($room['base_price'], 2); ?> / night</p>
                                <p><?php echo $safe_capacity; ?></p>
                                <?php if (!empty($safe_fees)): ?>
                                    <p style="color: var(--primary-gold);"><small>🏷️ <?php echo $safe_fees; ?></small></p>
                                <?php endif; ?>
                            </div>
                            <div class="room-actions">
                                <button type="button" class="btn btn-action-edit"
                                    data-id="<?php echo $room['id']; ?>"
                                    data-name="<?php echo $safe_name; ?>"
                                    data-price="<?php echo $room['base_price']; ?>"
                                    data-capacity="<?php echo $safe_capacity; ?>"
                                    data-fees="<?php echo $safe_fees; ?>"
                                    data-description="<?php echo $safe_desc; ?>"
                                    onclick="openEditModal(this)">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-action-delete"
                                    onclick="openDeleteModal(<?php echo $room['id']; ?>, '<?php echo addslashes($safe_name); ?>')">
                                    Delete
                                </button>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p>No rooms found in database.</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- =========================================================
         1. EDIT ROOM MODAL
         ========================================================= -->
    <div class="modal-overlay" id="editModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Edit Room Details</h3>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <form action="edit.php" method="POST">
                <input type="hidden" name="action" value="update_room">
                <input type="hidden" name="room_id" id="edit_room_id">

                <div class="form-group">
                    <label>Room Name</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Base Price per Night (₱)</label>
                    <input type="number" step="0.01" name="base_price" id="edit_price" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Capacity</label>
                    <input type="text" name="capacity" id="edit_capacity" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Additional Fees / Tags</label>
                    <input type="text" name="additional_fees" id="edit_fees" class="form-control">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_description" class="form-control" required></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="width: auto;">Update Room</button>
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
                <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
            </div>
            <p style="margin-bottom: 1.5rem; opacity: 0.9;">
                Are you sure you want to delete <strong id="delete_room_title" style="color: var(--primary-gold);"></strong>? This action cannot be undone.
            </p>
            <form action="edit.php" method="POST">
                <input type="hidden" name="action" value="delete_room">
                <input type="hidden" name="room_id" id="delete_room_id">

                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()" style="flex: 1;">Cancel</button>
                    <button type="submit" class="btn btn-danger" style="flex: 1;">Delete Room</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript to handle Modals -->
    <script>
        // EDIT MODAL CONTROLE
        function openEditModal(button) {
            document.getElementById('edit_room_id').value = button.getAttribute('data-id');
            document.getElementById('edit_name').value = button.getAttribute('data-name');
            document.getElementById('edit_price').value = button.getAttribute('data-price');
            document.getElementById('edit_capacity').value = button.getAttribute('data-capacity');
            document.getElementById('edit_fees').value = button.getAttribute('data-fees');
            document.getElementById('edit_description').value = button.getAttribute('data-description');

            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // DELETE MODAL CONTROL
        function openDeleteModal(id, roomName) {
            document.getElementById('delete_room_id').value = id;
            document.getElementById('delete_room_title').innerText = roomName;

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