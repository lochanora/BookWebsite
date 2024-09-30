<?php
session_start();
include 'config.php';

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect_to=checkout.php');
    exit();
}

// Initialize error messages array
$error_messages = [
    'billing_first_name' => '',
    'billing_last_name' => '',
    'billing_email' => '',
    'billing_zip' => '',
    'billing_state' => '',
    'billing_country' => '',
    'billing_address1' => '',
    'card_number' => '',
    'card_expiration' => '',
    'card_cvv' => '',
    'shipping_method' => ''
];

// Calculate the total price of the cart items
$total_price = 0.0;

if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $book_id => $item) {
        $quantity = intval($item['quantity']);
        
        // Fetch the price and calculate the item total
        $query = "SELECT price FROM books WHERE id = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('i', $book_id);
            $stmt->execute();
            $stmt->bind_result($price);
            $stmt->fetch();
            $item_total = floatval($price) * $quantity;
            $total_price += $item_total;
            $stmt->close();
        } else {
            die("Error preparing query: " . $conn->error);
        }
    }
}

// Capture POST data from checkout form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }

    // Sanitize and validate inputs
    $user_id = $_SESSION['user_id'];

    // Additional validation for names to prevent SQL injection
    $billing_first_name = filter_var(trim($_POST['billing_first_name']), FILTER_SANITIZE_STRING);
    $billing_last_name = filter_var(trim($_POST['billing_last_name']), FILTER_SANITIZE_STRING);
    $billing_email = filter_var(trim($_POST['billing_email']), FILTER_SANITIZE_EMAIL);
    $billing_address1 = filter_var(trim($_POST['billing_address1']), FILTER_SANITIZE_STRING);
    $billing_address2 = filter_var(trim($_POST['billing_address2']), FILTER_SANITIZE_STRING);
    $billing_country = filter_var(trim($_POST['billing_country']), FILTER_SANITIZE_STRING);
    $billing_state = filter_var(trim($_POST['billing_state']), FILTER_SANITIZE_STRING);
    $billing_zip = filter_var(trim($_POST['billing_zip']), FILTER_SANITIZE_STRING);
    $card_number = filter_var(trim($_POST['card_number']), FILTER_SANITIZE_STRING); // Sanitize but do not store
    $card_expiration = filter_var(trim($_POST['card_expiration']), FILTER_SANITIZE_STRING);
    $card_cvv = filter_var(trim($_POST['card_cvv']), FILTER_SANITIZE_STRING);
    $shipping_method = filter_var(trim($_POST['shipping-option']), FILTER_SANITIZE_STRING);

    // Validate inputs and add errors to array
    if (!preg_match('/^[a-zA-Z\s\-]+$/', $billing_first_name) || strlen($billing_first_name) < 2) {
        $error_messages['billing_first_name'] = "First name must be at least 2 characters long and contain only letters, spaces, or hyphens.";
    }
    if (!preg_match('/^[a-zA-Z\s\-]+$/', $billing_last_name) || strlen($billing_last_name) < 2) {
        $error_messages['billing_last_name'] = "Last name must be at least 2 characters long and contain only letters, spaces, or hyphens.";
    }
    if (!filter_var($billing_email, FILTER_VALIDATE_EMAIL)) {
        $error_messages['billing_email'] = "Invalid email address.";
    }
    if (empty($billing_address1)) {
        $error_messages['billing_address1'] = "Address is required.";
    }
    if (empty($billing_state)) {
        $error_messages['billing_state'] = "State is required.";
    }
    if (empty($billing_country)) {
        $error_messages['billing_country'] = "Country is required.";
    }
    // Allow alphanumeric postal codes with spaces and length of 6 or 7 (e.g., "K1K 1K1")
    if (!preg_match('/^[A-Za-z]\d[A-Za-z] \d[A-Za-z]\d$/', $billing_zip)) {
        $error_messages['billing_zip'] = "Postal code must be in the format K1K 1K1.";
    }
    if (!preg_match('/^\d{16}$/', $card_number)) {
        $error_messages['card_number'] = "Credit card number must be 16 digits long.";
    }
    // Validate expiration date format MM/YY and ensure it's a future date
    if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $card_expiration) || !validate_expiration_date($card_expiration)) {
        $error_messages['card_expiration'] = "Expiration date must be in MM/YY format and must be a future date.";
    }
    if (!preg_match('/^\d{3,4}$/', $card_cvv)) {
        $error_messages['card_cvv'] = "CVV must be 3 or 4 digits long.";
    }
    if (!in_array($shipping_method, ['standard', 'express', 'next_day'])) {
        $error_messages['shipping_method'] = "Invalid shipping method selected.";
    }

    // If no errors, proceed with order processing
    if (array_filter($error_messages) === []) {
        // Calculate shipping cost
        $shipping_cost = 0.0;
        if ($shipping_method == 'standard') {
            $shipping_cost = 0.0;
        } elseif ($shipping_method == 'express') {
            $shipping_cost = 10.0;
        } elseif ($shipping_method == 'next_day') {
            $shipping_cost = 20.0;
        }

        // Calculate total with shipping
        $grand_total = $total_price + $shipping_cost;

        // Insert order into orders table
        $query = "INSERT INTO orders (user_id, total) VALUES (?, ?)";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('id', $user_id, $grand_total);
            $stmt->execute();
            $order_id = $stmt->insert_id;
            $stmt->close();
        } else {
            die("Error preparing query: " . $conn->error);
        }

        // Insert billing address into user_addresses table
        $query = "INSERT INTO user_addresses (user_id, address_type, first_name, last_name, email, address1, address2, country, state, zip) VALUES (?, 'billing', ?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('issssssss', $user_id, $billing_first_name, $billing_last_name, $billing_email, $billing_address1, $billing_address2, $billing_country, $billing_state, $billing_zip);
            $stmt->execute();
            $stmt->close();
        } else {
            die("Error preparing query: " . $conn->error);
        }

        // Insert each item in the order into the order_details table
        foreach ($_SESSION['cart'] as $book_id => $item) {
            $quantity = intval($item['quantity']);
            $query = "SELECT price FROM books WHERE id = ?";
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param('i', $book_id);
                $stmt->execute();
                $stmt->bind_result($price);
                $stmt->fetch();
                $stmt->close();

                $item_total = floatval($price) * $quantity;

                $query = "INSERT INTO order_details (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)";
                if ($stmt = $conn->prepare($query)) {
                    $stmt->bind_param('iiid', $order_id, $book_id, $quantity, $item_total);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    die("Error preparing query: " . $conn->error);
                }
            } else {
                die("Error preparing query: " . $conn->error);
            }
        }

        // Redirect to order confirmation page with order ID
        header("Location: order_confirmation.php?order_id=$order_id");
        exit();
    }
}

/**
 * Validate the expiration date is in MM/YY format and is a future date.
 *
 * @param string $expiration
 * @return bool
 */
function validate_expiration_date($expiration) {
    list($month, $year) = explode('/', $expiration);
    $current_year = date('y');
    $current_month = date('m');

    if ($year > $current_year || ($year == $current_year && $month >= $current_month)) {
        return true;
    }
    return false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Site Metas -->
    <title>Checkout - Priceless Pages</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Site Icons -->
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Site CSS -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="css/responsive.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/custom.css">

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        /* Add your custom styles here */
        .order-box {
            margin-top: 20px;
        }

        .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
        }

        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }

        .btn-black {
            background-color: black;
            color: white;
            border: none;
            padding: 10px 20px;
            text-transform: uppercase;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-black:hover {
            background-color: #333;
        }
    </style>
</head>
<body>

<header>
    <?php include 'navbar.php'; ?>
</header>

<div class="cart-box-main">
    <div class="container">
        <form class="needs-validation" novalidate method="post" action="checkout.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="row">
                <div class="col-sm-6 col-lg-6 mb-3">
                    <div class="checkout-address">
                        <div class="title-left">
                            <h3>Billing address</h3>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="billing_first_name">First name *</label>
                                <input type="text" class="form-control" id="billing_first_name" name="billing_first_name" value="<?php echo htmlspecialchars($billing_first_name ?? ''); ?>" required>
                                <?php if ($error_messages['billing_first_name']): ?>
                                    <div class="error-message"><?php echo $error_messages['billing_first_name']; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="billing_last_name">Last name *</label>
                                <input type="text" class="form-control" id="billing_last_name" name="billing_last_name" value="<?php echo htmlspecialchars($billing_last_name ?? ''); ?>" required>
                                <?php if ($error_messages['billing_last_name']): ?>
                                    <div class="error-message"><?php echo $error_messages['billing_last_name']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="billing_email">Email Address *</label>
                            <input type="email" class="form-control" id="billing_email" name="billing_email" value="<?php echo htmlspecialchars($billing_email ?? ''); ?>" required>
                            <?php if ($error_messages['billing_email']): ?>
                                <div class="error-message"><?php echo $error_messages['billing_email']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="billing_address1">Address *</label>
                            <input type="text" class="form-control" id="billing_address1" name="billing_address1" value="<?php echo htmlspecialchars($billing_address1 ?? ''); ?>" required>
                            <?php if ($error_messages['billing_address1']): ?>
                                <div class="error-message"><?php echo $error_messages['billing_address1']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="billing_address2">Address 2 (optional)</label>
                            <input type="text" class="form-control" id="billing_address2" name="billing_address2" value="<?php echo htmlspecialchars($billing_address2 ?? ''); ?>">
                        </div>
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label for="billing_country">Country *</label>
                                <select class="wide w-100" id="billing_country" name="billing_country" required>
                                    <option value="">Select</option>
                                    <option value="United States" <?php echo ($billing_country ?? '') == 'United States' ? 'selected' : ''; ?>>United States</option>
                                    <option value="Canada" <?php echo ($billing_country ?? '') == 'Canada' ? 'selected' : ''; ?>>Canada</option>
                                    <option value="United Kingdom" <?php echo ($billing_country ?? '') == 'United Kingdom' ? 'selected' : ''; ?>>United Kingdom</option>
                                    <!-- Add all countries here -->
                                </select>
                                <?php if ($error_messages['billing_country']): ?>
                                    <div class="error-message"><?php echo $error_messages['billing_country']; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="billing_state">State *</label>
                                <input type="text" class="form-control" id="billing_state" name="billing_state" value="<?php echo htmlspecialchars($billing_state ?? ''); ?>" required>
                                <?php if ($error_messages['billing_state']): ?>
                                    <div class="error-message"><?php echo $error_messages['billing_state']; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="billing_zip">Postal Code *</label>
                                <input type="text" class="form-control" id="billing_zip" name="billing_zip" value="<?php echo htmlspecialchars($billing_zip ?? ''); ?>" required>
                                <?php if ($error_messages['billing_zip']): ?>
                                    <div class="error-message"><?php echo $error_messages['billing_zip']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr class="mb-4">
                        <div class="title"> <span>Payment</span> </div>
                        <div class="d-block my-3">
                            <div class="custom-control custom-radio">
                                <input id="credit" name="paymentMethod" type="radio" class="custom-control-input" checked required>
                                <label class="custom-control-label" for="credit">Credit card</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="debit" name="paymentMethod" type="radio" class="custom-control-input" required>
                                <label class="custom-control-label" for="debit">Debit card</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="paypal" name="paymentMethod" type="radio" class="custom-control-input" required>
                                <label class="custom-control-label" for="paypal">Paypal</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cc-name">Name on card *</label>
                                <input type="text" class="form-control" id="cc-name" placeholder="Full name as displayed on card" required>
                                <div class="invalid-feedback"> Name on card is required </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="card_number">Credit card number *</label>
                                <input type="text" class="form-control" id="card_number" name="card_number" value="<?php echo htmlspecialchars($card_number ?? ''); ?>" required>
                                <?php if ($error_messages['card_number']): ?>
                                    <div class="error-message"><?php echo $error_messages['card_number']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="card_expiration">Expiration (MM/YY) *</label>
                                <input type="text" class="form-control" id="card_expiration" name="card_expiration" value="<?php echo htmlspecialchars($card_expiration ?? ''); ?>" required>
                                <?php if ($error_messages['card_expiration']): ?>
                                    <div class="error-message"><?php echo $error_messages['card_expiration']; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="card_cvv">CVV *</label>
                                <input type="text" class="form-control" id="card_cvv" name="card_cvv" value="<?php echo htmlspecialchars($card_cvv ?? ''); ?>" required>
                                <?php if ($error_messages['card_cvv']): ?>
                                    <div class="error-message"><?php echo $error_messages['card_cvv']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr class="mb-1">

                        <div class="order-box">
                            <div class="title-left">
                                <h3>Shipping Method</h3>
                            </div>
                            <div class="mb-4">
                                <div class="custom-control custom-radio">
                                    <input id="shippingOption1" name="shipping-option" class="custom-control-input" value="standard" <?php echo ($shipping_method ?? 'standard') == 'standard' ? 'checked' : ''; ?> type="radio">
                                    <label class="custom-control-label" for="shippingOption1">Standard Delivery</label> <span class="float-right font-weight-bold">FREE</span>
                                </div>
                                <div class="ml-4 mb-2 small">(3-7 business days)</div>
                                <div class="custom-control custom-radio">
                                    <input id="shippingOption2" name="shipping-option" class="custom-control-input" value="express" <?php echo ($shipping_method ?? '') == 'express' ? 'checked' : ''; ?> type="radio">
                                    <label class="custom-control-label" for="shippingOption2">Express Delivery</label> <span class="float-right font-weight-bold">$10.00</span>
                                </div>
                                <div class="ml-4 mb-2 small">(2-4 business days)</div>
                                <div class="custom-control custom-radio">
                                    <input id="shippingOption3" name="shipping-option" class="custom-control-input" value="next_day" <?php echo ($shipping_method ?? '') == 'next_day' ? 'checked' : ''; ?> type="radio">
                                    <label class="custom-control-label" for="shippingOption3">Next Business day</label> <span class="float-right font-weight-bold">$20.00</span>
                                </div>
                                <?php if ($error_messages['shipping_method']): ?>
                                    <div class="error-message"><?php echo $error_messages['shipping_method']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-6 mb-3">
                    <div class="order-box">
                        <div class="title-left">
                            <h3>Your order</h3>
                        </div>
                        <div class="d-flex">
                            <div class="font-weight-bold">Sub Total</div>
                            <div class="ml-auto font-weight-bold">$<?php echo number_format($total_price, 2); ?></div>
                        </div>
                        <hr class="my-1">
                        <div class="d-flex">
                            <div class="font-weight-bold">Shipping Cost</div>
                            <div class="ml-auto font-weight-bold" id="shippingCostDisplay">Free</div>
                        </div>
                        <hr>
                        <div class="d-flex gr-total">
                            <h5>Grand Total</h5>
                            <div class="ml-auto h5" id="grandTotalDisplay">$<?php echo number_format($total_price, 2); ?></div>
                        </div>
                        <hr>
                    </div>
                    <input type="hidden" name="total" value="<?php echo number_format($total_price, 2); ?>">
                    <button type="submit" class="btn btn-black mt-4">Place Order</button>
                </div>
            </div>
        </form>
    </div>
</div>

<footer>
    <?php include 'footer.php'; ?>
</footer>

<a href="#" id="back-to-top" title="Back to top" style="display: none;">&uarr;</a>

<script>
    // Update the shipping cost and grand total display
    function updateShippingCost() {
        var shippingCost = 0;
        var selectedShipping = document.querySelector('input[name="shipping-option"]:checked').value;

        if (selectedShipping === 'standard') {
            shippingCost = 0;
        } else if (selectedShipping === 'express') {
            shippingCost = 10;
        } else if (selectedShipping === 'next_day') {
            shippingCost = 20;
        }

        var totalPrice = <?php echo json_encode($total_price); ?>; // Ensure totalPrice is a number
        var grandTotal = totalPrice + shippingCost;

        document.getElementById('shippingCostDisplay').textContent = '$' + shippingCost.toFixed(2);
        document.getElementById('grandTotalDisplay').textContent = '$' + grandTotal.toFixed(2);
        document.querySelector('input[name="total"]').value = grandTotal.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Attach event listener to shipping option changes
        var shippingOptions = document.querySelectorAll('input[name="shipping-option"]');
        shippingOptions.forEach(function(option) {
            option.addEventListener('change', updateShippingCost);
        });

        // Initial update of shipping cost display
        updateShippingCost();
    });
</script>
</body>
</html>
