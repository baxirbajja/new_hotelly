ALTER TABLE reviews
ADD COLUMN status ENUM('pending', 'approved', 'hidden') NOT NULL DEFAULT 'pending';
