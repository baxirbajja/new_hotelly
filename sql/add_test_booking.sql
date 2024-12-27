-- Insert a test booking (replace user_id and room_id with actual values)
INSERT INTO bookings (room_id, user_id, check_in, check_out, total_price, status)
SELECT 
    (SELECT id FROM rooms LIMIT 1) as room_id,
    (SELECT id FROM users LIMIT 1) as user_id,
    CURDATE() as check_in,
    DATE_ADD(CURDATE(), INTERVAL 3 DAY) as check_out,
    299.99 as total_price,
    'pending' as status;
