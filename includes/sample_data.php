<?php
require_once 'config.php';

// Clear existing data
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("TRUNCATE TABLE reviews");
$conn->query("TRUNCATE TABLE bookings");
$conn->query("TRUNCATE TABLE rooms");
$conn->query("TRUNCATE TABLE users");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// Sample room data for a Moroccan hotel
$rooms = [
    [
        'name' => 'Royal Riad Suite',
        'type' => 'Suite',
        'description' => 'Experience authentic Moroccan luxury in our Royal Riad Suite. This spacious suite features traditional Moroccan architecture, hand-crafted furniture, and a private terrace overlooking the Atlas Mountains. The suite includes a hammam-style bathroom with marble finishes.',
        'price' => 450.00,
        'capacity' => 2,
        'size' => 75,
        'view_type' => 'Mountain View',
        'amenities' => json_encode(['King Bed', 'Private Terrace', 'Traditional Hammam', 'Air Conditioning', 'Mini Bar', 'Free Wi-Fi', '24/7 Room Service']),
        'image' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?q=80&w=1200'
    ],
    [
        'name' => 'Marrakech Deluxe Room',
        'type' => 'Deluxe',
        'description' => 'Our Marrakech Deluxe Room combines modern comfort with traditional Moroccan design. Featuring colorful zellige tiles, carved wooden furniture, and a comfortable seating area. Perfect for those seeking an authentic Moroccan experience.',
        'price' => 250.00,
        'capacity' => 2,
        'size' => 45,
        'view_type' => 'Medina View',
        'amenities' => json_encode(['Queen Bed', 'Seating Area', 'Air Conditioning', 'Mini Bar', 'Free Wi-Fi', 'Room Service']),
        'image' => 'https://images.unsplash.com/photo-1590073242678-70ee3fc28e8e?q=80&w=1200'
    ],
    [
        'name' => 'Sahara Family Suite',
        'type' => 'Suite',
        'description' => 'Perfect for families, our Sahara Suite offers plenty of space and stunning desert-inspired décor. The suite includes a master bedroom and a separate living area that can accommodate additional guests. Enjoy panoramic views of the city.',
        'price' => 350.00,
        'capacity' => 4,
        'size' => 85,
        'view_type' => 'City View',
        'amenities' => json_encode(['King Bed', 'Sofa Bed', 'Living Room', 'Air Conditioning', 'Mini Bar', 'Free Wi-Fi', 'Family Entertainment']),
        'image' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?q=80&w=1200'
    ],
    [
        'name' => 'Casablanca Standard Room',
        'type' => 'Standard',
        'description' => 'Our Casablanca Standard Room offers comfortable accommodation with traditional Moroccan touches. The room features local artwork, comfortable bedding, and modern amenities for a pleasant stay.',
        'price' => 150.00,
        'capacity' => 2,
        'size' => 30,
        'view_type' => 'Garden View',
        'amenities' => json_encode(['Queen Bed', 'Air Conditioning', 'Free Wi-Fi', 'Room Service']),
        'image' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?q=80&w=1200'
    ],
    [
        'name' => 'Atlas Mountain View Suite',
        'type' => 'Suite',
        'description' => 'Wake up to breathtaking views of the Atlas Mountains in this luxurious suite. Featuring a private balcony, traditional Moroccan décor, and premium amenities. The perfect choice for a romantic getaway.',
        'price' => 400.00,
        'capacity' => 2,
        'size' => 65,
        'view_type' => 'Mountain View',
        'amenities' => json_encode(['King Bed', 'Private Balcony', 'Luxury Bathroom', 'Air Conditioning', 'Mini Bar', 'Free Wi-Fi', '24/7 Room Service']),
        'image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=1200'
    ]
];

// Insert rooms
$stmt = $conn->prepare("INSERT INTO rooms (name, type, description, price, capacity, size, view_type, amenities, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($rooms as $room) {
    $stmt->bind_param("sssdiisss", 
        $room['name'],
        $room['type'],
        $room['description'],
        $room['price'],
        $room['capacity'],
        $room['size'],
        $room['view_type'],
        $room['amenities'],
        $room['image']
    );
    $stmt->execute();
}

// Sample user data
$users = [
    [
        'name' => 'Mohammed Alami',
        'email' => 'mohammed@example.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'user'
    ],
    [
        'name' => 'Fatima Zahra',
        'email' => 'fatima@example.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'user'
    ],
    [
        'name' => 'Admin User',
        'email' => 'admin@hotelly.com',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'admin'
    ]
];

// Insert users
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");

foreach ($users as $user) {
    $stmt->bind_param("ssss", 
        $user['name'],
        $user['email'],
        $user['password'],
        $user['role']
    );
    $stmt->execute();
}

// Sample bookings
$bookings = [
    [
        'room_id' => 1,
        'user_id' => 1,
        'check_in' => '2024-01-15',
        'check_out' => '2024-01-20',
        'total_price' => 2250.00,
        'status' => 'confirmed'
    ],
    [
        'room_id' => 2,
        'user_id' => 2,
        'check_in' => '2024-02-01',
        'check_out' => '2024-02-05',
        'total_price' => 1000.00,
        'status' => 'pending'
    ]
];

// Insert bookings
$stmt = $conn->prepare("INSERT INTO bookings (room_id, user_id, check_in, check_out, total_price, status) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($bookings as $booking) {
    $stmt->bind_param("iissds", 
        $booking['room_id'],
        $booking['user_id'],
        $booking['check_in'],
        $booking['check_out'],
        $booking['total_price'],
        $booking['status']
    );
    $stmt->execute();
}

// Sample reviews
$reviews = [
    [
        'booking_id' => 1,
        'user_id' => 1,
        'room_id' => 1,
        'rating' => 5,
        'comment' => 'Amazing stay! The Royal Riad Suite was absolutely beautiful. The traditional Moroccan design and mountain views were breathtaking. Staff was very helpful and attentive.'
    ],
    [
        'booking_id' => 2,
        'user_id' => 2,
        'room_id' => 2,
        'rating' => 4,
        'comment' => 'Great experience in the Marrakech Deluxe Room. Loved the traditional décor and the location was perfect for exploring the medina.'
    ]
];

// Insert reviews
$stmt = $conn->prepare("INSERT INTO reviews (booking_id, user_id, room_id, rating, comment) VALUES (?, ?, ?, ?, ?)");

foreach ($reviews as $review) {
    $stmt->bind_param("iiiis", 
        $review['booking_id'],
        $review['user_id'],
        $review['room_id'],
        $review['rating'],
        $review['comment']
    );
    $stmt->execute();
}

echo "Sample data has been inserted successfully!";
?>
