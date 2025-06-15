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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 40px 50px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 700px;
        }
        h1 {
            text-align: center;
            color: #ad1457;
            margin-bottom: 30px;
        }
        .checkout-summary {
            margin-bottom: 30px;
        }
        .checkout-summary img {
            width: 80px;
            vertical-align: middle;
            border-radius: 8px;
        }
        .summary-row {
            margin: 8px 0;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 8px;
        }
        .total {
            font-weight: bold;
            font-size: 18px;
            color: #ec407a;
            margin-top: 15px;
        }
        label {
            font-weight: bold;
            margin-top: 15px;
            display: block;
        }
        textarea, select {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #f8bbd0;
            border-radius: 8px;
            font-size: 16px;
        }
        button {
            background-color: #ec407a;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        button:hover { background-color: #d81b60; }
    </style>
</head>
<body>

<div class="container">
    <h1>Checkout</h1>

    <div class="checkout-summary">
        <?php foreach ($items as $item): ?>
            <div class="summary-row">
                <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="">
                <?= htmlspecialchars($item['product_name']) ?> - <?= htmlspecialchars($item['variant_name']) ?> <br>
                Qty: <?= $item['quantity'] ?> x Rp<?= number_format($item['price'], 0, ',', '.') ?>
            </div>
        <?php endforeach; ?>
        <div class="summary-row">Subtotal Produk: Rp<?= number_format($total, 0, ',', '.') ?></div>
        <div class="summary-row">Subtotal Pengiriman: Rp<?= number_format($shipping_cost, 0, ',', '.') ?></div>
        <div class="summary-row">Biaya Layanan: Rp<?= number_format($service_fee, 0, ',', '.') ?></div>
        <div class="summary-row">Diskon Pengiriman: Rp<?= number_format($discount_shipping, 0, ',', '.') ?></div>
        <div class="total">Total Pembayaran: Rp<?= number_format($grand_total, 0, ',', '.') ?></div>
    </div>

    <form action="checkout_proses.php" method="POST">
        <input type="hidden" name="total" value="<?= $grand_total ?>">

        <label>Alamat Pengiriman:</label>
        <textarea name="address" required></textarea>

        <label>Metode Pembayaran:</label>
        <select name="payment_method">
            <option value="COD">COD</option>
            <option value="Transfer Bank">Transfer Bank</option>
        </select>

        <label>Kurir:</label>
        <select name="courier">
            <option value="JNE">JNE</option>
            <option value="SiCepat">SiCepat</option>
            <option value="AnterAja">AnterAja</option>
        </select>

        <button type="submit">Buat Pesanan</button>
    </form>
</div>

</body>
</html>
