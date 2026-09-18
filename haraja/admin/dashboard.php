<?php
require_once __DIR__ . "/../db.php";

// Protect the page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// ---------------------------------------------------------
// 1. FETCH DASHBOARD STATISTICS
// ---------------------------------------------------------

// Monthly Revenue
$rev_sql = "SELECT SUM(total_amount) AS revenue FROM bookings 
            WHERE status IN ('Confirmed', 'Done') 
            AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
            AND YEAR(created_at) = YEAR(CURRENT_DATE())";
$rev_result = $conn->query($rev_sql);
$monthly_revenue = ($rev_result && $row = $rev_result->fetch_assoc()) ? ($row['revenue'] ?: 0) : 0;

// Monthly Day Pass Revenue
$pass_rev_sql = "SELECT SUM(total_amount) AS revenue FROM day_passes 
                 WHERE status IN ('Confirmed', 'Done') 
                 AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
                 AND YEAR(created_at) = YEAR(CURRENT_DATE())";
$pass_rev_result = $conn->query($pass_rev_sql);
$monthly_pass_revenue = ($pass_rev_result && $row = $pass_rev_result->fetch_assoc()) ? ($row['revenue'] ?: 0) : 0;

$total = $monthly_revenue + $monthly_pass_revenue;

// Active Bookings
$active_sql = "SELECT COUNT(*) AS active_count FROM bookings WHERE status = 'Confirmed' AND check_out_date >= CURRENT_DATE()";
$active_result = $conn->query($active_sql);
$active_bookings = ($active_result) ? $active_result->fetch_assoc()['active_count'] : 0;

// Total Available Rooms
$rooms_sql = "SELECT COUNT(*) AS total_rooms FROM rooms WHERE is_active = 1";
$rooms_result = $conn->query($rooms_sql);
$total_rooms = ($rooms_result) ? $rooms_result->fetch_assoc()['total_rooms'] : 0;

// Pending Requests
$pending_sql = "SELECT COUNT(*) AS pending_count FROM bookings WHERE status = 'Pending'";
$pending_result = $conn->query($pending_sql);
$pending_requests = ($pending_result) ? $pending_result->fetch_assoc()['pending_count'] : 0;


// ---------------------------------------------------------
// 2. FETCH CHART DATA (Last 7 Days Occupancy)
// ---------------------------------------------------------
$chart_labels = [];
$chart_data = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_labels[] = date('D', strtotime($date));

    $occ_sql = "SELECT COUNT(*) AS occupied FROM bookings 
                WHERE status IN ('Confirmed', 'Done') 
                AND check_in_date <= '$date' AND check_out_date > '$date'";
    $occ_result = $conn->query($occ_sql);
    $occupied = ($occ_result) ? $occ_result->fetch_assoc()['occupied'] : 0;

    $percentage = ($total_rooms > 0) ? round(($occupied / $total_rooms) * 100) : 0;
    $chart_data[] = $percentage;
}

$chart_labels_json = json_encode($chart_labels);
$chart_data_json = json_encode($chart_data);


// ---------------------------------------------------------
// 3. PAGINATION & FILTER LOGIC FOR BOOKINGS
// ---------------------------------------------------------
$limit = 10; // Number of records per page

$status_filter = isset($_GET['filter']) ? $_GET['filter'] : 'active';
$where_clause = "WHERE b.status NOT IN ('Done', 'Cancelled')"; // Default
if ($status_filter === 'all') $where_clause = "";
elseif (in_array($status_filter, ['Pending', 'Confirmed', 'Cancelled', 'Done'])) {
    $where_clause = "WHERE b.status = '$status_filter'";
}

// Bookings Pagination Math
$booking_page = isset($_GET['booking_page']) && is_numeric($_GET['booking_page']) ? (int)$_GET['booking_page'] : 1;
$booking_offset = ($booking_page - 1) * $limit;

$total_bookings_sql = "SELECT COUNT(*) AS total FROM bookings b $where_clause";
$total_bookings_result = $conn->query($total_bookings_sql);
$total_bookings = $total_bookings_result ? $total_bookings_result->fetch_assoc()['total'] : 0;
$booking_total_pages = ceil($total_bookings / $limit);


// ---------------------------------------------------------
// 4. PAGINATION, STATS & FILTER LOGIC FOR PASSES
// ---------------------------------------------------------
$pass_stats_sql = "
    SELECT 
        SUM(CASE WHEN pass_type = 'Day Pass' THEN 1 ELSE 0 END) AS day_passes,
        SUM(CASE WHEN pass_type = 'Night Pass' THEN 1 ELSE 0 END) AS night_passes,
        SUM(CASE WHEN pass_type = 'All Day' THEN 1 ELSE 0 END) AS all_day_passes
    FROM day_passes 
    WHERE status IN ('Confirmed', 'Done') 
    AND MONTH(visit_date) = MONTH(CURRENT_DATE()) 
    AND YEAR(visit_date) = YEAR(CURRENT_DATE())
";
$stats_result = $conn->query($pass_stats_sql);
$pass_stats = $stats_result ? $stats_result->fetch_assoc() : null;

$day_pass_count = $pass_stats['day_passes'] ?? 0;
$night_pass_count = $pass_stats['night_passes'] ?? 0;
$all_day_count = $pass_stats['all_day_passes'] ?? 0;

$pass_status_filter = isset($_GET['pass_filter']) ? $_GET['pass_filter'] : 'active';
$pass_where_clause = "WHERE status NOT IN ('Done', 'Cancelled')"; // Default
if ($pass_status_filter === 'all') $pass_where_clause = "";
elseif (in_array($pass_status_filter, ['Pending', 'Confirmed', 'Cancelled', 'Done'])) {
    $pass_where_clause = "WHERE status = '$pass_status_filter'";
}

// Passes Pagination Math
$pass_page = isset($_GET['pass_page']) && is_numeric($_GET['pass_page']) ? (int)$_GET['pass_page'] : 1;
$pass_offset = ($pass_page - 1) * $limit;

$total_passes_sql = "SELECT COUNT(*) AS total FROM day_passes $pass_where_clause";
$total_passes_result = $conn->query($total_passes_sql);
$total_passes = $total_passes_result ? $total_passes_result->fetch_assoc()['total'] : 0;
$pass_total_pages = ceil($total_passes / $limit);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Harajā Resort</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            border-top: 3px solid var(--primary-gold);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .stat-card h4 {
            font-size: 0.9rem;
            color: #888;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .stat-card h2 {
            font-size: 2rem;
            color: var(--text-color);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .panel {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.5rem;
        }

        .panel-header h3 {
            color: var(--primary-gold);
        }

        /* Filter Dropdown */
        .filter-select {
            background: transparent;
            color: var(--text-color);
            border: 1px solid var(--border-color);
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            cursor: pointer;
        }

        .filter-select option {
            background: var(--card-bg);
            color: var(--text-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
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

        /* Dynamic Badges */
        .badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            background: #e0f2f1;
            color: #00897b;
            font-weight: bold;
        }

        .badge.pending {
            background: #fff3e0;
            color: #e65100;
        }

        .badge.cancelled {
            background: #ffebee;
            color: #c62828;
        }

        .badge.done {
            background: #e8eaf6;
            color: #3f51b5;
        }

        /* Pagination Controls */
        .pagination {
            display: flex;
            justify-content: flex-end;
            gap: 5px;
            margin-top: auto;
            /* Pushes to bottom of panel */
            padding-top: 10px;
        }

        .page-link {
            padding: 5px 10px;
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            color: var(--text-color);
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.85rem;
            transition: 0.2s;
        }

        .page-link:hover,
        .page-link.active {
            background: var(--primary-gold);
            color: #fff;
            border-color: var(--primary-gold);
        }

        .page-link.disabled {
            opacity: 0.4;
            pointer-events: none;
        }

        .export-btn {
            background: var(--primary-gold);
            color: #fff;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: bold;
            margin-left: 10px;
            transition: 0.2s;
        }

        .export-btn:hover {
            background: #a6893e;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Toast Notification Styles */
        #toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            background: var(--card-bg, #1e1e1e);
            color: var(--text-color, #e0e0e0);
            border-left: 4px solid var(--primary-gold, #C8A54B);
            padding: 15px 20px;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 280px;
            opacity: 0;
            transform: translateX(100%);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }

        .toast-content h4 {
            margin: 0 0 5px 0;
            color: var(--primary-gold, #C8A54B);
        }

        .toast-content p {
            margin: 0;
            font-size: 0.9rem;
        }

        .toast-close {
            cursor: pointer;
            color: #888;
            font-weight: bold;
            margin-left: 15px;
        }

        .toast-close:hover {
            color: #fff;
        }
    </style>
</head>

<body class="dark-mode">

    <div class="sidebar">
        <h2>Harajā Admin</h2>
        <a href="dashboard.php" class="nav-link active">Dashboard</a>
        <a href="manage.php" class="nav-link">Manage Bookings</a>
        <a href="managepasses.php" class="nav-link ">Manage Passes</a>
        <a href="edit.php" class="nav-link">Manage Rooms & Rates</a>
        <a href="logout.php" class="nav-link logout-btn">Logout</a>
    </div>

    <div class="main-content">
        <!-- ================= BOOKINGS SECTION ================= -->
        <div class="header-top">
            <h1>Overview</h1>
            <p>Welcome back, Admin</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h4>Monthly Revenue</h4>
                <h2>₱ <?php echo number_format($total, 2); ?></h2>
            </div>
            <div class="stat-card">
                <h4>Active Bookings</h4>
                <h2><?php echo $active_bookings; ?></h2>
            </div>
            <div class="stat-card">
                <h4>Available Rooms</h4>
                <h2><?php echo $total_rooms; ?></h2>
            </div>
            <div class="stat-card">
                <h4>Pending Requests</h4>
                <h2><?php echo $pending_requests; ?></h2>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Bookings Table with Filter and Pagination -->
            <div class="panel">
                <div class="panel-header">
                    <h3>Recent Bookings</h3>
                    <div class="header-actions">
                        <form method="GET" action="dashboard.php" id="filterForm">
                            <!-- Your existing inputs and select tag stay here -->
                            <input type="hidden" name="pass_filter" value="<?php echo htmlspecialchars($pass_status_filter); ?>">
                            <input type="hidden" name="pass_page" value="<?php echo htmlspecialchars($pass_page); ?>">
                            <select name="filter" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                                <option value="active" <?php if ($status_filter == 'active') echo 'selected'; ?>>Hide Done/Cancelled</option>
                                <option value="all" <?php if ($status_filter == 'all') echo 'selected'; ?>>Show All</option>
                                <option value="Confirmed" <?php if ($status_filter == 'Confirmed') echo 'selected'; ?>>Confirmed Only</option>
                                <option value="Pending" <?php if ($status_filter == 'Pending') echo 'selected'; ?>>Pending Only</option>
                                <option value="Done" <?php if ($status_filter == 'Done') echo 'selected'; ?>>Done Only</option>
                                <option value="Cancelled" <?php if ($status_filter == 'Cancelled') echo 'selected'; ?>>Cancelled Only</option>
                            </select>
                        </form>
                        <a href="export_bookings.php" class="export-btn">Export</a>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Check In/Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent_sql = "SELECT b.guest_name, r.name AS room_name, b.check_in_date, b.check_out_date, b.status 
                                       FROM bookings b 
                                       JOIN rooms r ON b.room_id = r.id 
                                       $where_clause
                                       ORDER BY b.created_at DESC 
                                       LIMIT $limit OFFSET $booking_offset";
                        $recent_bookings = $conn->query($recent_sql);

                        if ($recent_bookings && $recent_bookings->num_rows > 0) {
                            while ($row = $recent_bookings->fetch_assoc()) {
                                $date_range = date('M d', strtotime($row['check_in_date'])) . ' - ' . date('M d', strtotime($row['check_out_date']));

                                $badge_class = 'badge';
                                if ($row['status'] == 'Pending') $badge_class = 'badge pending';
                                if ($row['status'] == 'Cancelled') $badge_class = 'badge cancelled';
                                if ($row['status'] == 'Done') $badge_class = 'badge done';

                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['guest_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['room_name']) . "</td>";
                                echo "<td>" . $date_range . "</td>";
                                echo "<td><span class='$badge_class'>" . htmlspecialchars($row['status']) . "</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No bookings found for this filter.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Bookings Pagination Controls -->
                <?php if ($booking_total_pages > 1): ?>
                    <div class="pagination">
                        <?php
                        // Base URL carrying current filters and pass page state
                        $base_url = "?filter=$status_filter&pass_filter=$pass_status_filter&pass_page=$pass_page&booking_page=";

                        $prev_disabled = ($booking_page <= 1) ? 'disabled' : '';
                        $next_disabled = ($booking_page >= $booking_total_pages) ? 'disabled' : '';
                        ?>
                        <a href="<?php echo $base_url . ($booking_page - 1); ?>" class="page-link <?php echo $prev_disabled; ?>">« Prev</a>

                        <?php for ($i = 1; $i <= $booking_total_pages; $i++): ?>
                            <a href="<?php echo $base_url . $i; ?>" class="page-link <?php echo ($i == $booking_page) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <a href="<?php echo $base_url . ($booking_page + 1); ?>" class="page-link <?php echo $next_disabled; ?>">Next »</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Infographic Chart -->
            <div class="panel">
                <div class="panel-header">
                    <h3>Occupancy Rate (7 Days)</h3>
                </div>
                <canvas id="occupancyChart"></canvas>
            </div>
        </div>

        <!-- ================= PASSES SECTION ================= -->
        <div class="header-top" style="margin-top: 3rem;">
            <h1>Passes Overview</h1>
        </div>

        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
            <div class="stat-card">
                <h4>Day Passes (This Month)</h4>
                <h2><?php echo $day_pass_count; ?></h2>
            </div>
            <div class="stat-card">
                <h4>Night Passes (This Month)</h4>
                <h2><?php echo $night_pass_count; ?></h2>
            </div>
            <div class="stat-card">
                <h4>All Day Passes (This Month)</h4>
                <h2><?php echo $all_day_count; ?></h2>
            </div>
        </div>

        <div class="dashboard-grid" style="grid-template-columns: 1fr;">
            <!-- Passes Table with Filter and Pagination -->
            <div class="panel">
                <div class="panel-header">
                    <h3>Recent Passes</h3>
                    <div class="header-actions">
                        <form method="GET" action="dashboard.php" id="passFilterForm">
                            <!-- Your existing inputs and select tag stay here -->
                            <input type="hidden" name="filter" value="<?php echo htmlspecialchars($status_filter); ?>">
                            <input type="hidden" name="booking_page" value="<?php echo htmlspecialchars($booking_page); ?>">
                            <select name="pass_filter" class="filter-select" onchange="document.getElementById('passFilterForm').submit()">
                                <option value="active" <?php if ($pass_status_filter == 'active') echo 'selected'; ?>>Hide Done/Cancelled</option>
                                <option value="all" <?php if ($pass_status_filter == 'all') echo 'selected'; ?>>Show All</option>
                                <option value="Confirmed" <?php if ($pass_status_filter == 'Confirmed') echo 'selected'; ?>>Confirmed Only</option>
                                <option value="Pending" <?php if ($pass_status_filter == 'Pending') echo 'selected'; ?>>Pending Only</option>
                                <option value="Done" <?php if ($pass_status_filter == 'Done') echo 'selected'; ?>>Done Only</option>
                                <option value="Cancelled" <?php if ($pass_status_filter == 'Cancelled') echo 'selected'; ?>>Cancelled Only</option>
                            </select>
                        </form>
                        <a href="export_passes.php" class="export-btn">Export</a>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Guest</th>
                            <th>Pass Type</th>
                            <th>Visit Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent_passes_sql = "SELECT guest_name, pass_type, visit_date, status 
                                               FROM day_passes 
                                               $pass_where_clause
                                               ORDER BY created_at DESC 
                                               LIMIT $limit OFFSET $pass_offset";
                        $recent_passes = $conn->query($recent_passes_sql);

                        if ($recent_passes && $recent_passes->num_rows > 0) {
                            while ($row = $recent_passes->fetch_assoc()) {
                                $v_date = date('M d, Y', strtotime($row['visit_date']));

                                $badge_class = 'badge';
                                if ($row['status'] == 'Pending') $badge_class = 'badge pending';
                                if ($row['status'] == 'Cancelled') $badge_class = 'badge cancelled';
                                if ($row['status'] == 'Done') $badge_class = 'badge done';

                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['guest_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['pass_type']) . "</td>";
                                echo "<td>" . $v_date . "</td>";
                                echo "<td><span class='$badge_class'>" . htmlspecialchars($row['status']) . "</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No passes found for this filter.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Passes Pagination Controls -->
                <?php if ($pass_total_pages > 1): ?>
                    <div class="pagination">
                        <?php
                        // Base URL carrying current filters and booking page state
                        $base_pass_url = "?filter=$status_filter&pass_filter=$pass_status_filter&booking_page=$booking_page&pass_page=";

                        $prev_disabled = ($pass_page <= 1) ? 'disabled' : '';
                        $next_disabled = ($pass_page >= $pass_total_pages) ? 'disabled' : '';
                        ?>
                        <a href="<?php echo $base_pass_url . ($pass_page - 1); ?>" class="page-link <?php echo $prev_disabled; ?>">« Prev</a>

                        <?php for ($i = 1; $i <= $pass_total_pages; $i++): ?>
                            <a href="<?php echo $base_pass_url . $i; ?>" class="page-link <?php echo ($i == $pass_page) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <a href="<?php echo $base_pass_url . ($pass_page + 1); ?>" class="page-link <?php echo $next_disabled; ?>">Next »</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <script>
        const chartLabels = <?php echo $chart_labels_json; ?>;
        const chartData = <?php echo $chart_data_json; ?>;

        const ctx = document.getElementById('occupancyChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Occupancy %',
                    data: chartData,
                    borderColor: '#C8A54B',
                    backgroundColor: 'rgba(200, 165, 75, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        let lastBookingId = null;
        let lastPassId = null;

        function checkNewData() {
            fetch('check_latest.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) return; // Stop if not logged in

                    // On first load, just record the highest IDs without showing notifications
                    if (lastBookingId === null) {
                        lastBookingId = parseInt(data.latest_booking_id);
                        lastPassId = parseInt(data.latest_pass_id);
                        return;
                    }

                    // If the new ID is higher than our recorded ID, we have a new booking!
                    if (parseInt(data.latest_booking_id) > lastBookingId) {
                        showToast('New Room Booking!', `Guest: ${data.latest_booking_guest}`);
                        lastBookingId = parseInt(data.latest_booking_id);

                        // Optional: You can force the page to reload to update the tables automatically
                        // setTimeout(() => location.reload(), 3000); 
                    }

                    if (parseInt(data.latest_pass_id) > lastPassId) {
                        showToast('New Pass Purchased!', `Guest: ${data.latest_pass_guest}`);
                        lastPassId = parseInt(data.latest_pass_id);
                    }
                })
                .catch(error => console.error('Error fetching latest data:', error));
        }

        function showToast(title, message) {
            // Create the container if it doesn't exist
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                document.body.appendChild(container);
            }

            // Build the pop-up notification
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `
            <div class="toast-content">
                <h4>${title}</h4>
                <p>${message}</p>
            </div>
            <div class="toast-close" onclick="this.parentElement.remove()">X</div>
        `;

            container.appendChild(toast);

            // Trigger the slide-in animation
            setTimeout(() => toast.classList.add('show'), 100);

            // Play an optional notification sound (uncomment below if you have a sound file)
            // new Audio('notification.mp3').play();

            // Automatically hide and remove the pop-up after 6 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 6000);
        }

        // Poll the server every 10 seconds (10,000 milliseconds)
        setInterval(checkNewData, 10000);

        // Run an initial check immediately when the page loads
        checkNewData();
    </script>
</body>

</html>