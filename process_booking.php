<?php
require_once 'includes/functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$room_id = $_POST['room_id'] ?? null;
$dates = $_POST['dates'] ?? '';
$price_per_night = $_POST['price_per_night'] ?? 0;

if (!$room_id || !$dates || !$price_per_night) {
    $_SESSION['error'] = "Missing required booking information. Please select your dates and try again.";
    header('Location: room.php?id=' . $room_id);
    exit;
}

// Split the date range into check-in and check-out dates
$date_parts = explode(' - ', $dates);
if (count($date_parts) !== 2) {
    $_SESSION['error'] = "Please select both check-in and check-out dates.";
    header('Location: room.php?id=' . $room_id);
    exit;
}

$check_in = date('Y-m-d', strtotime($date_parts[0]));
$check_out = date('Y-m-d', strtotime($date_parts[1]));

// Validate dates
if (!$check_in || !$check_out || $check_in === '1970-01-01' || $check_out === '1970-01-01') {
    $_SESSION['error'] = "Invalid date format. Please select your dates again.";
    header('Location: room.php?id=' . $room_id);
    exit;
}

// Check if dates are in the past
$today = new DateTime();
$today->setTime(0, 0);
$check_in_obj = new DateTime($check_in);
$check_out_obj = new DateTime($check_out);

if ($check_in_obj < $today) {
    $_SESSION['error'] = "Check-in date cannot be in the past.";
    header('Location: room.php?id=' . $room_id);
    exit;
}

if ($check_in_obj >= $check_out_obj) {
    $_SESSION['error'] = "Check-out date must be after check-in date.";
    header('Location: room.php?id=' . $room_id);
    exit;
}

// Calculate total nights and price
$nights = $check_out_obj->diff($check_in_obj)->days;
if ($nights < 1) {
    $_SESSION['error'] = "Minimum stay is 1 night.";
    header('Location: room.php?id=' . $room_id);
    exit;
}
$total_price = $nights * $price_per_night;

// Verify room exists and is available
$sql = "SELECT id FROM rooms WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $room_id);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
    $_SESSION['error'] = "Invalid room selection.";
    header('Location: rooms.php');
    exit;
}

// Verify dates are available
$booked_dates = getBookedDates($room_id);
$booking_dates = [];
$current_date = clone $check_in_obj;

while ($current_date < $check_out_obj) {
    $date_str = $current_date->format('Y-m-d');
    if (in_array($date_str, $booked_dates)) {
        $_SESSION['error'] = "Some of your selected dates are no longer available. Please choose different dates.";
        header('Location: room.php?id=' . $room_id);
        exit;
    }
    $booking_dates[] = $date_str;
    $current_date->modify('+1 day');
}

// Create the booking with pending payment status
$booking_id = createBooking($room_id, $_SESSION['user_id'], $check_in, $check_out, $total_price);

if (!$booking_id) {
    $_SESSION['error'] = "Failed to create booking";
    header('Location: room.php?id=' . $room_id);
    exit;
}

// Redirect to payment page
header('Location: payment.php?booking_id=' . $booking_id);
exit;
?>
