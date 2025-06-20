<?php
// ----------- PHP FIXED TRANSAKSI -------------
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$address = $_POST['address'];
$payment_method = $_POST['payment_method'];
$courier = $_POST['courier'];

$query = "SELECT ci.variant_id, ci.quantity, pv.price 
          FROM cart_items ci
          JOIN product_variants pv ON ci.variant_id = pv.id
          WHERE ci.user_id = ? AND ci.is_checked_out = 0";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$items = [];

while ($row = $result->fetch_assoc()) {
    // FIX pembagian harga disini bro:
    $price_fix = $row['price'] / 1000;
    $subtotal = $price_fix * $row['quantity'];
    $total += $subtotal;
    $items[] = [
        'variant_id' => $row['variant_id'],
        'quantity' => $row['quantity'],
        'price' => $price_fix,
        'subtotal' => $subtotal
    ];
}

if (empty($items)) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Checkout</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #fce4ec, #f8bbd0);
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
            }
            .login-container {
                background-color: #fff;
                padding: 30px 40px;
                border-radius: 15px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
                text-align: center;
                width: 100%;
                max-width: 400px;
            }
            .login-container h2 {
                margin-bottom: 25px;
                color: #ad1457;
            }
        </style>
    </head>
    <body>
        <div class='login-container'>
            <h2>Keranjang kamu kosong!</h2>
        </div>
    </body>
    </html>";
    exit;
}

$stmt = $conn->prepare("INSERT INTO transactions (user_id, total, status, payment_method, shipping_address, courier) VALUES (?, ?, 'paid', ?, ?, ?)");
$stmt->bind_param("idsss", $user_id, $total, $payment_method, $address, $courier);
$stmt->execute();
$transaction_id = $stmt->insert_id;

foreach ($items as $item) {
    $stmt = $conn->prepare("INSERT INTO transaction_details (transaction_id, variant_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiidd", $transaction_id, $item['variant_id'], $item['quantity'], $item['price'], $item['subtotal']);
    $stmt->execute();
}

$stmt = $conn->prepare("UPDATE cart_items SET is_checked_out = 1 WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

header("Location: transactions.php");
exit;
?>
