<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Deluxe Ocean View',
                'description' => 'Spacious room with breathtaking ocean views, featuring a king-size bed, private balcony, and luxury amenities.',
                'price' => 299.99,
                'capacity' => 2,
                'is_available' => true,
                'amenities' => ['Ocean View', 'King Bed', 'Private Balcony', 'Mini Bar', 'Room Service', 'Free Wi-Fi', 'Air Conditioning']
            ],
            [
                'name' => 'Family Suite',
                'description' => 'Perfect for families, this suite offers two bedrooms, a living area, and all the comforts of home.',
                'price' => 449.99,
                'capacity' => 4,
                'is_available' => true,
                'amenities' => ['Two Bedrooms', 'Living Area', 'Kitchen', 'Family Entertainment', 'Free Wi-Fi', 'Air Conditioning']
            ],
            [
                'name' => 'Executive Suite',
                'description' => 'Luxurious suite with separate working area, perfect for business travelers seeking comfort and functionality.',
                'price' => 399.99,
                'capacity' => 2,
                'is_available' => true,
                'amenities' => ['Work Desk', 'Living Area', 'King Bed', 'Mini Bar', 'Room Service', 'Free Wi-Fi', 'Air Conditioning']
            ],
            [
                'name' => 'Honeymoon Suite',
                'description' => 'Romantic suite with panoramic views, featuring a champagne bar and luxury spa bath.',
                'price' => 499.99,
                'capacity' => 2,
                'is_available' => true,
                'amenities' => ['Panoramic View', 'King Bed', 'Spa Bath', 'Champagne Bar', 'Room Service', 'Free Wi-Fi', 'Air Conditioning']
            ],
            [
                'name' => 'Standard Double',
                'description' => 'Comfortable room with all essential amenities for a pleasant stay.',
                'price' => 199.99,
                'capacity' => 2,
                'is_available' => true,
                'amenities' => ['Double Bed', 'Basic Amenities', 'Free Wi-Fi', 'Air Conditioning']
            ],
            [
                'name' => 'Premium Twin',
                'description' => 'Spacious room with two single beds, perfect for friends or business colleagues traveling together.',
                'price' => 249.99,
                'capacity' => 2,
                'is_available' => true,
                'amenities' => ['Twin Beds', 'Work Desk', 'Mini Bar', 'Free Wi-Fi', 'Air Conditioning']
            ]
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
