<?php
require_once 'includes/functions.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id'] ?? null;
    
    if ($booking_id) {
        // Update booking status to cancelled
        $sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Booking cancelled successfully';
        } else {
            $_SESSION['error'] = 'Failed to cancel booking';
        }
    }
}

header('Location: bookings.php');
exit;
?>
