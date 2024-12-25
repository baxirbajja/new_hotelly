<?php
require_once 'includes/functions.php';
session_start();

// Get filter parameters
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';
$guests = $_GET['guests'] ?? '';
$room_type = $_GET['room_type'] ?? '';

// Get all available rooms with filters
$sql = "SELECT * FROM rooms WHERE is_available = 1";
if ($room_type) {
    $sql .= " AND type = '" . $conn->real_escape_string($room_type) . "'";
}
if ($guests) {
    $sql .= " AND capacity >= " . (int)$guests;
}
$result = $conn->query($sql);
$rooms = $result->fetch_all(MYSQLI_ASSOC);

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms - Hotelly</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/rooms.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="nav-container">
        <a href="index.php" class="logo">HOTELLY</a>
        <div class="nav-right">
            <div class="nav-links">
                <a href="index.php" class="nav-link">Home</a>
                <a href="rooms.php" class="nav-link active">Rooms</a>
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

    <!-- Page Header -->
    <header class="page-header">
        <div class="header-content">
            <h1 class="serif" data-aos="fade-up">Our Rooms</h1>
            <p data-aos="fade-up" data-aos-delay="100">Experience luxury and comfort in our carefully designed rooms</p>
        </div>
    </header>

    <!-- Room Filters -->
    <section class="filters-section">
        <div class="container">
            <form class="filters-form" data-aos="fade-up" method="GET">
                <div class="filter-group">
                    <label>Check In</label>
                    <input type="date" name="check_in" class="filter-input" value="<?php echo htmlspecialchars($check_in); ?>">
                </div>
                <div class="filter-group">
                    <label>Check Out</label>
                    <input type="date" name="check_out" class="filter-input" value="<?php echo htmlspecialchars($check_out); ?>">
                </div>
                <div class="filter-group">
                    <label>Guests</label>
                    <select name="guests" class="filter-input">
                        <option value="">All</option>
                        <option value="1" <?php echo $guests == '1' ? 'selected' : ''; ?>>1 Guest</option>
                        <option value="2" <?php echo $guests == '2' ? 'selected' : ''; ?>>2 Guests</option>
                        <option value="3" <?php echo $guests == '3' ? 'selected' : ''; ?>>3 Guests</option>
                        <option value="4" <?php echo $guests == '4' ? 'selected' : ''; ?>>4+ Guests</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Room Type</label>
                    <select name="room_type" class="filter-input">
                        <option value="">All Types</option>
                        <option value="Standard" <?php echo $room_type == 'Standard' ? 'selected' : ''; ?>>Standard Room</option>
                        <option value="Deluxe" <?php echo $room_type == 'Deluxe' ? 'selected' : ''; ?>>Deluxe Room</option>
                        <option value="Suite" <?php echo $room_type == 'Suite' ? 'selected' : ''; ?>>Suite</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-solid">Search Rooms</button>
            </form>
        </div>
    </section>

    <!-- Rooms Grid -->
    <section class="rooms-section">
        <div class="container">
            <div class="rooms-grid" data-aos="fade-up">
                <?php foreach ($rooms as $room): ?>
                    <div class="room-card">
                        <div class="room-image">
                            <img src="<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>">
                            <div class="room-overlay">
                                <a href="book.php?room_id=<?php echo $room['id']; ?>" class="btn btn-outline">Book Now</a>
                            </div>
                        </div>
                        <div class="room-info">
                            <div class="room-type"><?php echo htmlspecialchars($room['type']); ?></div>
                            <h3 class="serif"><?php echo htmlspecialchars($room['name']); ?></h3>
                            <div class="room-features">
                                <span><?php echo $room['capacity']; ?> Persons</span>
                                <span>•</span>
                                <span><?php echo $room['size']; ?>m²</span>
                                <span>•</span>
                                <span><?php echo htmlspecialchars($room['view_type']); ?></span>
                            </div>
                            <div class="room-bottom">
                                <p class="room-price">From $<?php echo number_format($room['price'], 0); ?><span>/night</span></p>
                                <a href="room.php?id=<?php echo $room['id']; ?>" class="btn-text">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
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
    </script>
</body>
</html>
