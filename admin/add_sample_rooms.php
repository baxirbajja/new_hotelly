<?php
require_once '../includes/functions.php';

// Get all hotels
$hotels = getAllHotels();

// Sample room types for Moroccan-themed hotels
$roomTypes = [
    [
        'name' => 'Royal Suite',
        'description' => 'Luxurious suite with traditional Moroccan decor, featuring hand-carved furniture, intricate tilework, and a private balcony overlooking the city.',
        'image' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b',
        'type' => 'Suite',
        'price' => 500.00,
        'capacity' => 4,
        'size' => 75,
        'view_type' => 'City View'
    ],
    [
        'name' => 'Deluxe Room',
        'description' => 'Elegant room with modern amenities and traditional Moroccan touches, featuring a comfortable seating area and stunning views.',
        'image' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461',
        'type' => 'Deluxe',
        'price' => 300.00,
        'capacity' => 2,
        'size' => 45,
        'view_type' => 'Garden View'
    ],
    [
        'name' => 'Standard Room',
        'description' => 'Comfortable room with authentic Moroccan design elements, perfect for a cozy stay with modern conveniences.',
        'image' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427',
        'type' => 'Standard',
        'price' => 200.00,
        'capacity' => 2,
        'size' => 35,
        'view_type' => 'City View'
    ]
];

// Add rooms for each hotel
foreach ($hotels as $hotel) {
    foreach ($roomTypes as $roomType) {
        $data = array_merge($roomType, ['hotel_id' => $hotel['id']]);
        
        try {
            if (addRoom($data)) {
                echo "Added {$roomType['name']} to {$hotel['name']}<br>";
            } else {
                echo "Failed to add {$roomType['name']} to {$hotel['name']}<br>";
            }
        } catch (Exception $e) {
            echo "Error adding {$roomType['name']} to {$hotel['name']}: {$e->getMessage()}<br>";
        }
    }
}

echo "<br>Done adding sample rooms!";
?>
