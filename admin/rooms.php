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
                    'type' => $_POST['type'],
                    'description' => $_POST['description'],
                    'price' => $_POST['price'],
                    'capacity' => $_POST['capacity'],
                    'size' => $_POST['size'],
                    'view_type' => $_POST['view_type'],
                    'amenities' => json_encode(explode(',', $_POST['amenities'])),
                    'hotel_id' => $_POST['hotel_id']
                ];

                if ($_POST['image_type'] === 'url') {
                    $data['image'] = $_POST['image_url'];
                } else {
                    $image = $_FILES['image_upload'];
                    $data['image'] = uploadImage($image);
                }

                if (addRoom($data)) {
                    $_SESSION['success'] = "Room added successfully!";
                } else {
                    $_SESSION['error'] = "Error adding room.";
                }
                break;

            case 'edit':
                $data = [
                    'name' => $_POST['name'],
                    'type' => $_POST['type'],
                    'description' => $_POST['description'],
                    'price' => $_POST['price'],
                    'capacity' => $_POST['capacity'],
                    'size' => $_POST['size'],
                    'view_type' => $_POST['view_type'],
                    'amenities' => json_encode(explode(',', $_POST['amenities'])),
                    'hotel_id' => $_POST['hotel_id']
                ];

                if ($_POST['image_type'] === 'url') {
                    $data['image'] = $_POST['image_url'];
                } else {
                    $image = $_FILES['image_upload'];
                    $data['image'] = uploadImage($image);
                }

                if (updateRoom($_POST['room_id'], $data)) {
                    $_SESSION['success'] = "Room updated successfully!";
                } else {
                    $_SESSION['error'] = "Error updating room.";
                }
                break;

            case 'delete':
                if (deleteRoom($_POST['room_id'])) {
                    $_SESSION['success'] = "Room deleted successfully!";
                } else {
                    $_SESSION['error'] = "Cannot delete room with existing bookings.";
                }
                break;
        }
        header('Location: rooms.php');
        exit;
    }
}

// Get all rooms
$rooms = getAllRooms();
$hotels = getAllHotels();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management - Hotelly Admin</title>
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
        .form-group textarea,
        .form-group select {
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
        .admin-table-image {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .admin-table th {
            background: var(--secondary-color);
            color: white;
            padding: 12px;
            text-align: left;
        }
        .admin-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .admin-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        .admin-btn-primary {
            background: var(--primary-color);
            color: var(--secondary-color);
        }
        .admin-btn-danger {
            background: #dc3545;
            color: white;
        }
        .admin-btn:hover {
            opacity: 0.9;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table-responsive {
            overflow-x: auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
            <a href="hotels.php" class="nav-link">Hotels</a>
            <a href="rooms.php" class="nav-link active">Rooms</a>
            <a href="bookings.php" class="nav-link">Bookings</a>
            <a href="users.php" class="nav-link">Users</a>
            <a href="reviews.php" class="nav-link">Reviews</a>
            <a href="../includes/logout.php" class="nav-link">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">Room Management</h1>
            <button class="admin-btn admin-btn-primary" onclick="showAddRoomModal()">Add New Room</button>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Rooms Table -->
        <div class="table-responsive" data-aos="fade-up">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Capacity</th>
                        <th>Size</th>
                        <th>View</th>
                        <th>Hotel</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" class="admin-table-image"></td>
                            <td><?php echo htmlspecialchars($room['name']); ?></td>
                            <td><?php echo htmlspecialchars($room['type']); ?></td>
                            <td>$<?php echo number_format($room['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($room['capacity']); ?> guests</td>
                            <td><?php echo htmlspecialchars($room['size']); ?> m²</td>
                            <td><?php echo htmlspecialchars($room['view_type']); ?></td>
                            <td><?php echo htmlspecialchars($room['hotel_name']); ?> - <?php echo htmlspecialchars($room['hotel_city']); ?></td>
                            <td>
                                <button class="admin-btn admin-btn-primary" onclick="showEditRoomModal(<?php echo htmlspecialchars(json_encode($room)); ?>)">Edit</button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this room?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                                    <button type="submit" class="admin-btn admin-btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Room Modal -->
    <div id="addRoomModal" class="modal">
        <div class="modal-content">
            <h2>Add New Room</h2>
            <form method="POST" class="admin-form" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">

                <div class="form-group">
                    <label>Room Name</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>Hotel</label>
                    <select name="hotel_id" required>
                        <?php foreach ($hotels as $hotel): ?>
                            <option value="<?php echo $hotel['id']; ?>"><?php echo htmlspecialchars($hotel['name']); ?> - <?php echo htmlspecialchars($hotel['city']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required></textarea>
                </div>

                <div class="form-group">
                    <label>Price per Night</label>
                    <input type="number" name="price" min="0" step="0.01" required>
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
                    <label>Capacity</label>
                    <input type="number" name="capacity" min="1" required>
                </div>

                <div class="form-group">
                    <label>Size</label>
                    <input type="number" name="size" required>
                </div>

                <div class="form-group">
                    <label>View Type</label>
                    <input type="text" name="view_type" required>
                </div>

                <div class="form-group">
                    <label>Amenities (comma-separated)</label>
                    <input type="text" name="amenities" required>
                </div>

                <div class="form-buttons">
                    <button type="button" class="admin-btn" onclick="hideAddRoomModal()">Cancel</button>
                    <button type="submit" class="admin-btn admin-btn-primary">Add Room</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Room Modal -->
    <div id="editRoomModal" class="modal">
        <div class="modal-content">
            <h2>Edit Room</h2>
            <form method="POST" class="admin-form" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="room_id" id="edit_room_id">

                <div class="form-group">
                    <label>Room Name</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>

                <div class="form-group">
                    <label>Hotel</label>
                    <select id="edit_hotel" name="hotel_id" required>
                        <?php foreach ($hotels as $hotel): ?>
                            <option value="<?php echo $hotel['id']; ?>"><?php echo htmlspecialchars($hotel['name']); ?> - <?php echo htmlspecialchars($hotel['city']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea id="edit_description" name="description" required></textarea>
                </div>

                <div class="form-group">
                    <label>Price per Night</label>
                    <input type="number" id="edit_price" name="price" min="0" step="0.01" required>
                </div>

                <div class="form-group">
                    <label>Image</label>
                    <div class="image-input-group">
                        <div class="input-option">
                            <input type="radio" name="image_type" value="url" id="edit_url_option" checked>
                            <label for="edit_url_option">Image URL</label>
                            <input type="url" id="edit_image_url" name="image_url" class="image-input" placeholder="Enter image URL">
                        </div>
                        <div class="input-option">
                            <input type="radio" name="image_type" value="upload" id="edit_upload_option">
                            <label for="edit_upload_option">Upload Image</label>
                            <input type="file" id="edit_image_upload" name="image_upload" class="image-input" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Capacity</label>
                    <input type="number" id="edit_capacity" name="capacity" min="1" required>
                </div>

                <div class="form-group">
                    <label>Size</label>
                    <input type="number" id="edit_size" name="size" required>
                </div>

                <div class="form-group">
                    <label>View Type</label>
                    <input type="text" id="edit_view_type" name="view_type" required>
                </div>

                <div class="form-group">
                    <label>Amenities (comma-separated)</label>
                    <input type="text" id="edit_amenities" name="amenities" required>
                </div>

                <div class="form-buttons">
                    <button type="button" class="admin-btn" onclick="hideEditRoomModal()">Cancel</button>
                    <button type="submit" class="admin-btn admin-btn-primary">Update Room</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();

        // Modal functions
        function showAddRoomModal() {
            document.getElementById('addRoomModal').style.display = 'block';
        }

        function hideAddRoomModal() {
            document.getElementById('addRoomModal').style.display = 'none';
        }

        function showEditRoomModal(room) {
            document.getElementById('edit_room_id').value = room.id;
            document.getElementById('edit_name').value = room.name;
            document.getElementById('edit_hotel').value = room.hotel_id;
            document.getElementById('edit_description').value = room.description;
            document.getElementById('edit_price').value = room.price;
            document.getElementById('edit_capacity').value = room.capacity;
            document.getElementById('edit_size').value = room.size;
            document.getElementById('edit_view_type').value = room.view_type;
            document.getElementById('edit_amenities').value = JSON.parse(room.amenities).join(',');
            document.getElementById('edit_image_url').value = room.image;
            
            document.getElementById('editRoomModal').style.display = 'block';
        }

        function hideEditRoomModal() {
            document.getElementById('editRoomModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
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
        document.querySelector('input[name="image_type"]:checked').dispatchEvent(new Event('change'));
    </script>
</body>
</html>
