<?php
require_once 'includes/functions.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user details
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get user's bookings
$sql = "SELECT b.*, r.name as room_name, r.image as room_image 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        WHERE b.user_id = ? 
        ORDER BY b.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    
    if ($name && $email) {
        // Update basic info
        $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $email, $_SESSION['user_id']);
        $stmt->execute();
        
        // Update password if provided
        if ($current_password && $new_password) {
            // Verify current password
            if (password_verify($current_password, $user['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $hashed_password, $_SESSION['user_id']);
                $stmt->execute();
            }
        }
        
        // Refresh page to show updated info
        header('Location: profile.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Hotelly</title>
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
                <a href="rooms.php" class="nav-link">Rooms</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="bookings.php" class="nav-link">My Bookings</a>
                    <a href="profile.php" class="nav-link active"><?php echo htmlspecialchars($user['name']); ?></a>
                    <a href="logout.php" class="nav-link">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Login</a>
                    <a href="register.php" class="nav-link">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="profile-section">
            <h1>My Profile</h1>
            
            <div class="profile-content">
                <div class="profile-info">
                    <h2>Profile Information</h2>
                    <form method="POST" class="profile-form">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <h3>Change Password</h3>
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password">
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password">
                        </div>
                        
                        <button type="submit" class="btn">Update Profile</button>
                    </form>
                </div>
                
                <div class="bookings-section">
                    <h2>Recent Bookings</h2>
                    <div class="recent-bookings">
                        <?php if (!empty($bookings)): ?>
                            <?php foreach (array_slice($bookings, 0, 3) as $booking): ?>
                                <div class="booking-card">
                                    <div class="booking-image">
                                        <?php if (!empty($booking['room_image'])): ?>
                                            <img src="<?php echo htmlspecialchars($booking['room_image']); ?>" alt="<?php echo htmlspecialchars($booking['room_name']); ?>">
                                        <?php else: ?>
                                            <div class="no-image">No Image Available</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="booking-info">
                                        <h3><?php echo htmlspecialchars($booking['room_name']); ?></h3>
                                        <p>Check-in: <?php echo date('M d, Y', strtotime($booking['check_in'])); ?></p>
                                        <p>Check-out: <?php echo date('M d, Y', strtotime($booking['check_out'])); ?></p>
                                        <p class="status status-<?php echo $booking['status']; ?>"><?php echo ucfirst($booking['status']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <a href="bookings.php" class="btn btn-outline">View All Bookings</a>
                        <?php else: ?>
                            <p class="no-bookings">You haven't made any bookings yet. <a href="rooms.php">Browse our rooms</a></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>
