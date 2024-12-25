<?php
require_once 'includes/functions.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $total_price = $_POST['total_price'];
    $user_id = $_SESSION['user_id'];

    // Validate dates
    $check_in_date = strtotime($check_in);
    $check_out_date = strtotime($check_out);
    $today = strtotime(date('Y-m-d'));

    if ($check_in_date < $today) {
        $error = "Check-in date cannot be in the past";
    } elseif ($check_out_date <= $check_in_date) {
        $error = "Check-out date must be after check-in date";
    } else {
        if (createBooking($room_id, $user_id, $check_in, $check_out, $total_price)) {
            $_SESSION['success'] = "Booking created successfully!";
            header('Location: bookings.php');
            exit;
        } else {
            $error = "Failed to create booking. Please try again.";
        }
    }
}

// Get room details
$room = null;
if (isset($_GET['room_id'])) {
    $room = getRoomById($_GET['room_id']);
    if (!$room) {
        header('Location: rooms.php');
        exit;
    }
}

// Get booked dates for the room
$booked_dates = [];
if ($room) {
    $bookings = getBookedDates($room['id']);
    foreach ($bookings as $booking) {
        $start = strtotime($booking['check_in']);
        $end = strtotime($booking['check_out']);
        $current = $start;
        
        // Add all dates between check-in and check-out
        while ($current <= $end) {
            $booked_dates[] = date('Y-m-d', $current);
            $current = strtotime('+1 day', $current);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Room - Hotelly</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .booking-form {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        .room-details {
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .error {
            color: #dc3545;
            margin-bottom: 1rem;
            padding: 0.5rem;
            border-radius: 5px;
            background: #ffe6e6;
        }
        .success {
            color: #28a745;
            margin-bottom: 1rem;
            padding: 0.5rem;
            border-radius: 5px;
            background: #e6ffe6;
        }
        .flatpickr-day.disabled {
            color: #ccc;
            background: #f8f9fa;
            text-decoration: line-through;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="booking-form">
            <h2>Book Room</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($room): ?>
                <div class="room-details">
                    <h3><?php echo htmlspecialchars($room['name']); ?></h3>
                    <p>Price per night: $<?php echo number_format($room['price'], 2); ?></p>
                </div>

                <form method="POST" action="" id="bookingForm">
                    <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                    <input type="hidden" name="total_price" id="totalPrice" value="<?php echo $room['price']; ?>">

                    <div class="form-group">
                        <label for="check_in">Check-in Date</label>
                        <input type="text" id="check_in" name="check_in" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="check_out">Check-out Date</label>
                        <input type="text" id="check_out" name="check_out" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Total Price</label>
                        <div id="totalPriceDisplay">$<?php echo number_format($room['price'], 2); ?></div>
                    </div>

                    <button type="submit" class="btn">Book Now</button>
                </form>

                <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                <script>
                    // Convert PHP array to JavaScript array
                    const bookedDates = <?php echo json_encode($booked_dates); ?>;
                    const pricePerNight = <?php echo $room['price']; ?>;

                    // Initialize check-in date picker
                    const checkInPicker = flatpickr("#check_in", {
                        minDate: "today",
                        disable: bookedDates,
                        onChange: function(selectedDates) {
                            // Update check-out date picker min date
                            checkOutPicker.set('minDate', selectedDates[0].fp_incr(1));
                            updateTotalPrice();
                        }
                    });

                    // Initialize check-out date picker
                    const checkOutPicker = flatpickr("#check_out", {
                        minDate: new Date().fp_incr(1),
                        disable: bookedDates,
                        onChange: function() {
                            updateTotalPrice();
                        }
                    });

                    function updateTotalPrice() {
                        const checkIn = checkInPicker.selectedDates[0];
                        const checkOut = checkOutPicker.selectedDates[0];

                        if (checkIn && checkOut && checkOut > checkIn) {
                            const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                            const totalPrice = nights * pricePerNight;
                            document.getElementById('totalPriceDisplay').textContent = '$' + totalPrice.toFixed(2);
                            document.getElementById('totalPrice').value = totalPrice;
                        }
                    }
                </script>
            <?php else: ?>
                <p>Room not found. <a href="rooms.php">View all rooms</a></p>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
