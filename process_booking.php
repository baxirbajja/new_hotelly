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
    $_SESSION['error'] = "Missing required booking information";
    header('Location: room.php?id=' . $room_id);
    exit;
}

// Split the date range into check-in and check-out dates
$date_parts = explode(' - ', $dates);
if (count($date_parts) !== 2) {
    $_SESSION['error'] = "Invalid date range";
    header('Location: room.php?id=' . $room_id);
    exit;
}

$check_in = date('Y-m-d', strtotime($date_parts[0]));
$check_out = date('Y-m-d', strtotime($date_parts[1]));

// Calculate total nights and price
$check_in_obj = new DateTime($check_in);
$check_out_obj = new DateTime($check_out);
$nights = $check_out_obj->diff($check_in_obj)->days;
$total_price = $nights * $price_per_night;

// Verify dates are available
$booked_dates = getBookedDates($room_id);
$booking_dates = [];
$current_date = clone $check_in_obj;

while ($current_date < $check_out_obj) {
    $date_str = $current_date->format('Y-m-d');
    if (in_array($date_str, $booked_dates)) {
        $_SESSION['error'] = "Selected dates are no longer available";
        header('Location: room.php?id=' . $room_id);
        exit;
    }
    $booking_dates[] = $date_str;
    $current_date->modify('+1 day');
}

// Create the booking
$booking_id = createBooking($room_id, $_SESSION['user_id'], $check_in, $check_out, $total_price);

if (!$booking_id) {
    $_SESSION['error'] = "Failed to create booking";
    header('Location: room.php?id=' . $room_id);
    exit;
}

// Get user and room details for the email
$user = getUserById($_SESSION['user_id']);
$room = getRoomById($room_id);

// Send booking confirmation email
$to = $user['email'];
$subject = "Booking Confirmation - " . $room['name'];
$message = "
Dear " . $user['name'] . ",

Thank you for booking with Hotelly! Here are your booking details:

Room: " . $room['name'] . " at " . $room['hotel_name'] . "
Check-in: " . date('F j, Y', strtotime($check_in)) . "
Check-out: " . date('F j, Y', strtotime($check_out)) . "
Total Nights: " . $nights . "
Total Price: $" . number_format($total_price, 2) . "

Your booking is confirmed. You can view your booking details in your account dashboard.

Best regards,
The Hotelly Team
";

$headers = "From: bookings@hotelly.com";

mail($to, $subject, $message, $headers);

// Redirect to success page
$_SESSION['success'] = "Booking confirmed! Check your email for details.";
header('Location: bookings.php');
exit;
?>
