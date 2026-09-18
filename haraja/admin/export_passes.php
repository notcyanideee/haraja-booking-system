<?php
require_once __DIR__ . "/../db.php";

// Protect the page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 1. Tell browser to download as CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="haraja_passes_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// 2. Write Column Headers
fputcsv($output, ['Guest Name', 'Phone Number', 'Visit Date', 'Pass Type', 'Adults', 'Children', 'Amenity', 'Total Amount', 'Payment Method', 'Date Booked']);

// 3. Fetch specific columns from the daypasses table
$query = "SELECT guest_name, guest_phone, visit_date, pass_type, number_of_adults, number_of_children, amenity, total_amount, payment_method, created_at 
          FROM daypasses 
          ORDER BY created_at DESC";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    // 4. Write Data Rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['guest_name'],
            $row['guest_phone'],
            $row['visit_date'],
            $row['pass_type'],
            $row['number_of_adults'],
            $row['number_of_children'],
            $row['amenity'],
            $row['total_amount'],
            $row['payment_method'],
            $row['created_at']
        ]);
    }
}

fclose($output);
exit();
