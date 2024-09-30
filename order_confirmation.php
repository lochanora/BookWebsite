<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$order_id = $_GET['order_id'];

// Verify order exists
$query = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $order_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h1>Order not found.</h1>";
    exit();
}

$order = $result->fetch_assoc();
$stmt->close();

// Fetch billing address
$query = "SELECT * FROM user_addresses WHERE user_id = ? AND address_type = 'billing'";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$address_result = $stmt->get_result();
$address = $address_result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Order Confirmation - Priceless Pages</title>
    <style>
        .book-card {
            text-decoration: none;
            color: inherit;
        }
        .book-card:hover {
            text-decoration: none;
            color: inherit;
        }
        .book-card img {
            width: 100%;
            height: 150px; /* Smaller height for the images */
            object-fit: cover;
        }
        .book-title {
            font-size: 0.9rem; /* Slightly smaller font size */
            font-weight: bold;
            margin-top: 0.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>

    <div class="container mt-5">
        <h1>Your order was successful!</h1>
        <p>Thank you for your purchase. An email will be sent to you with the details.</p>
       
        <h3>We thought you might like these books:</h3>
        <div class="row">
            <?php
            $query = "SELECT id, title, image FROM books ORDER BY RAND() LIMIT 3";
            $result = $conn->query($query);
            while ($row = $result->fetch_assoc()) {
                $book_id = $row['id'];
                $title = htmlspecialchars($row['title']);
                $image = htmlspecialchars($row['image']);
                echo "
                <div class='col-md-4 mb-4'>
                    <a href='book.php?id=$book_id' class='book-card'>
                        <div class='card'>
                            <img src='images/$image' class='card-img-top' alt='$title'>
                            <div class='card-body'>
                                <p class='book-title'>$title</p>
                            </div>
                        </div>
                    </a>
                </div>
                ";
            }
            ?>
        </div>
        <a href="index.php" class="btn btn-primary mt-3">Continue Shopping</a>
    </div>
    <footer class="footer">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>
