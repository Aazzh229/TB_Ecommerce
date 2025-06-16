<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['variant_ids'])) {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
</head>
<body>
    <h2>Kamu belum memilih item untuk di-checkout.</h2>
    <p><a href="cart.php">← Kembali ke Keranjang</a></p>
</body>
</html>
<?php
    exit;
}

$variant_ids = $_POST['variant_ids'];
$ids_placeholder = implode(',', array_fill(0, count($variant_ids), '?'));

$sql = "
    SELECT ci.variant_id, ci.quantity, v.price, v.product_id
    FROM cart_items ci
    JOIN product_variants v ON ci.variant_id = v.id
    WHERE ci.user_id = ? AND ci.variant_id IN ($ids_placeholder)
";
$stmt = $conn->prepare($sql);
$param_types = str_repeat("i", count($variant_ids)) . "i";
$params = array_merge($variant_ids, [$user_id]);
$stmt->bind_param($param_types, ...$params);
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
    $subtotal = $item['price'] * $item['quantity'];
    $stmt = $conn->prepare("INSERT INTO transaction_details (transaction_id, product_id, variant_id, quantity, price, subtotal, discount)
    VALUES (?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("iiiidd", $transaction_id, $item['product_id'], $item['variant_id'], $item['quantity'], $item['price'], $subtotal);
    $stmt->execute();
}

$sql = "UPDATE cart_items SET is_checked_out = 1 WHERE variant_id IN ($ids_placeholder) AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($param_types, ...$params);
$stmt->execute();

header("Location: cart.php?checkout=success");
exit;
?>
