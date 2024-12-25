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
                    'image' => $_POST['image']
                ];
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
                    'image' => $_POST['image']
                ];
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management - Hotelly Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <!-- Admin Navigation -->
    <nav class="admin-nav">
        <div class="admin-nav-left">
            <a href="index.php" class="logo">HOTELLY ADMIN</a>
        </div>
        <div class="admin-nav-right">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="rooms.php" class="nav-link active">Rooms</a>
            <a href="bookings.php" class="nav-link">Bookings</a>
            <a href="users.php" class="nav-link">Users</a>
            <a href="reviews.php" class="nav-link">Reviews</a>
            <a href="../logout.php" class="nav-link">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">Room Management</h1>
            <button class="admin-btn admin-btn-primary" onclick="showAddRoomModal()">Add New Room</button>
        </div>

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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" width="100"></td>
                            <td><?php echo htmlspecialchars($room['name']); ?></td>
                            <td><?php echo htmlspecialchars($room['type']); ?></td>
                            <td>$<?php echo number_format($room['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($room['capacity']); ?> guests</td>
                            <td><?php echo htmlspecialchars($room['size']); ?> m²</td>
                            <td><?php echo htmlspecialchars($room['view_type']); ?></td>
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
            <form method="POST" class="admin-form">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="name">Room Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="type">Room Type</label>
                    <select id="type" name="type" class="form-control" required>
                        <option value="Standard">Standard</option>
                        <option value="Deluxe">Deluxe</option>
                        <option value="Suite">Suite</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label for="price">Price per Night</label>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="capacity">Capacity (Guests)</label>
                    <input type="number" id="capacity" name="capacity" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="size">Size (m²)</label>
                    <input type="number" id="size" name="size" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="view_type">View Type</label>
                    <input type="text" id="view_type" name="view_type" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="amenities">Amenities (comma-separated)</label>
                    <input type="text" id="amenities" name="amenities" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="image">Image URL</label>
                    <input type="url" id="image" name="image" class="form-control" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="admin-btn admin-btn-primary">Add Room</button>
                    <button type="button" class="admin-btn" onclick="hideAddRoomModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Room Modal -->
    <div id="editRoomModal" class="modal">
        <div class="modal-content">
            <h2>Edit Room</h2>
            <form method="POST" class="admin-form">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="room_id" id="edit_room_id">
                
                <div class="form-group">
                    <label for="edit_name">Room Name</label>
                    <input type="text" id="edit_name" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit_type">Room Type</label>
                    <select id="edit_type" name="type" class="form-control" required>
                        <option value="Standard">Standard</option>
                        <option value="Deluxe">Deluxe</option>
                        <option value="Suite">Suite</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label for="edit_price">Price per Night</label>
                    <input type="number" id="edit_price" name="price" class="form-control" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="edit_capacity">Capacity (Guests)</label>
                    <input type="number" id="edit_capacity" name="capacity" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit_size">Size (m²)</label>
                    <input type="number" id="edit_size" name="size" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit_view_type">View Type</label>
                    <input type="text" id="edit_view_type" name="view_type" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit_amenities">Amenities (comma-separated)</label>
                    <input type="text" id="edit_amenities" name="amenities" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit_image">Image URL</label>
                    <input type="url" id="edit_image" name="image" class="form-control" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="admin-btn admin-btn-primary">Update Room</button>
                    <button type="button" class="admin-btn" onclick="hideEditRoomModal()">Cancel</button>
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
            document.getElementById('edit_type').value = room.type;
            document.getElementById('edit_description').value = room.description;
            document.getElementById('edit_price').value = room.price;
            document.getElementById('edit_capacity').value = room.capacity;
            document.getElementById('edit_size').value = room.size;
            document.getElementById('edit_view_type').value = room.view_type;
            document.getElementById('edit_amenities').value = JSON.parse(room.amenities).join(',');
            document.getElementById('edit_image').value = room.image;
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
    </script>
</body>
</html>
