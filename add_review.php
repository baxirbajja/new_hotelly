<?php
require_once 'includes/functions.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$booking_id = $_GET['booking_id'] ?? null;
if (!$booking_id) {
    header('Location: bookings.php');
    exit;
}

// Verify booking belongs to user and is confirmed
$sql = "SELECT b.*, r.name as room_name 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        WHERE b.id = ? AND b.user_id = ? AND b.status = 'confirmed'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    header('Location: bookings.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = $_POST['rating'] ?? 0;
    $comment = $_POST['comment'] ?? '';

    if ($rating < 1 || $rating > 5) {
        $error = 'Please select a rating between 1 and 5';
    } else {
        if (createReview($booking_id, $rating, $comment)) {
            $success = 'Review submitted successfully';
        } else {
            $error = 'Failed to submit review';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Review - Hotelly</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }
        .rating input {
            display: none;
        }
        .rating label {
            cursor: pointer;
            font-size: 30px;
            color: #ddd;
            padding: 5px;
        }
        .rating label:before {
            content: '★';
        }
        .rating input:checked ~ label {
            color: #ffd700;
        }
        .rating label:hover,
        .rating label:hover ~ label {
            color: #ffd700;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1>Hotelly</h1>
            </div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="rooms.php">Rooms</a></li>
                <li><a href="bookings.php">My Bookings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="review-form">
            <h2>Add Review for <?php echo htmlspecialchars($booking['room_name']); ?></h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success">
                    <?php echo htmlspecialchars($success); ?>
                    <p><a href="bookings.php">Return to My Bookings</a></p>
                </div>
            <?php else: ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Rating:</label>
                        <div class="rating">
                            <input type="radio" name="rating" value="5" id="star5">
                            <label for="star5"></label>
                            <input type="radio" name="rating" value="4" id="star4">
                            <label for="star4"></label>
                            <input type="radio" name="rating" value="3" id="star3">
                            <label for="star3"></label>
                            <input type="radio" name="rating" value="2" id="star2">
                            <label for="star2"></label>
                            <input type="radio" name="rating" value="1" id="star1">
                            <label for="star1"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="comment">Comment:</label>
                        <textarea id="comment" name="comment" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn">Submit Review</button>
                </form>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Hotelly. All rights reserved.</p>
    </footer>
</body>
</html>
