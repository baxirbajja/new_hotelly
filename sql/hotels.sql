-- Create hotels table
CREATE TABLE IF NOT EXISTS hotels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    description TEXT,
    image VARCHAR(255),
    rating DECIMAL(2,1),
    amenities TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add sample Moroccan hotels
INSERT INTO hotels (name, city, address, description, image, rating, amenities) VALUES
('Mamounia Palace', 'Marrakech', 'Avenue Bab Jdid, Marrakech 40040', 'A legendary palace that is part of Moroccan heritage, La Mamounia offers a unique experience combining luxury, gastronomy and authentic traditions.', 'img/hotels/mamounia.jpg', 5.0, 'Spa,Pool,Restaurant,WiFi,Room Service,Fitness Center'),
('Royal Mansour', 'Marrakech', 'Rue Abou Abbas El Sebti, Marrakech 40000', 'Royal Mansour Marrakech offers an exceptional experience in the heart of the historic city, with traditional Moroccan architecture and modern luxury.', 'img/hotels/royal-mansour.jpg', 4.9, 'Spa,Pool,Restaurant,WiFi,Room Service,Fitness Center,Garden'),
('Sofitel Casablanca Tour Blanche', 'Casablanca', 'Rue Sidi Belyout, Casablanca 20000', 'Located in the heart of the business district, Sofitel Casablanca Tour Blanche combines French luxury with Moroccan tradition.', 'img/hotels/sofitel-casa.jpg', 4.7, 'Business Center,Pool,Restaurant,WiFi,Room Service,Fitness Center'),
('Mazagan Beach Resort', 'El Jadida', 'Route de Casablanca, El Jadida 24000', 'A luxury resort along the Atlantic coast offering a perfect blend of entertainment, relaxation and Moroccan hospitality.', 'img/hotels/mazagan.jpg', 4.8, 'Beach,Golf,Casino,Spa,Pool,Restaurant,WiFi'),
('Palais Faraj', 'Fez', 'Bab Ziat, Fez 30000', 'A luxurious palace in the heart of the Fez Medina, offering panoramic views of the old city and authentic Moroccan experiences.', 'img/hotels/palais-faraj.jpg', 4.6, 'Restaurant,WiFi,Room Service,Spa,Terrace,Traditional Architecture'),
('Atlas Medina & Spa', 'Tangier', 'Boulevard Mohamed VI, Tangier 90000', 'Modern comfort meets traditional Moroccan hospitality in this seaside hotel with stunning views of the Mediterranean.', 'img/hotels/atlas-medina.jpg', 4.5, 'Spa,Pool,Restaurant,WiFi,Beach Access,Fitness Center');

-- Modify rooms table to include hotel_id
ALTER TABLE rooms ADD COLUMN hotel_id INT;
ALTER TABLE rooms ADD FOREIGN KEY (hotel_id) REFERENCES hotels(id);

-- Update existing rooms to belong to random hotels (sample data)
UPDATE rooms SET hotel_id = 1 WHERE id % 6 = 0;
UPDATE rooms SET hotel_id = 2 WHERE id % 6 = 1;
UPDATE rooms SET hotel_id = 3 WHERE id % 6 = 2;
UPDATE rooms SET hotel_id = 4 WHERE id % 6 = 3;
UPDATE rooms SET hotel_id = 5 WHERE id % 6 = 4;
UPDATE rooms SET hotel_id = 6 WHERE id % 6 = 5;
