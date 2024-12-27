<?php
require_once __DIR__ . '/../includes/functions.php';

// Check if hotels already exist
$result = $conn->query("SELECT COUNT(*) as count FROM hotels");
$row = $result->fetch_assoc();
if ($row['count'] > 0) {
    echo "Hotels and rooms already exist in the database.";
    exit;
}

// Sample Moroccan hotels
$hotels = [
    [
        'name' => 'Riad Royal Marrakech',
        'description' => 'Un riad authentique situé au cœur de la médina de Marrakech, offrant une expérience traditionnelle marocaine avec un service 5 étoiles.',
        'city' => 'Marrakech',
        'address' => 'Médina, Marrakech 40000, Maroc',
        'image' => '../uploads/riad_marrakech.jpg',
        'amenities' => json_encode(['Piscine', 'Spa', 'Restaurant', 'Wifi gratuit', 'Service en chambre', 'Terrasse sur le toit'])
    ],
    [
        'name' => 'Kasbah Atlas View',
        'description' => 'Une kasbah luxueuse avec vue imprenable sur les montagnes de l\'Atlas, combinant architecture traditionnelle et confort moderne.',
        'city' => 'Ouarzazate',
        'address' => 'Route de l\'Atlas, Ouarzazate 45000, Maroc',
        'image' => '../uploads/kasbah_atlas.jpg',
        'amenities' => json_encode(['Piscine extérieure', 'Restaurant panoramique', 'Excursions désert', 'Wifi gratuit', 'Parking'])
    ],
    [
        'name' => 'Palais Faraj Fes',
        'description' => 'Un palais restauré du 19ème siècle offrant une vue panoramique sur la médina de Fès, avec une décoration artisanale authentique.',
        'city' => 'Fès',
        'address' => 'Médina, Fès 30000, Maroc',
        'image' => '../uploads/palais_fes.jpg',
        'amenities' => json_encode(['Hammam', 'Spa', 'Restaurant gastronomique', 'Terrasse', 'Wifi gratuit', 'Service de conciergerie'])
    ],
    [
        'name' => 'Marina Bay Tanger',
        'description' => 'Hôtel moderne situé sur la baie de Tanger, offrant une vue imprenable sur la Méditerranée et l\'océan Atlantique.',
        'city' => 'Tanger',
        'address' => 'Boulevard Mohammed VI, Tanger 90000, Maroc',
        'image' => '../uploads/marina_tanger.jpg',
        'amenities' => json_encode(['Plage privée', 'Piscine infinity', 'Spa', 'Restaurants', 'Bar en terrasse', 'Centre de fitness'])
    ]
];

// Insert hotels
foreach ($hotels as $hotel) {
    $stmt = $conn->prepare("INSERT INTO hotels (name, description, city, address, image, amenities) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", 
        $hotel['name'], 
        $hotel['description'], 
        $hotel['city'], 
        $hotel['address'], 
        $hotel['image'], 
        $hotel['amenities']
    );
    $stmt->execute();
    $hotel_ids[$hotel['name']] = $conn->insert_id;
}

// Sample rooms for each hotel
$rooms = [
    // Riad Royal Marrakech
    [
        'hotel_name' => 'Riad Royal Marrakech',
        'rooms' => [
            [
                'name' => 'Suite Royale',
                'type' => 'Suite',
                'price' => 2500,
                'description' => 'Suite luxueuse avec salon privé et terrasse, décorée dans un style traditionnel marocain.',
                'image' => '../uploads/suite_royale.jpg',
                'capacity' => 3,
                'size' => 45,
                'view_type' => 'Patio et jardin',
                'amenities' => json_encode(['Lit king-size', 'Salon privé', 'Salle de bain en marbre', 'Terrasse privée', 'Climatisation'])
            ],
            [
                'name' => 'Chambre Deluxe',
                'type' => 'Deluxe',
                'price' => 1800,
                'description' => 'Chambre spacieuse avec balcon donnant sur le patio, meubles artisanaux.',
                'image' => '../uploads/chambre_deluxe.jpg',
                'capacity' => 2,
                'size' => 35,
                'view_type' => 'Patio',
                'amenities' => json_encode(['Lit queen-size', 'Balcon privé', 'Climatisation', 'Coffre-fort'])
            ]
        ]
    ],
    // Kasbah Atlas View
    [
        'hotel_name' => 'Kasbah Atlas View',
        'rooms' => [
            [
                'name' => 'Suite Atlas',
                'type' => 'Suite',
                'price' => 2200,
                'description' => 'Suite panoramique avec vue sur les montagnes de l\'Atlas.',
                'image' => '../uploads/suite_atlas.jpg',
                'capacity' => 4,
                'size' => 50,
                'view_type' => 'Montagnes',
                'amenities' => json_encode(['2 Lits king-size', 'Salon', 'Terrasse privée', 'Cheminée'])
            ],
            [
                'name' => 'Chambre Berbère',
                'type' => 'Standard',
                'price' => 1500,
                'description' => 'Chambre authentique avec décoration berbère traditionnelle.',
                'image' => '../uploads/chambre_berbere.jpg',
                'capacity' => 2,
                'size' => 30,
                'view_type' => 'Jardin',
                'amenities' => json_encode(['Lit double', 'Artisanat local', 'Climatisation'])
            ]
        ]
    ],
    // Palais Faraj Fes
    [
        'hotel_name' => 'Palais Faraj Fes',
        'rooms' => [
            [
                'name' => 'Suite Impériale',
                'type' => 'Suite',
                'price' => 3000,
                'description' => 'Suite luxueuse avec plafonds sculptés et vue sur la médina.',
                'image' => '../uploads/suite_imperiale.jpg',
                'capacity' => 3,
                'size' => 55,
                'view_type' => 'Médina',
                'amenities' => json_encode(['Lit king-size', 'Salon marocain', 'Salle de bain en zellige', 'Terrasse privée'])
            ],
            [
                'name' => 'Chambre Médina',
                'type' => 'Deluxe',
                'price' => 1900,
                'description' => 'Chambre élégante avec vue sur la vieille ville.',
                'image' => '../uploads/chambre_medina.jpg',
                'capacity' => 2,
                'size' => 35,
                'view_type' => 'Médina',
                'amenities' => json_encode(['Lit queen-size', 'Artisanat fassi', 'Climatisation'])
            ]
        ]
    ],
    // Marina Bay Tanger
    [
        'hotel_name' => 'Marina Bay Tanger',
        'rooms' => [
            [
                'name' => 'Suite Méditerranée',
                'type' => 'Suite',
                'price' => 2800,
                'description' => 'Suite moderne avec vue panoramique sur la mer.',
                'image' => '../uploads/suite_mediterranee.jpg',
                'capacity' => 3,
                'size' => 60,
                'view_type' => 'Mer',
                'amenities' => json_encode(['Lit king-size', 'Salon', 'Balcon privé', 'Bain à remous'])
            ],
            [
                'name' => 'Chambre Ocean',
                'type' => 'Deluxe',
                'price' => 2000,
                'description' => 'Chambre contemporaine avec vue sur l\'océan.',
                'image' => '../uploads/chambre_ocean.jpg',
                'capacity' => 2,
                'size' => 40,
                'view_type' => 'Océan',
                'amenities' => json_encode(['Lit queen-size', 'Balcon', 'Mini-bar', 'Station d\'accueil'])
            ]
        ]
    ]
];

// Insert rooms
foreach ($rooms as $hotel_rooms) {
    $hotel_id = $hotel_ids[$hotel_rooms['hotel_name']];
    foreach ($hotel_rooms['rooms'] as $room) {
        $stmt = $conn->prepare("INSERT INTO rooms (hotel_id, name, type, price, description, image, capacity, size, view_type, amenities) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdssidss", 
            $hotel_id,
            $room['name'],
            $room['type'],
            $room['price'],
            $room['description'],
            $room['image'],
            $room['capacity'],
            $room['size'],
            $room['view_type'],
            $room['amenities']
        );
        $stmt->execute();
    }
}

// Create a regular user
$user_password = password_hash('user123', PASSWORD_DEFAULT);
$conn->query("INSERT INTO users (name, email, password, role) VALUES ('Mohammed', 'user@hotelly.com', '$user_password', 'user')");

echo "Sample Moroccan hotels and rooms added successfully!";
?>
