<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $book_id = intval($input['book_id']);
    $user_id = $_SESSION['user_id'];

    // Check if the book is already in the cart
    $query = "SELECT * FROM cart_items WHERE user_id = ? AND book_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $user_id, $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If the item already exists, update the quantity
        $query = "UPDATE cart_items SET quantity = quantity + 1 WHERE user_id = ? AND book_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $user_id, $book_id);
        $stmt->execute();
    } else {
        // If the item does not exist, insert a new record
        $query = "INSERT INTO cart_items (user_id, book_id, quantity) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $user_id, $book_id);
        $stmt->execute();
    }

    // Update session cart
    if (isset($_SESSION['cart'][$book_id])) {
        $_SESSION['cart'][$book_id]++;
    } else {
        $_SESSION['cart'][$book_id] = 1;
    }

    $cartCount = array_sum($_SESSION['cart']);

    echo json_encode(['success' => true, 'cartCount' => $cartCount]);
    exit;
}

echo json_encode(['success' => false]);
