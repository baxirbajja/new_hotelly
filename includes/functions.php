<?php
require_once 'config.php';

// Database initialization
function initializeDatabase() {
    global $conn;
    
    // Create reviews table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS reviews (
        id INT PRIMARY KEY AUTO_INCREMENT,
        booking_id INT,
        user_id INT,
        room_id INT,
        rating INT NOT NULL,
        comment TEXT,
        status ENUM('pending', 'approved', 'hidden') NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
    )";
    $conn->query($sql);

    // Check if status column exists, if not add it
    $result = $conn->query("SHOW COLUMNS FROM reviews LIKE 'status'");
    if ($result->num_rows === 0) {
        $sql = "ALTER TABLE reviews ADD COLUMN status ENUM('pending', 'approved', 'hidden') NOT NULL DEFAULT 'pending'";
        $conn->query($sql);
    }
}

// Call initialization
initializeDatabase();

// Room functions
function getAllRooms() {
    global $conn;
    $sql = "SELECT * FROM rooms WHERE is_available = TRUE";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getRoomById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Booking functions
function createBooking($roomId, $userId, $checkIn, $checkOut, $totalPrice) {
    global $conn;
    $status = 'pending'; // Default status
    $sql = "INSERT INTO bookings (room_id, user_id, check_in, check_out, total_price, status) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissds", $roomId, $userId, $checkIn, $checkOut, $totalPrice, $status);
    return $stmt->execute();
}

function getBookingsByUser($userId) {
    global $conn;
    $sql = "SELECT b.*, r.name as room_name, r.image as room_image 
            FROM bookings b 
            JOIN rooms r ON b.room_id = r.id 
            WHERE b.user_id = ? 
            ORDER BY b.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Review functions
function createReview($bookingId, $userId, $roomId, $rating, $comment) {
    global $conn;
    $status = 'pending'; // Default status for new reviews
    
    // First check if status column exists
    $result = $conn->query("SHOW COLUMNS FROM reviews LIKE 'status'");
    if ($result->num_rows === 0) {
        // Add status column if it doesn't exist
        $conn->query("ALTER TABLE reviews ADD COLUMN status ENUM('pending', 'approved', 'hidden') NOT NULL DEFAULT 'pending'");
    }
    
    $sql = "INSERT INTO reviews (booking_id, user_id, room_id, rating, comment, status) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiss", $bookingId, $userId, $roomId, $rating, $comment, $status);
    return $stmt->execute();
}

function getReviewsByRoom($roomId) {
    global $conn;
    $sql = "SELECT r.*, u.name as user_name 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.room_id = ? 
            ORDER BY r.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// User functions
function createUser($name, $email, $password) {
    global $conn;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $hashedPassword);
    return $stmt->execute();
}

function getUserByEmail($email) {
    global $conn;
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function validateUser($email, $password) {
    $user = getUserByEmail($email);
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

// Admin Dashboard functions
function getAdminDashboardStats() {
    global $conn;
    $stats = [];

    // Get total bookings
    $result = $conn->query("SELECT COUNT(*) as count FROM bookings");
    $stats['total_bookings'] = $result->fetch_assoc()['count'];

    // Get active bookings
    $sql = "SELECT COUNT(*) as count FROM bookings 
            WHERE status = 'confirmed' 
            AND check_out >= CURDATE()";
    $result = $conn->query($sql);
    $stats['active_bookings'] = $result->fetch_assoc()['count'];

    // Get total revenue
    $result = $conn->query("SELECT SUM(total_price) as total FROM bookings WHERE status != 'cancelled'");
    $stats['total_revenue'] = $result->fetch_assoc()['total'] ?? 0;

    // Get total users
    $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
    $stats['total_users'] = $result->fetch_assoc()['count'];

    // Get monthly bookings for the last 6 months
    $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
            COUNT(*) as count 
            FROM bookings 
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) 
            GROUP BY month 
            ORDER BY month";
    $result = $conn->query($sql);
    $monthly_bookings = [];
    $monthly_labels = [];
    while ($row = $result->fetch_assoc()) {
        $monthly_labels[] = date('M Y', strtotime($row['month']));
        $monthly_bookings[] = (int)$row['count'];
    }
    $stats['monthly_labels'] = $monthly_labels;
    $stats['monthly_bookings'] = $monthly_bookings;

    // Get monthly revenue
    $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
            SUM(total_price) as total 
            FROM bookings 
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) 
            AND status != 'cancelled'
            GROUP BY month 
            ORDER BY month";
    $result = $conn->query($sql);
    $monthly_revenue = [];
    while ($row = $result->fetch_assoc()) {
        $monthly_revenue[] = (float)$row['total'];
    }
    $stats['monthly_revenue'] = $monthly_revenue;

    // Get recent activity
    $sql = "SELECT 'booking' as type, 
                   CONCAT(u.name, ' made a booking for ', r.name) as description,
                   b.created_at as time
            FROM bookings b 
            JOIN users u ON b.user_id = u.id 
            JOIN rooms r ON b.room_id = r.id 
            UNION ALL 
            SELECT 'review' as type,
                   CONCAT(u.name, ' reviewed ', r.name) as description,
                   rv.created_at as time
            FROM reviews rv 
            JOIN users u ON rv.user_id = u.id 
            JOIN rooms r ON rv.room_id = r.id 
            ORDER BY time DESC 
            LIMIT 10";
    $result = $conn->query($sql);
    $stats['recent_activity'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['recent_activity'][] = [
            'type' => $row['type'],
            'description' => $row['description'],
            'time' => date('M j, Y g:i A', strtotime($row['time']))
        ];
    }

    return $stats;
}

// Room Management functions
function addRoom($data) {
    global $conn;
    $sql = "INSERT INTO rooms (name, type, description, price, capacity, size, view_type, amenities, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdiisss", 
        $data['name'],
        $data['type'],
        $data['description'],
        $data['price'],
        $data['capacity'],
        $data['size'],
        $data['view_type'],
        $data['amenities'],
        $data['image']
    );
    return $stmt->execute();
}

function updateRoom($id, $data) {
    global $conn;
    $sql = "UPDATE rooms 
            SET name = ?, type = ?, description = ?, price = ?, 
                capacity = ?, size = ?, view_type = ?, amenities = ?, image = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdiisssi", 
        $data['name'],
        $data['type'],
        $data['description'],
        $data['price'],
        $data['capacity'],
        $data['size'],
        $data['view_type'],
        $data['amenities'],
        $data['image'],
        $id
    );
    return $stmt->execute();
}

function deleteRoom($id) {
    global $conn;
    // First check if room has any bookings
    $sql = "SELECT COUNT(*) as count FROM bookings WHERE room_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] > 0) {
        return false; // Can't delete room with bookings
    }
    
    // Delete room if no bookings
    $sql = "DELETE FROM rooms WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Booking Management functions
function updateBookingStatus($id, $status) {
    global $conn;
    $sql = "UPDATE bookings SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id);
    return $stmt->execute();
}

function getAllBookings($limit = null) {
    global $conn;
    $sql = "SELECT b.*, r.name as room_name, u.name as user_name 
            FROM bookings b 
            JOIN rooms r ON b.room_id = r.id 
            JOIN users u ON b.user_id = u.id 
            ORDER BY b.created_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// User Management functions
function getAllUsers($limit = null) {
    global $conn;
    $sql = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function updateUserStatus($id, $status) {
    global $conn;
    $sql = "UPDATE users SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id);
    return $stmt->execute();
}

function updateUserRole($id, $role) {
    global $conn;
    $sql = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $role, $id);
    return $stmt->execute();
}

// Delete user function
function deleteUser($userId) {
    global $conn;
    $userId = mysqli_real_escape_string($conn, $userId);
    
    // First delete all bookings associated with this user
    $bookingQuery = "DELETE FROM bookings WHERE user_id = '$userId'";
    mysqli_query($conn, $bookingQuery);
    
    // Then delete all reviews associated with this user
    $reviewQuery = "DELETE FROM reviews WHERE user_id = '$userId'";
    mysqli_query($conn, $reviewQuery);
    
    // Finally delete the user
    $userQuery = "DELETE FROM users WHERE id = '$userId'";
    return mysqli_query($conn, $userQuery);
}

// Delete booking function
function deleteBooking($bookingId) {
    global $conn;
    $bookingId = mysqli_real_escape_string($conn, $bookingId);
    
    // Delete the booking
    $query = "DELETE FROM bookings WHERE id = '$bookingId'";
    return mysqli_query($conn, $query);
}

// Review Management functions
function getAllReviews($limit = null) {
    global $conn;
    $sql = "SELECT r.*, u.name as user_name, rm.name as room_name 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            JOIN rooms rm ON r.room_id = rm.id 
            ORDER BY r.created_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function updateReviewStatus($id, $status) {
    global $conn;
    if (!in_array($status, ['pending', 'approved', 'hidden'])) {
        return false;
    }
    
    // First check if status column exists
    $result = $conn->query("SHOW COLUMNS FROM reviews LIKE 'status'");
    if ($result->num_rows === 0) {
        // Add status column if it doesn't exist
        $conn->query("ALTER TABLE reviews ADD COLUMN status ENUM('pending', 'approved', 'hidden') NOT NULL DEFAULT 'pending'");
    }
    
    $sql = "UPDATE reviews SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id);
    return $stmt->execute();
}

?>
