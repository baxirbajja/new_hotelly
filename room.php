<?php
require_once 'includes/functions.php';
session_start();

$room_id = $_GET['id'] ?? null;
if (!$room_id) {
    header('Location: rooms.php');
    exit;
}

// Get room details
$sql = "SELECT * FROM rooms WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();

if (!$room) {
    header('Location: rooms.php');
    exit;
}

// Get room reviews
$sql = "SELECT r.*, u.name as user_name 
        FROM reviews r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.room_id = ? 
        ORDER BY r.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate average rating
$average_rating = 0;
if (count($reviews) > 0) {
    $total_rating = array_sum(array_column($reviews, 'rating'));
    $average_rating = round($total_rating / count($reviews), 1);
}

// Get user name if logged in
$user_name = '';
if (isset($_SESSION['user_id'])) {
    $sql = "SELECT name FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $user_name = $user['name'];
}

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $guests = $_POST['guests'] ?? '';

    if ($check_in && $check_out && $guests) {
        $booking_result = createBooking($_SESSION['user_id'], $room_id, $check_in, $check_out, $guests);
        if ($booking_result) {
            header('Location: bookings.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($room['name']); ?> - Hotelly</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/room.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="nav-container">
        <a href="index.php" class="logo">HOTELLY</a>
        <div class="nav-right">
            <div class="nav-links">
                <a href="index.php" class="nav-link">Home</a>
                <a href="rooms.php" class="nav-link">Rooms</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="bookings.php" class="nav-link">My Bookings</a>
                    <a href="profile.php" class="nav-link"><?php echo htmlspecialchars($user_name); ?></a>
                    <a href="logout.php" class="nav-link">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Login</a>
                    <a href="register.php" class="nav-link">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Room Details -->
    <section class="room-details">
        <div class="container">
            <div class="room-header" data-aos="fade-up">
                <div class="room-title">
                    <div class="room-type"><?php echo htmlspecialchars($room['type']); ?></div>
                    <h1 class="serif"><?php echo htmlspecialchars($room['name']); ?></h1>
                    <?php if ($average_rating > 0): ?>
                        <div class="room-rating">
                            <span class="stars"><?php echo str_repeat('★', round($average_rating)); ?></span>
                            <span class="rating-text"><?php echo $average_rating; ?> (<?php echo count($reviews); ?> reviews)</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="room-price">
                    <span class="price">$<?php echo number_format($room['price'], 0); ?></span>
                    <span class="per-night">/night</span>
                </div>
            </div>

            <div class="room-gallery" data-aos="fade-up">
                <img src="<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" class="main-image">
            </div>

            <div class="room-content" data-aos="fade-up">
                <div class="room-info">
                    <h2 class="serif">Room Details</h2>
                    <div class="room-features">
                        <div class="feature">
                            <span class="feature-label">Capacity</span>
                            <span class="feature-value"><?php echo $room['capacity']; ?> Persons</span>
                        </div>
                        <div class="feature">
                            <span class="feature-label">Size</span>
                            <span class="feature-value"><?php echo $room['size']; ?>m²</span>
                        </div>
                        <div class="feature">
                            <span class="feature-label">View</span>
                            <span class="feature-value"><?php echo htmlspecialchars($room['view_type']); ?></span>
                        </div>
                    </div>
                    <div class="room-description">
                        <p><?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
                    </div>
                    <?php if ($room['amenities']): ?>
                        <div class="room-amenities">
                            <h3>Amenities</h3>
                            <ul>
                                <?php foreach (json_decode($room['amenities'], true) as $amenity): ?>
                                    <li><?php echo htmlspecialchars($amenity); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="booking-form">
                    <h2 class="serif">Book This Room</h2>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST" class="book-form">
                            <div class="form-group">
                                <label>Check In</label>
                                <input type="date" name="check_in" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="form-group">
                                <label>Check Out</label>
                                <input type="date" name="check_out" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                            </div>
                            <div class="form-group">
                                <label>Guests</label>
                                <select name="guests" required>
                                    <?php for ($i = 1; $i <= $room['capacity']; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?> Guest<?php echo $i > 1 ? 's' : ''; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-solid">Book Now</button>
                        </form>
                    <?php else: ?>
                        <p>Please <a href="login.php">login</a> to book this room.</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($reviews): ?>
                <div class="room-reviews" data-aos="fade-up">
                    <h2 class="serif">Guest Reviews</h2>
                    <div class="reviews-grid">
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-card">
                                <div class="review-header">
                                    <div class="reviewer-name"><?php echo htmlspecialchars($review['user_name']); ?></div>
                                    <div class="review-rating">
                                        <span class="stars"><?php echo str_repeat('★', $review['rating']); ?></span>
                                    </div>
                                </div>
                                <div class="review-date">
                                    <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                                </div>
                                <div class="review-comment">
                                    <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="serif">HOTELLY</h3>
                    <p>Experience luxury and comfort in the heart of Copenhagen.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="rooms.php">Rooms</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="bookings.php">My Bookings</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Login</a></li>
                            <li><a href="register.php">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p>123 Hotel Street<br>Copenhagen, Denmark<br>Phone: +45 12 34 56 78<br>Email: info@hotelly.com</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Hotelly. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
        
        // Validate check-in and check-out dates
        document.querySelector('.book-form').addEventListener('submit', function(e) {
            const checkIn = new Date(this.check_in.value);
            const checkOut = new Date(this.check_out.value);
            
            if (checkIn >= checkOut) {
                e.preventDefault();
                alert('Check-out date must be after check-in date');
            }
        });
    </script>
</body>
</html>
