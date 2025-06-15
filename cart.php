<?php
session_start();
include 'db.php';

// Proteksi login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil isi keranjang dari database (JOIN ke variants dan products)
$stmt = $conn->prepare("
    SELECT ci.id, p.name, v.variant_name, v.price, ci.quantity 
    FROM cart_items ci 
    JOIN product_variants v ON ci.variant_id = v.id 
    JOIN products p ON v.product_id = p.id 
    WHERE ci.user_id = ? AND ci.is_checked_out = 0
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja</title>
</head>
<body>

<h2>Keranjang Belanja</h2>

<?php if ($result->num_rows == 0): ?>
    <p>Keranjang kamu kosong.</p>
<?php else: ?>
    <form action="checkout_form.php" method="POST">
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Pilih</th>
            <th>Nama Produk</th>
            <th>Varian</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
        </tr>

        <?php 
        $total = 0;
        while($row = $result->fetch_assoc()): 
            $subtotal = $row['price'] * $row['quantity'];
            $total += $subtotal;
        ?>
        <tr>
            <td>
                <input type="checkbox" name="cart_ids[]" value="<?= $row['id'] ?>">
            </td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['variant_name']) ?></td>
            <td>Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
            <td><?= $row['quantity'] ?></td>
            <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3>Total Keranjang (Semua Item): Rp <?= number_format($total, 0, ',', '.') ?></h3>

    <br>
    <button type="submit">Lanjut Checkout</button>
    </form>
<?php endif; ?>

<br>
<a href="index.php">← Lanjut Belanja</a>

</body>
</html>
