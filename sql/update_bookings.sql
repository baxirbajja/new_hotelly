-- Add payment-related columns to bookings table
ALTER TABLE bookings 
ADD COLUMN status VARCHAR(20) DEFAULT 'pending' AFTER total_price,
ADD COLUMN payment_id VARCHAR(100) DEFAULT NULL AFTER status,
ADD COLUMN payment_method VARCHAR(50) DEFAULT NULL AFTER payment_id;
