<?php
require_once '../includes/functions.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Handle review status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $review_id = $_POST['review_id'];
    $status = $_POST['status'];
    
    if (updateReviewStatus($review_id, $status)) {
        $_SESSION['success'] = "Review status updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating review status.";
    }
    header('Location: reviews.php');
    exit;
}

// Get all reviews
$reviews = getAllReviews();

// Set default status for any reviews that don't have one
foreach ($reviews as &$review) {
    if (!isset($review['status'])) {
        $review['status'] = 'pending';
    }
}
unset($review); // Break the reference
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Management - Hotelly Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .admin-nav {
            background: var(--secondary-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }
        .admin-nav-left {
            display: flex;
            align-items: center;
        }
        .admin-nav .logo {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            font-family: 'Playfair Display', serif;
            transition: color 0.3s;
        }
        .admin-nav .logo:hover {
            color: #fff;
        }
        .admin-nav-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .admin-nav-right .nav-link {
            color: #fff;
            text-decoration: none;
            margin-left: 2rem;
            font-family: 'Montserrat', sans-serif;
            transition: color 0.3s;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .admin-nav-right .nav-link:hover,
        .admin-nav-right .nav-link.active {
            color: var(--primary-color);
        }
        .admin-container {
            padding: 6rem 2rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <!-- Admin Navigation -->
    <nav class="admin-nav">
        <div class="admin-nav-left">
            <a href="index.php" class="logo">HOTELLY ADMIN</a>
        </div>
        <div class="admin-nav-right">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="hotels.php" class="nav-link">Hotels</a>
            <a href="rooms.php" class="nav-link">Rooms</a>
            <a href="bookings.php" class="nav-link">Bookings</a>
            <a href="users.php" class="nav-link">Users</a>
            <a href="reviews.php" class="nav-link active">Reviews</a>
            <a href="../includes/logout.php" class="nav-link">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <h1 class="admin-title">Review Management</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Reviews Grid -->
        <div class="reviews-grid" data-aos="fade-up">
            <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-user">
                            <i class="fas fa-user-circle"></i>
                            <span><?php echo htmlspecialchars($review['user_name']); ?></span>
                        </div>
                        <div class="review-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'active' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="review-content">
                        <h3><?php echo htmlspecialchars($review['room_name']); ?></h3>
                        <p><?php echo htmlspecialchars($review['comment']); ?></p>
                    </div>
                    
                    <div class="review-footer">
                        <div class="review-date">
                            <?php echo date('M j, Y', strtotime($review['created_at'])); ?>
                        </div>
                        <div class="review-actions">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                <select name="status" onchange="this.form.submit()" class="status-select">
                                    <option value="approved" <?php echo $review['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                    <option value="pending" <?php echo $review['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="hidden" <?php echo $review['status'] === 'hidden' ? 'selected' : ''; ?>>Hidden</option>
                                </select>
                            </form>
                            <button class="admin-btn admin-btn-primary" onclick="viewReviewDetails(<?php echo htmlspecialchars(json_encode($review)); ?>)">View Details</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Review Details Modal -->
    <div id="reviewDetailsModal" class="modal">
        <div class="modal-content">
            <h2>Review Details</h2>
            <div id="reviewDetails"></div>
            <button class="admin-btn" onclick="hideReviewDetailsModal()">Close</button>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();

        function viewReviewDetails(review) {
            const details = `
                <div class="review-details">
                    <div class="review-section">
                        <h3>Review Information</h3>
                        <p><strong>Guest:</strong> ${review.user_name}</p>
                        <p><strong>Room:</strong> ${review.room_name}</p>
                        <p><strong>Rating:</strong> ${review.rating}/5</p>
                        <p><strong>Status:</strong> ${review.status}</p>
                        <p><strong>Date:</strong> ${new Date(review.created_at).toLocaleString()}</p>
                    </div>
                    
                    <div class="review-section">
                        <h3>Review Content</h3>
                        <p>${review.comment}</p>
                    </div>

                    <div class="review-section">
                        <h3>Moderation Actions</h3>
                        <form method="POST">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="review_id" value="${review.id}">
                            <div class="form-group">
                                <label for="status">Review Status:</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="approved" ${review.status === 'approved' ? 'selected' : ''}>Approved</option>
                                    <option value="pending" ${review.status === 'pending' ? 'selected' : ''}>Pending</option>
                                    <option value="hidden" ${review.status === 'hidden' ? 'selected' : ''}>Hidden</option>
                                </select>
                            </div>
                            <button type="submit" class="admin-btn admin-btn-primary">Update Status</button>
                        </form>
                    </div>
                </div>
            `;
            document.getElementById('reviewDetails').innerHTML = details;
            document.getElementById('reviewDetailsModal').style.display = 'block';
        }

        function hideReviewDetailsModal() {
            document.getElementById('reviewDetailsModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
