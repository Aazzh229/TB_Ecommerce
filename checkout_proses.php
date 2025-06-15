<?php
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

// Ambil cart
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
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    $items[] = [
        'variant_id' => $row['variant_id'],
        'quantity' => $row['quantity'],
        'price' => $row['price'],
        'subtotal' => $subtotal
    ];
}

if (empty($items)) {
    echo "Cart kosong!";
    exit;
}

// Insert transaksi
$stmt = $conn->prepare("INSERT INTO transactions (user_id, total, status, payment_method, shipping_address, courier) VALUES (?, ?, 'paid', ?, ?, ?)");
$stmt->bind_param("idsss", $user_id, $total, $payment_method, $address, $courier);
$stmt->execute();
$transaction_id = $stmt->insert_id;

// Insert detail
foreach ($items as $item) {
    $stmt = $conn->prepare("INSERT INTO transaction_details (transaction_id, variant_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiidd", $transaction_id, $item['variant_id'], $item['quantity'], $item['price'], $item['subtotal']);
    $stmt->execute();
}

// Update cart menjadi checkout
$stmt = $conn->prepare("UPDATE cart_items SET is_checked_out = 1 WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

header("Location: transactions.php");
exit;
?>
