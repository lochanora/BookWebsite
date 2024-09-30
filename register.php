<?php
session_start();
include 'config.php';

// Initialize variables
$usernameError = $emailError = $passwordError = $generalError = "";
$username = ""; // Initialize username as an empty string

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $username = htmlspecialchars(trim($_POST['username']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = htmlspecialchars(trim($_POST['password']));

    // Validate username format
    if (empty($username) || !preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $usernameError = "Username must be 3-20 characters long and contain only letters, numbers, and underscores.";
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format.";
    }

    // Validate password strength
    if (strlen($password) < 8) {
        $passwordError = "Password must be at least 8 characters long.";
    }

    // Proceed only if there are no validation errors
    if (empty($usernameError) && empty($emailError) && empty($passwordError)) {
        // Check if username or email already exists
        $query = "SELECT id FROM users WHERE username = ? OR email = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('ss', $username, $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // If the query returns any rows, the username or email already exists
                $generalError = "Username or email already exists.";
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Insert new user
                $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                if ($stmt = $conn->prepare($query)) {
                    $stmt->bind_param('sss', $username, $email, $hashedPassword);
                    try {
                        $stmt->execute();
                        $user_id = $stmt->insert_id;
                        $_SESSION['user_id'] = $user_id;
                        session_regenerate_id(true); // Regenerate session ID
                        header("Location: index.php");
                        exit();
                    } catch (mysqli_sql_exception $e) {
                        $generalError = "Error executing query: " . $conn->error;
                    }
                } else {
                    $generalError = "Error preparing query: " . $conn->error;
                }
            }
            $stmt->close();
        } else {
            $generalError = "Error preparing query: " . $conn->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Register - Priceless Pages</title>
    <style>
        .error { color: red; }
    </style>
</head>
<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>
    <div class="container">
        <?php if (!empty($generalError)): ?>
            <div class="alert alert-danger"><?php echo $generalError; ?></div>
        <?php endif; ?>
        <form method="post" action="register.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" placeholder="Enter your username">
                <span class="error"><?php echo $usernameError; ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                <span class="error"><?php echo $emailError; ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <span class="error"><?php echo $passwordError; ?></span>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
     <footer class="footer">
        <?php include 'footer.php'; ?>
    </footer>
    
</body>
</html>
