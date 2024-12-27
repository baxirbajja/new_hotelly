<?php
require_once '../includes/functions.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Get statistics
$stats = [
    'total_bookings' => count(getAllBookings()),
    'active_bookings' => count(array_filter(getAllBookings(), function($booking) {
        return $booking['status'] === 'confirmed';
    })),
    'total_users' => count(getAllUsers()),
    'total_revenue' => array_reduce(getAllBookings(), function($carry, $booking) {
        return $carry + $booking['total_price'];
    }, 0),
    'monthly_labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    'monthly_bookings' => [10, 15, 8, 12, 20, 18],
    'monthly_revenue' => [5000, 7500, 4000, 6000, 10000, 9000],
    'recent_activity' => [
        ['type' => 'Booking', 'description' => 'New booking for Luxury Suite', 'time' => '2 hours ago'],
        ['type' => 'User', 'description' => 'New user registration', 'time' => '5 hours ago']
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hotelly</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Admin Navigation -->
    <nav class="admin-nav">
        <div class="admin-nav-left">
            <a href="index.php" class="logo">HOTELLY ADMIN</a>
        </div>
        <div class="admin-nav-right">
            <a href="index.php" class="nav-link active">Dashboard</a>
            <a href="hotels.php" class="nav-link">Hotels</a>
            <a href="rooms.php" class="nav-link">Rooms</a>
            <a href="bookings.php" class="nav-link">Bookings</a>
            <a href="users.php" class="nav-link">Users</a>
            <a href="../logout.php" class="nav-link">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <h1 class="admin-title">Dashboard Overview</h1>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card" data-aos="fade-up">
                <h3>Total Bookings</h3>
                <p class="stat-number"><?php echo $stats['total_bookings']; ?></p>
                <p class="stat-label">All Time</p>
            </div>

            <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                <h3>Active Bookings</h3>
                <p class="stat-number"><?php echo $stats['active_bookings']; ?></p>
                <p class="stat-label">Current</p>
            </div>

            <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                <h3>Total Revenue</h3>
                <p class="stat-number">$<?php echo number_format($stats['total_revenue'], 2); ?></p>
                <p class="stat-label">All Time</p>
            </div>

            <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
                <h3>Total Users</h3>
                <p class="stat-number"><?php echo $stats['total_users']; ?></p>
                <p class="stat-label">Registered</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <div class="chart-container" data-aos="fade-up">
                <h2>Monthly Bookings</h2>
                <canvas id="bookingsChart"></canvas>
            </div>
            <div class="chart-container" data-aos="fade-up" data-aos-delay="100">
                <h2>Revenue Trend</h2>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="recent-activity" data-aos="fade-up">
            <h2>Recent Activity</h2>
            <div class="activity-list">
                <?php foreach ($stats['recent_activity'] as $activity): ?>
                <div class="activity-item">
                    <span class="activity-type"><?php echo htmlspecialchars($activity['type']); ?></span>
                    <span class="activity-desc"><?php echo htmlspecialchars($activity['description']); ?></span>
                    <span class="activity-time"><?php echo htmlspecialchars($activity['time']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();

        // Bookings Chart
        const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
        new Chart(bookingsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($stats['monthly_labels']); ?>,
                datasets: [{
                    label: 'Monthly Bookings',
                    data: <?php echo json_encode($stats['monthly_bookings']); ?>,
                    borderColor: '#4A90E2',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($stats['monthly_labels']); ?>,
                datasets: [{
                    label: 'Monthly Revenue',
                    data: <?php echo json_encode($stats['monthly_revenue']); ?>,
                    backgroundColor: '#2ECC71'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
</body>
</html>
