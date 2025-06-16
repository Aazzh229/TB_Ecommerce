<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['cart_ids'])) {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
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
        .error-box {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
        }
        .error-box h2 {
            color: #ad1457;
            margin-bottom: 15px;
        }
        a {
            color: #ec407a;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <h2>Kamu belum memilih item untuk di-checkout.</h2>
        <p><a href="cart.php">← Kembali ke Keranjang</a></p>
    </div>
</body>
</html>
<?php
    exit;
}

$cart_ids = $_POST['cart_ids'];
$ids_placeholder = implode(',', array_fill(0, count($cart_ids), '?'));

$sql = "
    SELECT ci.id, ci.product_id, ci.quantity, p.price
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    WHERE ci.user_id = ? AND ci.id IN ($ids_placeholder)
";
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat("i", count($cart_ids) + 1), $user_id, ...$cart_ids);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$items = [];

while ($row = $result->fetch_assoc()) {
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    $items[] = $row;
}

$stmt = $conn->prepare("INSERT INTO transactions (user_id, total, transaction_date, status, payment_method, shipping_address, courier)
VALUES (?, ?, NOW(), 'pending', 'COD', 'alamat dummy', 'JNE')");
$stmt->bind_param("id", $user_id, $total);
$stmt->execute();
$transaction_id = $stmt->insert_id;

foreach ($items as $item) {
    $stmt = $conn->prepare("INSERT INTO transaction_details (transaction_id, product_id, quantity, price, subtotal, discount)
    VALUES (?, ?, ?, ?, ?, 0)");
    $subtotal = $item['price'] * $item['quantity'];
    $stmt->bind_param("iiidd", $transaction_id, $item['product_id'], $item['quantity'], $item['price'], $subtotal);
    $stmt->execute();
}

$sql = "UPDATE cart_items SET is_checked_out = 1 WHERE id IN ($ids_placeholder)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat("i", count($cart_ids)), ...$cart_ids);
$stmt->execute();

header("Location: cart.php?checkout=success");
exit;
?>
