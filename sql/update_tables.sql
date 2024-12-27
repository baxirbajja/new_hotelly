-- Drop tables in correct order (child tables first)
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS bookings;

CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    guests INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    payment_id VARCHAR(255) DEFAULT NULL,
    payment_method VARCHAR(50) DEFAULT NULL,
    payment_date DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Create reviews table
CREATE TABLE reviews (
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
);

-- Create payments table
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    payment_id VARCHAR(255) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);
