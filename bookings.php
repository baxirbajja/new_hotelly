<?php
require_once 'includes/functions.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user's bookings
$bookings = getBookingsByUser($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Hotelly</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1>Hotelly</h1>
            </div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="rooms.php">Rooms</a></li>
                <li><a href="bookings.php" class="active">My Bookings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="bookings-list">
            <h2>My Bookings</h2>
            <?php if ($bookings): ?>
                <div class="bookings-grid">
                    <?php foreach ($bookings as $booking): ?>
                        <div class="booking-card">
                            <h3><?php echo htmlspecialchars($booking['room_name']); ?></h3>
                            <div class="booking-details">
                                <p>
                                    <strong>Check-in:</strong> 
                                    <?php echo date('M d, Y', strtotime($booking['check_in'])); ?>
                                </p>
                                <p>
                                    <strong>Check-out:</strong> 
                                    <?php echo date('M d, Y', strtotime($booking['check_out'])); ?>
                                </p>
                                <p>
                                    <strong>Total Price:</strong> 
                                    $<?php echo number_format($booking['total_price'], 2); ?>
                                </p>
                                <p>
                                    <strong>Status:</strong> 
                                    <span class="status-<?php echo $booking['status']; ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </p>
                            </div>
                            <?php if ($booking['status'] === 'pending'): ?>
                                <form method="POST" action="cancel_booking.php">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" class="btn btn-cancel">Cancel Booking</button>
                                </form>
                            <?php endif; ?>
                            <?php if ($booking['status'] === 'confirmed'): ?>
                                <a href="add_review.php?booking_id=<?php echo $booking['id']; ?>" class="btn">Add Review</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-bookings">You don't have any bookings yet. <a href="rooms.php">Browse our rooms</a></p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Hotelly. All rights reserved.</p>
    </footer>
</body>
</html>
