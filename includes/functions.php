<?php
require_once 'config.php';

// Database initialization
function initializeDatabase() {
    global $conn;
    
    // Create users table
    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create hotels table
    $conn->query("CREATE TABLE IF NOT EXISTS hotels (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        city VARCHAR(100),
        address TEXT,
        image VARCHAR(255),
        amenities JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Create rooms table
    $conn->query("CREATE TABLE IF NOT EXISTS rooms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        hotel_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        type VARCHAR(50) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        description TEXT,
        image VARCHAR(255),
        capacity INT,
        size DECIMAL(5,2),
        view_type VARCHAR(100),
        amenities JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
    )");

    // Create bookings table with payment_status
    $conn->query("CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        room_id INT NOT NULL,
        user_id INT NOT NULL,
        check_in DATE NOT NULL,
        check_out DATE NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        payment_id VARCHAR(100) DEFAULT NULL,
        payment_method VARCHAR(50) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Create reviews table
    $conn->query("CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id INT NOT NULL,
        user_id INT NOT NULL,
        room_id INT NOT NULL,
        rating INT NOT NULL,
        comment TEXT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
    )");

    // Create payments table
    $conn->query("CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
        transaction_id VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
    )");
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
    
    // Debug: Check connection
    if ($conn->connect_error) {
        error_log("Database connection error in getAllRooms: " . $conn->connect_error);
        return [];
    }
    
    $sql = "SELECT r.*, h.name as hotel_name, h.city as hotel_city 
            FROM rooms r 
            LEFT JOIN hotels h ON r.hotel_id = h.id 
            ORDER BY r.created_at DESC";
            
    if ($limit) {
        $sql .= " LIMIT ?";
    }
    
    try {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare statement failed in getAllRooms: " . $conn->error);
            return [];
        }
        
        if ($limit) {
            $stmt->bind_param("i", $limit);
        }
        
        if (!$stmt->execute()) {
            error_log("Execute failed in getAllRooms: " . $stmt->error);
            return [];
        }
        
        $result = $stmt->get_result();
        if (!$result) {
            error_log("Get result failed in getAllRooms: " . $stmt->error);
            return [];
        }
        
        $rooms = $result->fetch_all(MYSQLI_ASSOC);
        
        // Debug: Log room count
        error_log("Found " . count($rooms) . " rooms in getAllRooms");
        
        // Fix image paths
        foreach ($rooms as &$room) {
            if (isset($room['image'])) {
                $room['image'] = fixImagePath($room['image']);
                error_log("Room image path: " . $room['image']);
            }
        }
        
        return $rooms;
    } catch (Exception $e) {
        error_log("Exception in getAllRooms: " . $e->getMessage());
        return [];
    }
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
    $sql = "INSERT INTO rooms (hotel_id, name, type, price, description, image, capacity, size, view_type, amenities) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    // Convert amenities to JSON if it's not already
    $amenities = isset($data['amenities']) ? $data['amenities'] : '[]';
    if (is_array($amenities)) {
        $amenities = json_encode($amenities);
    }
    
    $stmt->bind_param("issdssiisd", 
        $data['hotel_id'],
        $data['name'],
        $data['type'],
        $data['price'],
        $data['description'],
        $data['image'],
        $data['capacity'],
        $data['size'],
        $data['view_type'],
        $amenities
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
    
    // Get hotels with their average rating from reviews
    $sql = "SELECT h.*, 
            COALESCE(AVG(r.rating), 0) as average_rating,
            COUNT(DISTINCT r.id) as review_count
            FROM hotels h
            LEFT JOIN rooms rm ON h.id = rm.hotel_id
            LEFT JOIN reviews r ON rm.id = r.room_id AND r.status = 'approved'
            GROUP BY h.id
            ORDER BY h.name";
            
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $result = $conn->query($sql);
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
    $sql = "SELECT h.*, 
            COALESCE(AVG(r.rating), 0) as average_rating,
            COUNT(DISTINCT r.id) as review_count
            FROM hotels h
            LEFT JOIN rooms rm ON h.id = rm.hotel_id
            LEFT JOIN reviews r ON rm.id = r.room_id AND r.status = 'approved'
            WHERE h.id = ?
            GROUP BY h.id";
    $stmt = $conn->prepare($sql);
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
function createBooking($room_id, $user_id, $check_in, $check_out, $total_price) {
    global $conn;
    
    try {
        $conn->begin_transaction();
        
        $sql = "INSERT INTO bookings (room_id, user_id, check_in, check_out, total_price, status, payment_id, payment_method) 
                VALUES (?, ?, ?, ?, ?, 'pending', NULL, NULL)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissd", $room_id, $user_id, $check_in, $check_out, $total_price);
        
        if ($stmt->execute()) {
            $booking_id = $conn->insert_id;
            $conn->commit();
            return $booking_id;
        } else {
            $conn->rollback();
            return false;
        }
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

function getBookingsByUser($user_id) {
    global $conn;
    
    $sql = "SELECT b.*, r.name as room_name, r.image as room_image, r.type as room_type, 
            h.name as hotel_name, h.city as hotel_city 
            FROM bookings b 
            LEFT JOIN rooms r ON b.room_id = r.id 
            LEFT JOIN hotels h ON r.hotel_id = h.id 
            WHERE b.user_id = ?";
            
    error_log("Getting bookings for user: " . $user_id);
    error_log("SQL Query: " . $sql);
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return [];
    }
    
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return [];
    }
    
    $result = $stmt->get_result();
    $bookings = $result->fetch_all(MYSQLI_ASSOC);
    
    error_log("Found " . count($bookings) . " bookings");
    if (count($bookings) === 0) {
        error_log("No bookings found. Last SQL error: " . $conn->error);
    }
    
    return $bookings;
}

function updateBookingStatus($booking_id, $status) {
    global $conn;
    $sql = "UPDATE bookings SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $booking_id);
    return $stmt->execute();
}

function deleteBooking($booking_id) {
    global $conn;
    $sql = "DELETE FROM bookings WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    return $stmt->execute();
}

// Review functions
function createReview($booking_id, $user_id, $room_id, $rating, $comment) {
    global $conn;
    $sql = "INSERT INTO reviews (booking_id, user_id, room_id, rating, comment) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiis", $booking_id, $user_id, $room_id, $rating, $comment);
    return $stmt->execute();
}

function getReviewsByRoom($room_id) {
    global $conn;
    $sql = "SELECT r.*, u.name as user_name 
            FROM reviews r 
            LEFT JOIN users u ON r.user_id = u.id 
            WHERE r.room_id = ? AND r.status = 'approved'
            ORDER BY r.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
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

// User role functions
function isAdmin($user_id) {
    global $conn;
    $sql = "SELECT role FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        return $user['role'] === 'admin';
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
function getBookedDates($room_id) {
    global $conn;
    $dates = [];
    
    $sql = "SELECT check_in, check_out 
            FROM bookings 
            WHERE room_id = ? 
            AND status != 'cancelled' 
            AND check_out >= CURRENT_DATE()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($booking = $result->fetch_assoc()) {
        $current = new DateTime($booking['check_in']);
        $end = new DateTime($booking['check_out']);
        
        while ($current < $end) {
            $dates[] = $current->format('Y-m-d');
            $current->modify('+1 day');
        }
    }
    
    return $dates;
}

function getUserById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
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

function deleteUser($user_id) {
    global $conn;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete user's bookings
        $stmt = $conn->prepare("DELETE FROM bookings WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Delete user's reviews
        $stmt = $conn->prepare("DELETE FROM reviews WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Finally, delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
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
