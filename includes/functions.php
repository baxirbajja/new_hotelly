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

    // Create rooms table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS rooms (
        id INT PRIMARY KEY AUTO_INCREMENT,
        hotel_id INT,
        name VARCHAR(255) NOT NULL,
        type VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        description TEXT,
        image VARCHAR(255),
        amenities JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
    )";
    $conn->query($sql);

    // Create users table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);

    // Create bookings table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS bookings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        room_id INT,
        user_id INT,
        check_in DATE NOT NULL,
        check_out DATE NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
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
}

// Call initialization
initializeDatabase();

function fixImagePath($path) {
    // If the path doesn't start with /new_hotelly/ and is not a URL, add it
    if ($path && strpos($path, '/new_hotelly/') !== 0 && strpos($path, 'http') !== 0) {
        return '/new_hotelly' . $path;
    }
    return $path;
}

// Room functions
function getAllRooms($limit = null) {
    global $conn;
    $sql = "SELECT r.*, h.name as hotel_name, h.city as hotel_city 
            FROM rooms r 
            LEFT JOIN hotels h ON r.hotel_id = h.id 
            ORDER BY r.created_at DESC";
    if ($limit) {
        $sql .= " LIMIT ?";
    }
    $stmt = $conn->prepare($sql);
    if ($limit) {
        $stmt->bind_param("i", $limit);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $rooms = $result->fetch_all(MYSQLI_ASSOC);
    
    // Fix image paths
    foreach ($rooms as &$room) {
        if (isset($room['image'])) {
            $room['image'] = fixImagePath($room['image']);
        }
    }
    
    return $rooms;
}

function getRoomById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT r.*, h.name as hotel_name, h.city as hotel_city 
                           FROM rooms r 
                           LEFT JOIN hotels h ON r.hotel_id = h.id 
                           WHERE r.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $room = $stmt->get_result()->fetch_assoc();
    if ($room && isset($room['image'])) {
        $room['image'] = fixImagePath($room['image']);
    }
    return $room;
}

function addRoom($data) {
    global $conn;
    $sql = "INSERT INTO rooms (hotel_id, name, type, price, description, image, amenities) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issdsss", 
        $data['hotel_id'],
        $data['name'],
        $data['type'],
        $data['price'],
        $data['description'],
        $data['image'],
        $data['amenities']
    );
    return $stmt->execute();
}

function updateRoom($id, $data) {
    global $conn;
    $sql = "UPDATE rooms SET ";
    $params = [];
    $types = "";
    
    foreach ($data as $key => $value) {
        $sql .= "$key = ?, ";
        $params[] = $value;
        $types .= "s";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    return $stmt->execute();
}

function deleteRoom($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Hotel Management functions
function getAllHotels($limit = null) {
    global $conn;
    $sql = "SELECT id, name, city, image, rating FROM hotels ORDER BY created_at DESC";
    if ($limit) {
        $sql .= " LIMIT ?";
    }
    $stmt = $conn->prepare($sql);
    if ($limit) {
        $stmt->bind_param("i", $limit);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $hotels = $result->fetch_all(MYSQLI_ASSOC);
    
    // Fix image paths
    foreach ($hotels as &$hotel) {
        if (isset($hotel['image'])) {
            $hotel['image'] = fixImagePath($hotel['image']);
        }
    }
    
    return $hotels;
}

function getHotelById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM hotels WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $hotel = $stmt->get_result()->fetch_assoc();
    if ($hotel && isset($hotel['image'])) {
        $hotel['image'] = fixImagePath($hotel['image']);
    }
    return $hotel;
}

function addHotel($data) {
    global $conn;
    $sql = "INSERT INTO hotels (name, city, address, description, image, rating, amenities) VALUES (?, ?, ?, ?, ?, ?, ?)";
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
    $sql = "UPDATE hotels SET ";
    $params = [];
    $types = "";
    
    foreach ($data as $key => $value) {
        $sql .= "$key = ?, ";
        $params[] = $value;
        $types .= "s";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    return $stmt->execute();
}

function deleteHotel($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM hotels WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Booking functions
function createBooking($roomId, $userId, $checkIn, $checkOut, $totalPrice) {
    global $conn;
    $sql = "INSERT INTO bookings (room_id, user_id, check_in, check_out, total_price) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissd", $roomId, $userId, $checkIn, $checkOut, $totalPrice);
    return $stmt->execute();
}

function getBookingsByUser($userId) {
    global $conn;
    $sql = "SELECT b.*, r.name as room_name, r.type as room_type, h.name as hotel_name 
            FROM bookings b 
            LEFT JOIN rooms r ON b.room_id = r.id 
            LEFT JOIN hotels h ON r.hotel_id = h.id 
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
    $sql = "INSERT INTO reviews (booking_id, user_id, room_id, rating, comment) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiis", $bookingId, $userId, $roomId, $rating, $comment);
    return $stmt->execute();
}

function getReviewsByRoom($roomId) {
    global $conn;
    $sql = "SELECT r.*, u.name as user_name 
            FROM reviews r 
            LEFT JOIN users u ON r.user_id = u.id 
            WHERE r.room_id = ? AND r.status = 'approved'
            ORDER BY r.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// User functions
function createUser($name, $email, $password) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);
    return $stmt->execute();
}

function getUserByEmail($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
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
    
    $stats = [
        'total_users' => 0,
        'total_bookings' => 0,
        'total_revenue' => 0,
        'total_hotels' => 0,
        'total_rooms' => 0,
        'recent_bookings' => [],
        'recent_users' => [],
        'recent_reviews' => []
    ];
    
    // Get total users
    $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
    $stats['total_users'] = $result->fetch_assoc()['count'];
    
    // Get total bookings and revenue
    $result = $conn->query("SELECT COUNT(*) as count, SUM(total_price) as revenue FROM bookings WHERE status != 'cancelled'");
    $row = $result->fetch_assoc();
    $stats['total_bookings'] = $row['count'];
    $stats['total_revenue'] = $row['revenue'] ?? 0;
    
    // Get total hotels
    $result = $conn->query("SELECT COUNT(*) as count FROM hotels");
    $stats['total_hotels'] = $result->fetch_assoc()['count'];
    
    // Get total rooms
    $result = $conn->query("SELECT COUNT(*) as count FROM rooms");
    $stats['total_rooms'] = $result->fetch_assoc()['count'];
    
    // Get recent bookings
    $sql = "SELECT b.*, u.name as user_name, r.name as room_name 
            FROM bookings b 
            LEFT JOIN users u ON b.user_id = u.id 
            LEFT JOIN rooms r ON b.room_id = r.id 
            ORDER BY b.created_at DESC LIMIT 5";
    $result = $conn->query($sql);
    $stats['recent_bookings'] = $result->fetch_all(MYSQLI_ASSOC);
    
    // Get recent users
    $sql = "SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT 5";
    $result = $conn->query($sql);
    $stats['recent_users'] = $result->fetch_all(MYSQLI_ASSOC);
    
    // Get recent reviews
    $sql = "SELECT r.*, u.name as user_name, rm.name as room_name, h.name as hotel_name 
            FROM reviews r 
            LEFT JOIN users u ON r.user_id = u.id 
            LEFT JOIN rooms rm ON r.room_id = rm.id 
            LEFT JOIN hotels h ON rm.hotel_id = h.id 
            ORDER BY r.created_at DESC LIMIT 5";
    $result = $conn->query($sql);
    $stats['recent_reviews'] = $result->fetch_all(MYSQLI_ASSOC);
    
    return $stats;
}

// Get booked dates for a room
function getBookedDates($roomId) {
    global $conn;
    $sql = "SELECT check_in, check_out 
            FROM bookings 
            WHERE room_id = ? 
            AND status != 'cancelled' 
            AND check_out >= CURRENT_DATE()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Admin Management Functions
function getAllBookings($limit = null) {
    global $conn;
    $sql = "SELECT b.*, u.name as user_name, r.name as room_name, h.name as hotel_name 
            FROM bookings b 
            LEFT JOIN users u ON b.user_id = u.id 
            LEFT JOIN rooms r ON b.room_id = r.id 
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
    $sql = "SELECT * FROM users ORDER BY created_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function updateUserRole($id, $role) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role, $id);
    return $stmt->execute();
}

function deleteUser($userId) {
    global $conn;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete user's bookings
        $stmt = $conn->prepare("DELETE FROM bookings WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        // Delete user's reviews
        $stmt = $conn->prepare("DELETE FROM reviews WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        // Finally, delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        // If we got here, commit the changes
        $conn->commit();
        return true;
    } catch (Exception $e) {
        // An error occurred; rollback the changes
        $conn->rollback();
        return false;
    }
}

function getAllReviews($limit = null) {
    global $conn;
    $sql = "SELECT r.*, u.name as user_name, rm.name as room_name, h.name as hotel_name 
            FROM reviews r 
            LEFT JOIN users u ON r.user_id = u.id 
            LEFT JOIN rooms rm ON r.room_id = rm.id 
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
    $sql = "UPDATE reviews SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id);
    return $stmt->execute();
}

function uploadImage($file) {
    try {
        // Create uploads directory if it doesn't exist
        $upload_dir = "uploads/";  // Relative to website root
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/new_hotelly/" . $upload_dir;
        
        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                error_log("Failed to create directory: " . $target_dir);
                throw new Exception("Failed to create upload directory");
            }
        }

        // Check if directory is writable
        if (!is_writable($target_dir)) {
            error_log("Upload directory is not writable: " . $target_dir);
            throw new Exception("Upload directory is not writable");
        }

        // Generate unique filename
        $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $unique_filename = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $unique_filename;
        
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
            error_log("File uploaded successfully to: " . $target_file);
            // Return path relative to the website root
            return "/new_hotelly/" . $upload_dir . $unique_filename;
        } else {
            error_log("Failed to move uploaded file to: " . $target_file);
            error_log("Upload error: " . error_get_last()['message']);
            throw new Exception("Sorry, there was an error uploading your file.");
        }
    } catch (Exception $e) {
        error_log("Image upload error: " . $e->getMessage());
        throw $e;
    }
}

?>
