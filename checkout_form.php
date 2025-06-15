<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil isi keranjang user
$query = "SELECT ci.variant_id, ci.quantity, pv.price, pv.variant_name, p.name as product_name, p.image
          FROM cart_items ci
          JOIN product_variants pv ON ci.variant_id = pv.id
          JOIN products p ON pv.product_id = p.id
          WHERE ci.user_id = $user_id AND ci.is_checked_out = 0";

$result = $conn->query($query);

$items = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    $items[] = [
        'product_name' => $row['product_name'],
        'variant_name' => $row['variant_name'],
        'quantity' => $row['quantity'],
        'price' => $row['price'],
        'subtotal' => $subtotal,
        'image' => $row['image']
    ];
}

// Biaya tetap contoh
$shipping_cost = 10000; 
$service_fee = 2000;
$discount_shipping = -10000; 
$grand_total = $total + $shipping_cost + $service_fee + $discount_shipping;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <style>
        body { font-family: Arial; margin: 30px; }
        .checkout-summary { border: 1px solid #ddd; padding: 20px; border-radius: 10px; width: 500px; }
        .checkout-summary img { width: 80px; vertical-align: middle; }
        .summary-row { margin: 8px 0; }
        .total { font-weight: bold; font-size: 18px; color: red; }
    </style>
</head>
<body>

<h1>Checkout</h1>

<div class="checkout-summary">
    <?php foreach ($items as $item): ?>
        <div class="summary-row">
            <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="">
            <?= htmlspecialchars($item['product_name']) ?> - <?= htmlspecialchars($item['variant_name']) ?> <br>
            Qty: <?= $item['quantity'] ?> x Rp<?= number_format($item['price'], 0, ',', '.') ?>
        </div>
    <?php endforeach; ?>
    <hr>
    <div class="summary-row">Subtotal Produk: Rp<?= number_format($total, 0, ',', '.') ?></div>
    <div class="summary-row">Subtotal Pengiriman: Rp<?= number_format($shipping_cost, 0, ',', '.') ?></div>
    <div class="summary-row">Biaya Layanan: Rp<?= number_format($service_fee, 0, ',', '.') ?></div>
    <div class="summary-row">Diskon Pengiriman: Rp<?= number_format($discount_shipping, 0, ',', '.') ?></div>
    <hr>
    <div class="summary-row total">Total Pembayaran: Rp<?= number_format($grand_total, 0, ',', '.') ?></div>
</div>

<br><br>

<form action="checkout_proses.php" method="POST">
    <input type="hidden" name="total" value="<?= $grand_total ?>">
    <label>Alamat Pengiriman:</label><br>
    <textarea name="address" required></textarea><br><br>

    <label>Metode Pembayaran:</label>
    <select name="payment_method">
        <option value="COD">COD</option>
        <option value="Transfer Bank">Transfer Bank</option>
    </select><br><br>

    <label>Kurir:</label>
    <select name="courier">
        <option value="JNE">JNE</option>
        <option value="SiCepat">SiCepat</option>
        <option value="AnterAja">AnterAja</option>
    </select><br><br>

    <button type="submit">Buat Pesanan</button>
</form>

</body>
</html>
