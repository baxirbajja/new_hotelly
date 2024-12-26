<?php
require_once 'includes/functions.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get booking ID from GET parameter
$booking_id = $_GET['id'] ?? null;

if ($booking_id) {
    // Verify that the booking belongs to the user
    $sql = "SELECT * FROM bookings WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();

    if ($booking) {
        // Update booking status to cancelled
        $sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Booking cancelled successfully, the booking is not gonna be deleted untill the admin confirm your cancelation';
        } else {
            $_SESSION['error'] = 'Failed to cancel booking';
        }
    } else {
        $_SESSION['error'] = 'Invalid booking';
    }
} else {
    $_SESSION['error'] = 'Invalid booking ID';
}

header('Location: bookings.php');
exit;
?>
