-- First, let's clear existing data to avoid duplicates
DELETE FROM rooms;
DELETE FROM hotels;

-- Insert Moroccan Hotels
INSERT INTO hotels (name, description, city, address, image, amenities) VALUES
(
    'La Mamounia Marrakech',
    'La Mamounia is a luxury hotel in Marrakech, Morocco. Set within the walls of the old city, it is one of the most famous hotels in Morocco.',
    'Marrakech',
    'Avenue Bab Jdid, 40040 Marrakech, Morocco',
    'https://images.unsplash.com/photo-1582719508461-905c673771fd?q=80&w=1200',
    '["Swimming Pool", "Spa", "Restaurant", "Bar", "Garden", "Fitness Center", "Free WiFi"]'
),
(
    'Royal Mansour Marrakech',
    'The Royal Mansour Marrakech offers an unparalleled experience of wonder and authenticity, where Moroccan tradition and hospitality receive a modern touch.',
    'Marrakech',
    'Rue Abou Abbas El Sebti, 40000 Marrakech, Morocco',
    'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=1200',
    '["Private Pool", "Spa", "Fine Dining", "Butler Service", "Garden", "Hammam"]'
),
(
    'Four Seasons Resort Marrakech',
    'An oasis of year-round luxury in the heart of the Red City, with views of the Atlas Mountains.',
    'Marrakech',
    '1 Boulevard de la Menara, 40000 Marrakech, Morocco',
    'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=1200',
    '["Outdoor Pools", "Spa", "Tennis Courts", "Kids Club", "Restaurants", "Bars"]'
),
(
    'Mazagan Beach Resort',
    'Located on the Atlantic coast, Mazagan Beach Resort offers breathtaking views and world-class amenities.',
    'El Jadida',
    'Route de Casablanca, 24000 El Jadida, Morocco',
    'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?q=80&w=1200',
    '["Beach Access", "Golf Course", "Casino", "Spa", "Kids Club", "Multiple Restaurants"]'
),
(
    'Fairmont Royal Palm Marrakech',
    'A luxury resort combining Arabic-Moorish architecture with Moroccan hospitality.',
    'Marrakech',
    'Route dAmizmiz, 42302 Marrakech, Morocco',
    'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=1200',
    '["Golf Course", "Spa", "Tennis Academy", "Kids Club", "Restaurants"]'
),
(
    'Sofitel Agadir Royal Bay Resort',
    'Luxury beachfront resort combining French elegance with Moroccan tradition.',
    'Agadir',
    'Baie des Palmiers, 80000 Agadir, Morocco',
    'https://images.unsplash.com/photo-1549294413-26f195471d9b?q=80&w=1200',
    '["Private Beach", "Spa", "Pool", "Tennis Courts", "Water Sports"]'
),
(
    'Mandarin Oriental Marrakech',
    'Contemporary luxury meets traditional Moroccan charm in spacious villas and suites.',
    'Marrakech',
    'Route du Golf Royal, 40000 Marrakech, Morocco',
    'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?q=80&w=1200',
    '["Private Gardens", "Spa", "Golf", "Pools", "Fine Dining"]'
),
(
    'Palais Faraj Suites & Spa',
    'A luxurious palace hotel offering panoramic views of the Fes Medina, combining traditional Moroccan architecture with modern comfort.',
    'Fes',
    'Bab Ziat, Quartier Ziat, 30000 Fes, Morocco',
    'https://images.unsplash.com/photo-1577493340887-b7bfff550145?q=80&w=1200',
    '["Spa", "Restaurant", "Rooftop Terrace", "Pool", "Traditional Hammam"]'
),
(
    'Riad Fes Maya Suite & Spa',
    'An authentic riad in the heart of Fes, offering a peaceful retreat with traditional Moroccan hospitality.',
    'Fes',
    'Quartier Batha, 30000 Fes, Morocco',
    'https://images.unsplash.com/photo-1539437829697-1b4ed5aebd19?q=80&w=1200',
    '["Spa", "Indoor Pool", "Restaurant", "Courtyard Garden", "Hammam"]'
),
(
    'Sahrai Hotel Fes',
    'Contemporary luxury hotel with stunning views of the Fes Medina and Atlas Mountains.',
    'Fes',
    'Dhar El Mehraz, 30000 Fes, Morocco',
    'https://images.unsplash.com/photo-1518684079-3c830dcef090?q=80&w=1200',
    '["Infinity Pool", "Givenchy Spa", "Rooftop Bar", "Fitness Center", "Tennis Court"]'
),
(
    'Movenpick Hotel Casablanca',
    'Modern luxury in the heart of Casablancas business district with stunning city views.',
    'Casablanca',
    'Corner of Avenue Hassan II, 20070 Casablanca, Morocco',
    'https://images.unsplash.com/photo-1544124499-58912cbddaad?q=80&w=1200',
    '["Rooftop Pool", "Spa", "Business Center", "Multiple Restaurants", "Fitness Center"]'
),
(
    'Hyatt Regency Casablanca',
    'Elegant hotel in the heart of Casablanca, offering views of the Hassan II Mosque and the Atlantic Ocean.',
    'Casablanca',
    'Place des Nations Unies, 20000 Casablanca, Morocco',
    'https://images.unsplash.com/photo-1535827841776-24afc1e255ac?q=80&w=1200',
    '["Swimming Pool", "Spa", "Multiple Restaurants", "Casino", "Business Center"]'
),
(
    'Atlas Sky Hotel Tangier',
    'Modern hotel with panoramic views of the Strait of Gibraltar and the Mediterranean Sea.',
    'Tangier',
    'Route de Malabata, 90000 Tangier, Morocco',
    'https://images.unsplash.com/photo-1622392079468-13d13d66b426?q=80&w=1200',
    '["Infinity Pool", "Beach Access", "Spa", "Multiple Restaurants", "Fitness Center"]'
),
(
    'Hilton Garden Inn Tanger City Center',
    'Contemporary hotel in Tangiers business district with modern amenities.',
    'Tangier',
    'Place du Maghreb Arabe, 90000 Tangier, Morocco',
    'https://images.unsplash.com/photo-1564501049412-61c2a3083791?q=80&w=1200',
    '["Business Center", "Restaurant", "Fitness Center", "Meeting Rooms"]'
),
(
    'Le Medina Essaouira Hotel Thalassa Sea & Spa',
    'Beachfront resort combining traditional Moroccan style with modern comfort.',
    'Essaouira',
    'Avenue Mohamed V, 44000 Essaouira, Morocco',
    'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=1200',
    '["Private Beach", "Thalassotherapy", "Pool", "Tennis Courts", "Water Sports"]'
);

-- Insert Rooms for La Mamounia
INSERT INTO rooms (hotel_id, name, type, price, description, image, capacity, size, view_type, amenities) VALUES
(
    (SELECT id FROM hotels WHERE name = 'La Mamounia Marrakech' LIMIT 1),
    'Classic Hivernage Room',
    'Classic',
    450.00,
    'Elegant room with traditional Moroccan decor and modern amenities.',
    'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?q=80&w=1200',
    2,
    35,
    'Garden View',
    '["King Bed", "Air Conditioning", "Mini Bar", "Safe", "Free WiFi"]'
),
(
    (SELECT id FROM hotels WHERE name = 'La Mamounia Marrakech' LIMIT 1),
    'Deluxe Koutoubia Room',
    'Deluxe',
    650.00,
    'Spacious room with views of the Koutoubia Mosque and Atlas Mountains.',
    'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=1200',
    2,
    45,
    'City View',
    '["King Bed", "Sitting Area", "Marble Bathroom", "Balcony", "Butler Service"]'
);

-- Insert Rooms for Royal Mansour
INSERT INTO rooms (hotel_id, name, type, price, description, image, capacity, size, view_type, amenities) VALUES
(
    (SELECT id FROM hotels WHERE name = 'Royal Mansour Marrakech' LIMIT 1),
    'Superior Riad',
    'Riad',
    1200.00,
    'Three-story private riad with rooftop terrace and plunge pool.',
    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=1200',
    4,
    140,
    'Medina View',
    '["Private Pool", "Terrace", "Butler Service", "Living Room", "Dining Room"]'
),
(
    (SELECT id FROM hotels WHERE name = 'Royal Mansour Marrakech' LIMIT 1),
    'Premier Riad',
    'Riad',
    1800.00,
    'Luxurious riad with private garden and traditional Moroccan architecture.',
    'https://images.unsplash.com/photo-1590490359683-658d3d23f972?q=80&w=1200',
    6,
    175,
    'Garden View',
    '["Private Garden", "Multiple Bedrooms", "Private Kitchen", "Personal Butler", "Hammam"]'
);

-- Insert Rooms for Four Seasons
INSERT INTO rooms (hotel_id, name, type, price, description, image, capacity, size, view_type, amenities) VALUES
(
    (SELECT id FROM hotels WHERE name = 'Four Seasons Resort Marrakech' LIMIT 1),
    'Garden View Room',
    'Deluxe',
    550.00,
    'Elegant room overlooking the resorts lush gardens.',
    'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?q=80&w=1200',
    2,
    42,
    'Garden View',
    '["King Bed", "Private Terrace", "Marble Bathroom", "Mini Bar"]'
),
(
    (SELECT id FROM hotels WHERE name = 'Four Seasons Resort Marrakech' LIMIT 1),
    'Pool View Suite',
    'Suite',
    850.00,
    'Spacious suite with views of the resort pool and Atlas Mountains.',
    'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=1200',
    3,
    65,
    'Pool View',
    '["Living Room", "Private Balcony", "Walk-in Closet", "Soaking Tub"]'
);

-- Insert Rooms for Mazagan
INSERT INTO rooms (hotel_id, name, type, price, description, image, capacity, size, view_type, amenities) VALUES
(
    (SELECT id FROM hotels WHERE name = 'Mazagan Beach Resort' LIMIT 1),
    'Ocean View Room',
    'Deluxe',
    350.00,
    'Modern room with stunning views of the Atlantic Ocean.',
    'https://images.unsplash.com/photo-1582719508461-905c673771fd?q=80&w=1200',
    2,
    42,
    'Ocean View',
    '["King Bed", "Private Balcony", "Rain Shower", "Mini Bar", "Sea View"]'
),
(
    (SELECT id FROM hotels WHERE name = 'Mazagan Beach Resort' LIMIT 1),
    'Pool Suite',
    'Suite',
    580.00,
    'Luxurious suite with direct access to a private pool area.',
    'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?q=80&w=1200',
    3,
    65,
    'Pool and Ocean View',
    '["Private Pool Access", "Separate Living Area", "Walk-in Closet", "Premium Amenities", "Butler Service"]'
);

-- Insert Rooms for Fairmont Royal Palm
INSERT INTO rooms (hotel_id, name, type, price, description, image, capacity, size, view_type, amenities) VALUES
(
    (SELECT id FROM hotels WHERE name = 'Fairmont Royal Palm Marrakech' LIMIT 1),
    'Atlas Suite',
    'Suite',
    750.00,
    'Luxurious suite with panoramic views of the Atlas Mountains.',
    'https://images.unsplash.com/photo-1590490359683-658d3d23f972?q=80&w=1200',
    2,
    72,
    'Mountain View',
    '["King Bed", "Living Room", "Private Terrace", "Butler Service"]'
),
(
    (SELECT id FROM hotels WHERE name = 'Fairmont Royal Palm Marrakech' LIMIT 1),
    'Presidential Suite',
    'Suite',
    2500.00,
    'Ultimate luxury with private pool and garden.',
    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=1200',
    4,
    280,
    'Golf Course View',
    '["Private Pool", "Kitchen", "Dining Room", "Butler Service", "Private Garden"]'
);

-- Insert Rooms for Sofitel Agadir
INSERT INTO rooms (hotel_id, name, type, price, description, image, capacity, size, view_type, amenities) VALUES
(
    (SELECT id FROM hotels WHERE name = 'Sofitel Agadir Royal Bay Resort' LIMIT 1),
    'Ocean Deluxe Room',
    'Deluxe',
    280.00,
    'Elegant room with direct ocean views.',
    'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?q=80&w=1200',
    2,
    45,
    'Ocean View',
    '["King Bed", "Balcony", "Mini Bar", "Rain Shower"]'
),
(
    (SELECT id FROM hotels WHERE name = 'Sofitel Agadir Royal Bay Resort' LIMIT 1),
    'Royal Suite',
    'Suite',
    680.00,
    'Luxurious beachfront suite with premium amenities.',
    'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=1200',
    3,
    90,
    'Ocean Front',
    '["Living Room", "Private Terrace", "Dining Area", "Butler Service"]'
);

-- Insert Rooms for Mandarin Oriental
INSERT INTO rooms (hotel_id, name, type, price, description, image, capacity, size, view_type, amenities) VALUES
(
    (SELECT id FROM hotels WHERE name = 'Mandarin Oriental Marrakech' LIMIT 1),
    'Atlas View Villa',
    'Villa',
    1500.00,
    'Private villa with stunning views of the Atlas Mountains.',
    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=1200',
    4,
    200,
    'Mountain View',
    '["Private Pool", "Garden", "Kitchen", "Butler Service"]'
),
(
    (SELECT id FROM hotels WHERE name = 'Mandarin Oriental Marrakech' LIMIT 1),
    'Royal Penthouse',
    'Penthouse',
    3000.00,
    'Ultimate luxury penthouse with panoramic views.',
    'https://images.unsplash.com/photo-1590490359683-658d3d23f972?q=80&w=1200',
    6,
    400,
    'Panoramic View',
    '["Rooftop Pool", "Private Spa", "Chef Kitchen", "Multiple Terraces"]'
);

-- Insert rooms for Palais Faraj
INSERT INTO rooms (hotel_id, name, type, price, description, image, capacity, size, view_type, amenities) VALUES
(
    (SELECT id FROM hotels WHERE name = 'Palais Faraj Suites & Spa' LIMIT 1),
    'Royal Suite',
    'Suite',
    800.00,
    'Luxurious suite with traditional Moroccan decor and modern amenities.',
    'https://images.unsplash.com/photo-1560448075-bb485b067938?q=80&w=1200',
    2,
    85,
    'Medina View',
    '["King Bed", "Private Terrace", "Living Room", "Mini Bar", "Butler Service"]'
),
(
    (SELECT id FROM hotels WHERE name = 'Palais Faraj Suites & Spa' LIMIT 1),
    'Ambassador Suite',
    'Suite',
    600.00,
    'Elegant suite with panoramic views of the old Medina.',
    'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?q=80&w=1200',
    2,
    65,
    'City View',
    '["King Bed", "Sitting Area", "Marble Bathroom", "Mini Bar"]'
);

-- Insert rooms for Sahrai Hotel
INSERT INTO rooms (hotel_id, name, type, price, description, image, capacity, size, view_type, amenities) VALUES
(
    (SELECT id FROM hotels WHERE name = 'Sahrai Hotel Fes' LIMIT 1),
    'Deluxe Atlas View',
    'Deluxe',
    450.00,
    'Modern room with stunning views of the Atlas Mountains.',
    'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?q=80&w=1200',
    2,
    45,
    'Mountain View',
    '["King Bed", "Private Balcony", "Rain Shower", "Mini Bar"]'
),
(
    (SELECT id FROM hotels WHERE name = 'Sahrai Hotel Fes' LIMIT 1),
    'Junior Suite',
    'Suite',
    650.00,
    'Spacious suite with separate living area and premium amenities.',
    'https://images.unsplash.com/photo-1609949279531-cf48d64bed89?q=80&w=1200',
    3,
    65,
    'Pool View',
    '["King Bed", "Living Room", "Luxury Bathroom", "Private Terrace"]'
);

-- Insert rooms for Movenpick Casablanca
INSERT INTO rooms (hotel_id, name, type, price, description, image, capacity, size, view_type, amenities) VALUES
(
    (SELECT id FROM hotels WHERE name = 'Movenpick Hotel Casablanca' LIMIT 1),
    'Executive Room',
    'Executive',
    380.00,
    'Modern room with city views and executive lounge access.',
    'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=1200',
    2,
    40,
    'City View',
    '["King Bed", "Executive Lounge", "Work Desk", "Mini Bar"]'
),
(
    (SELECT id FROM hotels WHERE name = 'Movenpick Hotel Casablanca' LIMIT 1),
    'Premium Suite',
    'Suite',
    580.00,
    'Luxurious suite with panoramic city views.',
    'https://images.unsplash.com/photo-1591088398332-8a7791972843?q=80&w=1200',
    3,
    75,
    'City View',
    '["Living Room", "King Bed", "Executive Benefits", "Bathtub"]'
);

-- Insert rooms for Atlas Sky Tangier
INSERT INTO rooms (hotel_id, name, type, price, description, image, capacity, size, view_type, amenities) VALUES
(
    (SELECT id FROM hotels WHERE name = 'Atlas Sky Hotel Tangier' LIMIT 1),
    'Mediterranean View Room',
    'Deluxe',
    320.00,
    'Bright room with stunning views of the Mediterranean Sea.',
    'https://images.unsplash.com/photo-1598928636135-d146006ff4be?q=80&w=1200',
    2,
    35,
    'Sea View',
    '["Queen Bed", "Balcony", "Mini Bar", "Work Desk"]'
),
(
    (SELECT id FROM hotels WHERE name = 'Atlas Sky Hotel Tangier' LIMIT 1),
    'Panoramic Suite',
    'Suite',
    520.00,
    'Luxurious suite with wraparound views of the sea and city.',
    'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=1200',
    4,
    80,
    'Sea and City View',
    '["Two Bedrooms", "Living Room", "Private Terrace", "Mini Bar"]'
);

-- Insert rooms for Le Medina Essaouira
INSERT INTO rooms (hotel_id, name, type, price, description, image, capacity, size, view_type, amenities) VALUES
(
    (SELECT id FROM hotels WHERE name = 'Le Medina Essaouira Hotel Thalassa Sea & Spa' LIMIT 1),
    'Ocean View Room',
    'Deluxe',
    280.00,
    'Comfortable room with direct ocean views.',
    'https://images.unsplash.com/photo-1615874959474-d609969a20ed?q=80&w=1200',
    2,
    32,
    'Ocean View',
    '["Queen Bed", "Balcony", "Mini Bar", "Safe"]'
),
(
    (SELECT id FROM hotels WHERE name = 'Le Medina Essaouira Hotel Thalassa Sea & Spa' LIMIT 1),
    'Beach Suite',
    'Suite',
    480.00,
    'Spacious suite with private terrace and beach access.',
    'https://images.unsplash.com/photo-1602002418082-a4443e081dd1?q=80&w=1200',
    3,
    70,
    'Ocean Front',
    '["King Bed", "Living Area", "Private Terrace", "Direct Beach Access"]'
);
