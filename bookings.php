<?php
require_once 'includes/functions.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user name
$sql = "SELECT name FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$user_name = $user['name'];

// Get user's bookings
$sql = "SELECT b.*, r.name as room_name, r.image as room_image, r.type as room_type, h.name as hotel_name, h.city as hotel_city 
        FROM bookings b 
        LEFT JOIN rooms r ON b.room_id = r.id 
        LEFT JOIN hotels h ON r.hotel_id = h.id 
        WHERE b.user_id = ? 
        ORDER BY b.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Hotelly</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="nav-container">
        <a href="index.php" class="logo">HOTELLY</a>
        <div class="nav-right">
            <div class="nav-links">
                <a href="index.php" class="nav-link">Home</a>
                <a href="hotels.php" class="nav-link">Hotels</a>
                <a href="rooms.php" class="nav-link">Rooms</a>
                <a href="bookings.php" class="nav-link active">My Bookings</a>
                <a href="profile.php" class="nav-link"><?php echo htmlspecialchars($user_name); ?></a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="page-header">
        <div class="header-content">
            <h1 class="serif" data-aos="fade-up">My Bookings</h1>
            <p data-aos="fade-up" data-aos-delay="100">Manage your hotel reservations</p>
        </div>
    </header>

    <!-- Bookings Section -->
    <section class="bookings-section">
        <div class="container">
            <?php if (!empty($bookings)): ?>
                <div class="bookings-grid" data-aos="fade-up">
                    <?php foreach ($bookings as $booking): ?>
                        <div class="booking-card">
                            <div class="booking-image">
                                <img src="<?php echo htmlspecialchars($booking['room_image']); ?>" alt="<?php echo htmlspecialchars($booking['room_name']); ?>">
                            </div>
                            <div class="booking-info">
                                <div class="booking-header">
                                    <?php if (!empty($booking['hotel_name'])): ?>
                                        <div class="booking-hotel"><?php echo htmlspecialchars($booking['hotel_name']); ?> - <?php echo htmlspecialchars($booking['hotel_city']); ?></div>
                                    <?php endif; ?>
                                    <h3 class="serif"><?php echo htmlspecialchars($booking['room_name']); ?></h3>
                                    <div class="room-type"><?php echo htmlspecialchars($booking['room_type']); ?></div>
                                </div>
                                <div class="booking-dates">
                                    <div class="date-group">
                                        <span class="date-label">Check-in</span>
                                        <span class="date-value"><?php echo date('M d, Y', strtotime($booking['check_in'])); ?></span>
                                    </div>
                                    <div class="date-group">
                                        <span class="date-label">Check-out</span>
                                        <span class="date-value"><?php echo date('M d, Y', strtotime($booking['check_out'])); ?></span>
                                    </div>
                                </div>
                                <div class="booking-price">
                                    <span class="price-label">Total Price</span>
                                    <span class="price-value">$<?php echo number_format($booking['total_price'], 2); ?></span>
                                </div>
                                <?php if (strtotime($booking['check_in']) > time()): ?>
                                    <div class="booking-actions">
                                        <a href="cancel_booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-outline" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel Booking</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-bookings" data-aos="fade-up">
                    <h2>No Bookings Found</h2>
                    <p>You haven't made any bookings yet. Start exploring our hotels and rooms!</p>
                    <a href="hotels.php" class="btn btn-solid">Browse Hotels</a>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>
