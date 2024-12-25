<?php
require_once 'includes/functions.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        // Check if email already exists
        if (getUserByEmail($email)) {
            $error = 'Email already exists';
        } else {
            if (createUser($name, $email, $password)) {
                $_SESSION['registration_success'] = true;
                header('Location: login.php');
                exit;
            } else {
                $error = 'Registration failed';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Hotelly</title>
    <link rel="stylesheet" href="css/style.css">
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
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php" class="active">Register</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="auth-form">
            <h2>Register</h2>
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" action="register.php">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Hotelly. All rights reserved.</p>
    </footer>
</body>
</html>
