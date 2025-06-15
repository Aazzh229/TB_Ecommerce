<?php
session_start();
include 'db.php';

// Proteksi login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['cart_ids'])) {
    die("Tidak ada item yang dipilih.");
}

$cart_ids = $_POST['cart_ids'];
$ids_placeholder = implode(',', array_fill(0, count($cart_ids), '?'));

// Load data cart yang dipilih user
$sql = "
    SELECT ci.id, p.name, v.variant_name, v.price, ci.quantity, (v.price * ci.quantity) AS subtotal 
    FROM cart_items ci 
    JOIN product_variants v ON ci.variant_id = v.id 
    JOIN products p ON v.product_id = p.id 
    WHERE ci.user_id = ? AND ci.id IN ($ids_placeholder)
";
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat("i", count($cart_ids) + 1), $user_id, ...$cart_ids);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
    $total += $row['subtotal'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Checkout</title>
<style>
    body { font-family: Arial; margin: 20px; }
    .container { max-width: 800px; margin: auto; }
    .card { border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 5px; }
    .title { font-weight: bold; margin-bottom: 10px; }
</style>
</head>
<body>

<div class="container">

<h2>Checkout</h2>

<!-- Alamat & Metode Pembayaran -->
<div class="card">
    <div class="title">Informasi Pengiriman</div>
    <form action="checkout_proses.php" method="POST">

        <?php foreach ($cart_ids as $id): ?>
            <input type="hidden" name="cart_ids[]" value="<?= $id ?>">
        <?php endforeach; ?>

        <label>Alamat Pengiriman:</label><br>
        <textarea name="shipping_address" rows="3" style="width:100%" required></textarea><br><br>

        <label>Metode Pembayaran:</label><br>
        <select name="payment_method" required>
            <option value="COD">COD</option>
            <option value="Transfer Bank">Transfer Bank</option>
            <option value="QRIS">QRIS</option>
            <option value="SPayLater">SPayLater</option>
        </select><br><br>

        <label>Kurir Pengiriman:</label><br>
        <select name="courier" required>
            <option value="JNE">JNE</option>
            <option value="SiCepat">SiCepat</option>
            <option value="J&T">J&T</option>
        </select><br><br>
</div>

<!-- Ringkasan Produk -->
<div class="card">
    <div class="title">Ringkasan Pesanan</div>
    <table border="1" width="100%" cellpadding="8" cellspacing="0">
        <tr>
            <th>Produk</th>
            <th>Varian</th>
            <th>Qty</th>
            <th>Subtotal</th>
        </tr>
        <?php foreach($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= htmlspecialchars($item['variant_name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <h3 style="text-align:right;">Total: Rp <?= number_format($total, 0, ',', '.') ?></h3>
</div>

<button type="submit" style="padding:10px 30px; font-size:16px;">Buat Pesanan</button>
</form>

<a href="cart.php">← Kembali ke Keranjang</a>

</div>

</body>
</html>
