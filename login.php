<?php
session_start();
include 'config.php';

// Initialize error messages
$usernameError = $passwordError = $generalError = "";
$username = ""; // Initialize username as an empty string

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Validate username input
    if (empty($username)) {
        $usernameError = "Username is required.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $usernameError = "Invalid username format.";
    }

    // Validate password input
    if (empty($password)) {
        $passwordError = "Password is required.";
    }

    // Proceed only if there are no validation errors
    if (empty($usernameError) && empty($passwordError)) {
        // Authenticate user
        $query = "SELECT id, password FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($user_id, $hashedPassword);
                $stmt->fetch();

                if (password_verify($password, $hashedPassword)) {
                    // Password is correct, start session
                    $_SESSION['user_id'] = $user_id;
                    session_regenerate_id(true); // Regenerate session ID to prevent fixation
                    header("Location: cart.php");
                    exit();
                } else {
                    $generalError = "Incorrect password.";
                }
            } else {
                $generalError = "Username not found.";
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
    <title>Login - Priceless Pages</title>
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
        <form method="post" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" placeholder="Enter your username">
                <span class="error"><?php echo $usernameError; ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <span class="error"><?php echo $passwordError; ?></span>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
     <footer class="footer">
        <?php include 'footer.php'; ?>
    </footer>
    
</body>
</html>
