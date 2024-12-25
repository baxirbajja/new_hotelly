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
    $id = $conn->real_escape_string($id);
    $sql = "SELECT * FROM rooms WHERE id = '$id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Booking functions
function createBooking($roomId, $userId, $checkIn, $checkOut, $totalPrice) {
    global $conn;
    $sql = "INSERT INTO bookings (room_id, user_id, check_in, check_out, total_price) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissd", $roomId, $userId, $checkIn, $checkOut, $totalPrice);
    return $stmt->execute();
}

function getBookingsByUser($userId) {
    global $conn;
    $userId = $conn->real_escape_string($userId);
    $sql = "SELECT b.*, r.name as room_name 
            FROM bookings b 
            JOIN rooms r ON b.room_id = r.id 
            WHERE b.user_id = '$userId'";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Review functions
function createReview($bookingId, $rating, $comment) {
    global $conn;
    $sql = "INSERT INTO reviews (booking_id, rating, comment) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $bookingId, $rating, $comment);
    return $stmt->execute();
}

function getReviewsByRoom($roomId) {
    global $conn;
    $roomId = $conn->real_escape_string($roomId);
    $sql = "SELECT r.*, u.name as user_name 
            FROM reviews r 
            JOIN bookings b ON r.booking_id = b.id 
            JOIN users u ON b.user_id = u.id 
            WHERE b.room_id = '$roomId'";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
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
    $email = $conn->real_escape_string($email);
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function validateUser($email, $password) {
    $user = getUserByEmail($email);
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}
?>
