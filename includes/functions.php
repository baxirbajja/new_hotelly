<?php
require_once 'config.php';

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
    $sql = "INSERT INTO reviews (booking_id, user_id, room_id, rating, comment) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiis", $bookingId, $userId, $roomId, $rating, $comment);
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
?>
