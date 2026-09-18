<?php
require_once __DIR__ . "/../db.php";

// Make sure only logged-in admins can access this script
// Protect the page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 1. Get the very latest Room Booking ID and Guest Name
$b_sql = "SELECT id, guest_name FROM bookings ORDER BY id DESC LIMIT 1";
$b_res = $conn->query($b_sql);
$latest_booking = $b_res ? $b_res->fetch_assoc() : null;

// 2. Get the very latest Pass ID and Guest Name
$p_sql = "SELECT id, guest_name FROM day_passes ORDER BY id DESC LIMIT 1";
$p_res = $conn->query($p_sql);
$latest_pass = $p_res ? $p_res->fetch_assoc() : null;

// 3. Send the data back as JSON
echo json_encode([
    'latest_booking_id' => $latest_booking['id'] ?? 0,
    'latest_booking_guest' => $latest_booking['guest_name'] ?? 'Unknown',
    'latest_pass_id' => $latest_pass['id'] ?? 0,
    'latest_pass_guest' => $latest_pass['guest_name'] ?? 'Unknown'
]);
