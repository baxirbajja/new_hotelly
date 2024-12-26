<?php
require_once '../includes/functions.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $data = [
                    'name' => $_POST['name'],
                    'city' => $_POST['city'],
                    'address' => $_POST['address'],
                    'description' => $_POST['description'],
                    'rating' => floatval($_POST['rating']),
                    'amenities' => json_encode(explode(',', $_POST['amenities']))
                ];
                if ($_POST['image_type'] === 'url') {
                    $data['image'] = $_POST['image_url'];
                } else {
                    $data['image'] = uploadImage($_FILES['image_upload']);
                }
                if (addHotel($data)) {
                    $_SESSION['success'] = "Hotel added successfully!";
                } else {
                    $_SESSION['error'] = "Error adding hotel.";
                }
                break;

            case 'edit':
                $data = [
                    'name' => $_POST['name'],
                    'city' => $_POST['city'],
                    'address' => $_POST['address'],
                    'description' => $_POST['description'],
                    'rating' => floatval($_POST['rating']),
                    'amenities' => json_encode(explode(',', $_POST['amenities']))
                ];
                if (updateHotel($_POST['hotel_id'], $data)) {
                    $_SESSION['success'] = "Hotel updated successfully!";
                } else {
                    $_SESSION['error'] = "Error updating hotel.";
                }
                break;

            case 'delete':
                if (deleteHotel($_POST['hotel_id'])) {
                    $_SESSION['success'] = "Hotel deleted successfully!";
                } else {
                    $_SESSION['error'] = "Cannot delete hotel that has rooms assigned to it.";
                }
                break;
        }
        header('Location: hotels.php');
        exit;
    }
}

// Get all hotels
$hotels = getAllHotels();

// Get success/error messages
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management - Hotelly Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            overflow-y: auto;
            z-index: 1000;
        }
        .modal-content {
            background: #fff;
            width: 90%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            position: relative;
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group textarea {
            min-height: 100px;
        }
        .form-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .image-input-group {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
        }
        .input-option {
            margin-bottom: 10px;
        }
        .input-option:last-child {
            margin-bottom: 0;
        }
        .image-input {
            width: 100%;
            margin-top: 5px;
        }
        .input-option label {
            margin-left: 5px;
            display: inline-block;
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
            <a href="hotels.php" class="nav-link active">Hotels</a>
            <a href="rooms.php" class="nav-link">Rooms</a>
            <a href="bookings.php" class="nav-link">Bookings</a>
            <a href="users.php" class="nav-link">Users</a>
            <a href="reviews.php" class="nav-link">Reviews</a>
            <a href="../includes/logout.php" class="nav-link">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-content">
                <h1>Manage Hotels</h1>
                <button class="admin-btn" onclick="showAddHotelModal()">Add New Hotel</button>
            </div>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
        </div>

        <div class="admin-content">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>City</th>
                            <th>Rating</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $hotels = getAllHotels();
                        foreach ($hotels as $hotel): ?>
                            <tr>
                                <td><?php echo $hotel['id']; ?></td>
                                <td><?php echo htmlspecialchars($hotel['name']); ?></td>
                                <td><?php echo htmlspecialchars($hotel['city']); ?></td>
                                <td><?php echo $hotel['rating']; ?> ★</td>
                                <td>
                                    <button class="admin-btn" onclick='showEditHotelModal(<?php echo json_encode($hotel); ?>)'>Edit</button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this hotel?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
                                        <button type="submit" class="admin-btn admin-btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Hotel Modal -->
    <div id="addHotelModal" class="modal">
        <div class="modal-content">
            <h2>Add New Hotel</h2>
            <form method="POST" class="admin-form" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label>Hotel Name</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" required>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required></textarea>
                </div>

                <div class="form-group">
                    <label>Image</label>
                    <div class="image-input-group">
                        <div class="input-option">
                            <input type="radio" name="image_type" value="url" id="url_option" checked>
                            <label for="url_option">Image URL</label>
                            <input type="url" name="image_url" class="image-input" placeholder="Enter image URL">
                        </div>
                        <div class="input-option">
                            <input type="radio" name="image_type" value="upload" id="upload_option">
                            <label for="upload_option">Upload Image</label>
                            <input type="file" name="image_upload" class="image-input" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Rating</label>
                    <input type="number" name="rating" min="0" max="5" step="0.1" required>
                </div>

                <div class="form-group">
                    <label>Amenities (comma-separated)</label>
                    <input type="text" name="amenities" required>
                </div>

                <div class="form-buttons">
                    <button type="button" class="admin-btn" onclick="closeModal('addHotelModal')">Cancel</button>
                    <button type="submit" class="admin-btn admin-btn-primary">Add Hotel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Hotel Modal -->
    <div id="editHotelModal" class="modal">
        <div class="modal-content">
            <h2>Edit Hotel</h2>
            <form method="POST" class="admin-form">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="hotel_id" id="edit_hotel_id">
                
                <div class="form-group">
                    <label>Hotel Name</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>

                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" id="edit_city" required>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" id="edit_address" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_description" required></textarea>
                </div>

                <div class="form-group">
                    <label>Image URL</label>
                    <input type="url" name="image" id="edit_image" required>
                </div>

                <div class="form-group">
                    <label>Rating</label>
                    <input type="number" name="rating" id="edit_rating" min="0" max="5" step="0.1" required>
                </div>

                <div class="form-group">
                    <label>Amenities (comma-separated)</label>
                    <input type="text" name="amenities" id="edit_amenities" required>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="admin-btn">Update Hotel</button>
                    <button type="button" class="admin-btn admin-btn-secondary" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();

        // Modal functions
        const addModal = document.getElementById('addHotelModal');
        const editModal = document.getElementById('editHotelModal');

        function showAddHotelModal() {
            addModal.style.display = 'block';
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = 'none';
        }

        function showEditHotelModal(hotel) {
            document.getElementById('edit_hotel_id').value = hotel.id;
            document.getElementById('edit_name').value = hotel.name;
            document.getElementById('edit_city').value = hotel.city;
            document.getElementById('edit_address').value = hotel.address;
            document.getElementById('edit_description').value = hotel.description;
            document.getElementById('edit_image').value = hotel.image;
            document.getElementById('edit_rating').value = hotel.rating;
            
            const amenities = JSON.parse(hotel.amenities);
            document.getElementById('edit_amenities').value = amenities.join(',');
            
            editModal.style.display = 'block';
        }

        function closeEditModal() {
            editModal.style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target == addModal) {
                closeModal('addHotelModal');
            }
            if (event.target == editModal) {
                closeEditModal();
            }
        }

        // Add this to your existing JavaScript
        document.querySelectorAll('input[name="image_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const urlInput = document.querySelector('input[name="image_url"]');
                const fileInput = document.querySelector('input[name="image_upload"]');
                
                if (this.value === 'url') {
                    urlInput.required = true;
                    fileInput.required = false;
                    urlInput.style.display = 'block';
                    fileInput.style.display = 'none';
                } else {
                    urlInput.required = false;
                    fileInput.required = true;
                    urlInput.style.display = 'none';
                    fileInput.style.display = 'block';
                }
            });
        });

        // Trigger change event on page load to set initial state
        document.querySelector('input[name="image_type"]:checked').dispatchEvent(new Event('change'));
    </script>
</body>
</html>
