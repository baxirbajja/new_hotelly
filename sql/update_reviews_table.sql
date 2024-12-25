-- First check if the status column exists
SET @dbname = 'hotelly';
SET @tablename = 'reviews';
SET @columnname = 'status';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT "Column already exists."',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN status ENUM("pending", "approved", "hidden") NOT NULL DEFAULT "pending";')
));

-- Create the reviews table if it doesn't exist
CREATE TABLE IF NOT EXISTS reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT,
    user_id INT,
    room_id INT,
    rating INT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'hidden') NOT NULL DEFAULT 'pending',
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Execute the prepared statement to add the status column if it doesn't exist
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Update existing reviews to have a default status if they don't have one
UPDATE reviews SET status = 'pending' WHERE status IS NULL;
