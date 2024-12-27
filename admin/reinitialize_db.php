<?php
require_once __DIR__ . '/../includes/functions.php';

// Drop existing tables in reverse order of dependencies
$tables = ['payments', 'reviews', 'bookings', 'rooms', 'hotels', 'users'];

foreach ($tables as $table) {
    $conn->query("DROP TABLE IF EXISTS $table");
}

// Reinitialize database
initializeDatabase();

echo "Database reinitialized successfully!";
?>
