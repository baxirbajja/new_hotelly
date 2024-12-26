<?php
require_once 'config.php';

// Database initialization
function initializeDatabase() {
    global $conn;
    
    // Create hotels table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS hotels (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        city VARCHAR(100) NOT NULL,
        address TEXT NOT NULL,
        description TEXT,
        image VARCHAR(255),
        rating DECIMAL(2,1) DEFAULT 0,
        amenities JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $conn->query($sql);

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
    $sql = "SELECT r.*, h.name as hotel_name, h.city as hotel_city 
            FROM rooms r 
            LEFT JOIN hotels h ON r.hotel_id = h.id 
            WHERE r.is_available = TRUE";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getRoomById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT r.*, h.name as hotel_name, h.city as hotel_city 
                           FROM rooms r 
                           LEFT JOIN hotels h ON r.hotel_id = h.id 
                           WHERE r.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Room Management functions
function addRoom($data) {
    global $conn;
    $sql = "INSERT INTO rooms (name, type, description, price, capacity, size, view_type, amenities, image, hotel_id, is_available) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
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
        $data['hotel_id']
    );
    return $stmt->execute();
}

function updateRoom($id, $data) {
    global $conn;
    $sql = "UPDATE rooms 
            SET name = ?, type = ?, description = ?, price = ?, 
                capacity = ?, size = ?, view_type = ?, amenities = ?, 
                image = ?, hotel_id = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdiisssii", 
        $data['name'],
        $data['type'],
        $data['description'],
        $data['price'],
        $data['capacity'],
        $data['size'],
        $data['view_type'],
        $data['amenities'],
        $data['image'],
        $data['hotel_id'],
        $id
    );
    return $stmt->execute();
}

function deleteRoom($id) {
    global $conn;
    // Check if room has any bookings
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings WHERE room_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] > 0) {
        return false; // Cannot delete room with bookings
    }
    
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Hotel Management functions
function getAllHotels($limit = null) {
    global $conn;
    $sql = "SELECT * FROM hotels ORDER BY created_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getHotelById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM hotels WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function addHotel($data) {
    global $conn;
    $sql = "INSERT INTO hotels (name, city, address, description, image, rating, amenities) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssds", 
        $data['name'],
        $data['city'],
        $data['address'],
        $data['description'],
        $data['image'],
        $data['rating'],
        $data['amenities']
    );
    return $stmt->execute();
}

function updateHotel($id, $data) {
    global $conn;
    $sql = "UPDATE hotels 
            SET name = ?, city = ?, address = ?, description = ?, 
                image = ?, rating = ?, amenities = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssdsi", 
        $data['name'],
        $data['city'],
        $data['address'],
        $data['description'],
        $data['image'],
        $data['rating'],
        $data['amenities'],
        $id
    );
    return $stmt->execute();
}

function deleteHotel($id) {
    global $conn;
    // First check if hotel has any rooms
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM rooms WHERE hotel_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] > 0) {
        return false; // Cannot delete hotel with rooms
    }
    
    $stmt = $conn->prepare("DELETE FROM hotels WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
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

function updateBookingStatus($bookingId, $status) {
    global $conn;
    $sql = "UPDATE bookings SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $bookingId);
    return $stmt->execute();
}

function deleteBooking($bookingId) {
    global $conn;
    $sql = "DELETE FROM bookings WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookingId);
    return $stmt->execute();
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

    return $stats;
}

// Get booked dates for a room
function getBookedDates($roomId) {
    global $conn;
    $sql = "SELECT check_in, check_out 
            FROM bookings 
            WHERE room_id = ? 
            AND status != 'cancelled'
            AND check_out >= CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Admin Management Functions
function getAllBookings($limit = null) {
    global $conn;
    $sql = "SELECT b.*, r.name as room_name, u.name as user_name, h.name as hotel_name 
            FROM bookings b 
            JOIN rooms r ON b.room_id = r.id 
            JOIN users u ON b.user_id = u.id 
            LEFT JOIN hotels h ON r.hotel_id = h.id 
            ORDER BY b.created_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllUsers($limit = null) {
    global $conn;
    $sql = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function updateUserRole($id, $role) {
    global $conn;
    $sql = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $role, $id);
    return $stmt->execute();
}

function deleteUser($userId) {
    global $conn;
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete user's reviews
        $stmt = $conn->prepare("DELETE FROM reviews WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        // Delete user's bookings
        $stmt = $conn->prepare("DELETE FROM bookings WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        // Finally delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        return true;
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        return false;
    }
}

function getAllReviews($limit = null) {
    global $conn;
    $sql = "SELECT r.*, u.name as user_name, rm.name as room_name, h.name as hotel_name 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            JOIN rooms rm ON r.room_id = rm.id 
            LEFT JOIN hotels h ON rm.hotel_id = h.id 
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
    
    $sql = "UPDATE reviews SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id);
    return $stmt->execute();
}

function uploadImage($file) {
    // Create uploads directory if it doesn't exist
    $target_dir = "../uploads";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Generate unique filename
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . uniqid() . '.' . $imageFileType;
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        throw new Exception("File is not an image.");
    }
    
    // Check file size (limit to 5MB)
    if ($file["size"] > 5000000) {
        throw new Exception("File is too large. Maximum size is 5MB.");
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed.");
    }
    
    // Try to upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return str_replace("..", "", $target_file); // Return relative path
    } else {
        throw new Exception("Sorry, there was an error uploading your file.");
    }
}

?>
